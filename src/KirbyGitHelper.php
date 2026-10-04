<?php

namespace Thathoff\GitContent;

use CzProject\GitPhp\Git;
use CzProject\GitPhp\Runners\CliRunner;
use CzProject\GitPhp\GitException;
use CzProject\GitPhp\GitRepository;
use DateTime;
use Exception;
use Kirby\Cms\App;

class KirbyGitHelper
{
    private App $kirby;
    private ?GitRepository $repo = null;
    private string $repoPath;
    private string $commitMessageTemplate;
    private bool $pullOnChange = false;
    private bool $pushOnChange = false;
    private bool $commitOnChange = true;
    private string $gitBin = 'git';
    private Git $git;

    public function __construct(?string $repoPath = null)
    {
        $this->kirby = kirby();
        $this->repoPath = $repoPath ? $repoPath : option('thathoff.git-content.path', $this->kirby->root("content"));
        $this->commitMessageTemplate = option('thathoff.git-content.commitMessage', ':action:(:item:): :url:');
    }

    private function initRepo(): GitRepository
    {
        if ($this->repo) {
            return $this->repo;
        }

        if (!class_exists("CzProject\GitPhp\Git")) {
            throw new Exception('Git class not found. Make sure you run composer install inside this plugins directory');
        }

        $this->pullOnChange = (bool) option('thathoff.git-content.pull', false);
        $this->pushOnChange = (bool) option('thathoff.git-content.push', false);
        $this->commitOnChange = (bool) option('thathoff.git-content.commit', true);
        $this->gitBin = (string) option('thathoff.git-content.gitBin', '') ?: 'git';
        // force English locale for predictable command outputs
        $runner = new CliRunner('LC_ALL=C ' . $this->gitBin);
        $this->git = new Git($runner);
        $this->repo = $this->git->open($this->repoPath);

        return $this->repo;
    }

    /**
     * @return array<int, array{hash: string, message: string, author: string, email: string, date: DateTime|null}>
     */
    public function log(int $limit = 10): array
    {
        $separator = "\|";
        $format = implode($separator, ["%H", "%s", "%an", "%ae", "%cI"]);
        $log = [];

        try {
            $log = $this->getRepo()->execute('log', '--pretty=format:' . $format, '--max-count=' . $limit);
        } catch (GitException $e) {
            $this->catchGitException($e);
        }

        $log = array_map(
            function (string $line) use ($separator): array {
                $entry = explode($separator, $line);

                return [
                    'hash' => $entry[0],
                    'message' => $entry[1],
                    'author' => $entry[2],
                    'email' => $entry[3],
                    'date' => DateTime::createFromFormat(DateTime::ISO8601, $entry[4]) ?: null,
                ];
            },
            $log
        );

        return $log;
    }

    private function getRepo(): GitRepository
    {
        return $this->initRepo();
    }

    /**
     * @param string[]|null $paths
     */
    public function commit(string $commitMessage, ?array $paths, ?string $author = null): void
    {
        try {
            if ($paths) {
                $uniquePaths = array_unique($paths);
                $this->getRepo()->execute('add', '--', ...$uniquePaths);
            }

            $args = ['commit', '-m', $commitMessage];

            if ($author) {
                $args[] = "--author=" . $author;
            }

            // restrict the commit to the given paths, so unrelated staged
            // changes (eg. from another file selection) are left untouched
            if ($paths) {
                $args[] = '--';
                $args = array_merge($args, array_unique($paths));
            }

            $this->getRepo()->execute(...$args);
        } catch (GitException $e) {
            $this->catchGitException($e);
        }
    }

    /**
     * @param string[] $files
     */
    public function revertFiles(array $files): void
    {
        if (empty($files)) {
            return;
        }

        $trackedFiles = [];
        $untrackedFiles = [];

        foreach ($this->status()['files'] as $file) {
            if (!in_array($file['filename'], $files, true)) {
                continue;
            }

            if (strpos($file['code'], '?') !== false) {
                $untrackedFiles[] = $file['filename'];
            } else {
                $trackedFiles[] = $file['filename'];
            }
        }

        try {
            if ($trackedFiles) {
                // unstage first, so files that were only `git add`ed (eg. new files) are included in the checkout below
                $this->getRepo()->execute('reset', 'HEAD', '--', ...$trackedFiles);
                $this->getRepo()->execute('checkout', 'HEAD', '--', ...$trackedFiles);
            }

            if ($untrackedFiles) {
                $this->getRepo()->execute('clean', '-fd', '--', ...$untrackedFiles);
            }
        } catch (GitException $e) {
            $this->catchGitException($e);
        }
    }

    /**
     * @param string[] $files
     */
    public function commitFiles(?string $title, ?string $description, array $files): void
    {
        if (!$title) {
            throw new Exception('A commit title is required.');
        }

        $message = $title;
        if ($description) {
            $message .= "\n\n" . $description;
        }

        $this->commit($message, $files ?: null, $this->getAuthorString());
    }

    private function catchGitException(GitException $e): void
    {
        // Sometimes a change results in multiple hooks being fired (for example status change). This causes a race condition:
        // As the file change can only be committed once, latter hooks will fail when calling either 'git add' or 'git commit'.
        // The files in question have actually been committed already in an earlier hook call and therefore we may ignore the errors.
        // We don’t run git status in front because that is much slower in large repositories.
        // Refer to #84

        // We concat the actual git error message, the error output and regular output together to then search for "exclusion strings".
        // For some reason, the output is sometimes obtainable using getErrorOutput() and sometimes using getOutput().
        $errorMessage = $e->getMessage();
        if ($runnerResult = $e->getRunnerResult()) {
            $errorMessage .= "\n\n" . implode("\n", $runnerResult->getErrorOutput()) . "\n\n" . implode("\n", $runnerResult->getOutput());
        }

        // make the dubious ownership error more user friendly
        if (strpos($errorMessage, 'dubious ownership') !== false) {
            $phpUser = posix_getpwuid(posix_geteuid());
            $phpUserName = $phpUser !== false ? $phpUser['name'] : (string) posix_geteuid();

            throw new Exception('The content repository is not owned by the user running the PHP process. ' .
                'Please change the owner to ' . $phpUserName . ', eg. by running `chown -R ' . $phpUserName . ' "' . $this->repoPath . '"`.');
        }

        $ignoredErrors = [
            'nothing to commit',
            'did not match any files'
        ];

        // if the error message is not in the ignored errors, throw the exception
        // check if one of the ignored errors is in the error message
        foreach ($ignoredErrors as $ignoredError) {
            if (strpos($errorMessage, $ignoredError) !== false) {
                return;
            }
        }

        // otherwise throw the exception
        throw $e;
    }

    public function push(): void
    {
        $this->getRepo()->push();
    }

    public function getCurrentBranch(): string
    {
        return $this->getRepo()->getCurrentBranchName();
    }

    public function pull(): void
    {
        $this->getRepo()->pull(null, ['--no-rebase']);
    }

    public function fetch(): void
    {
        $this->getRepo()->fetch();
    }

    public function reset(): void
    {
        $this->getRepo()->execute('reset', '--hard', 'HEAD');
    }

    public function resetToOrigin(): void
    {
        $remoteBranch = $this->getRepo()->execute('rev-parse', '--abbrev-ref', '@{u}');
        $remoteBranch = $remoteBranch[0] ?? null;
        if (empty($remoteBranch)) {
            throw new Exception('No remote branch found. Please add a remote branch first.');
        }
        $this->getRepo()->execute('reset', '--hard', $remoteBranch);
    }

    public function removeIndexLock(): void
    {
        if (!$this->hasIndexLock()) {
            return;
        }

        unlink($this->repoPath . '/.git/index.lock');
    }

    public function hasIndexLock(): bool
    {
        return file_exists($this->repoPath . '/.git/index.lock');
    }

    public function clean(): void
    {
        $this->getRepo()->execute('clean', '-fd');
    }

    public function addAll(): void
    {
        $this->getRepo()->addAllChanges();
    }

    public function checkout(string $branch): void
    {
        $this->getRepo()->checkout($branch);
    }

    /**
     * @return string[]|null
     */
    public function getBranches(): ?array
    {
        return $this->getRepo()->getLocalBranches();
    }

    public function createBranch(string $branch): GitRepository
    {
        return $this->getRepo()->createBranch($branch, true);
    }

    /**
     * @return array{hasRemote: bool, diffFromOrigin: int|null, files: array<int, array{code: string, filename: string}>}
     */
    public function status(): array
    {
        /* git returns a two character code for every entry in 'git status --porcelain'. these codes are shown below, split in index and worktree codes.
           the first code character always refers to the index state of the file, the second for the worktree
           for more info refer to https://git-scm.com/docs/git-status#_short_format
        */
        $upstreamResponse = $this->getRepo()->execute('status', '--porcelain=2', '--branch');
        $filesResponse = $this->getRepo()->execute('status', '--porcelain');

        // REMOTE INFORMATION --------------
        // the first few lines (length depending on whether remote branch is available) are branch information.
        // line about ahead/behind commits looks as follows:
        // # branch.ab +0 -0
        $hasRemote = false;
        $diff = null;
        foreach ($upstreamResponse as $key => $line) {
            if (!empty($line) && strpos($line, 'branch.ab') !== false) {
                $hasRemote = true;

                preg_match('/\+\d+/', $line, $ahead);
                preg_match('/\-\d+/', $line, $behind);
                $ahead = (int) substr($ahead[0] ?? '+0', 1);
                $behind = (int) substr($behind[0] ?? '-0', 1);

                $diff = $ahead - $behind;
                break;
            }
        }

        // CHANGED FILES -------------------
        // one line per file. line looks like this:
        // XY filename.txt
        $files = [];
        foreach ($filesResponse as $key => $file) {
            $files[$key] = [
                'code' => substr($file, 0, 2),
                'filename' => substr($file, 3)
            ];
        }

        return [
            'hasRemote' => $hasRemote,
            'diffFromOrigin' => $diff,
            'files' => $files,
        ];
    }

    public function getAuthorString(): ?string
    {
        if (!$user = $this->kirby->user()) {
            return null;
        }

        return $user->name()->or($user->email()) . " <" . $user->email() . ">";
    }

    /**
     * @param string[] $paths
     */
    public function kirbyChange(string $action, string $item, array $paths, string $url = ''): void
    {
        try {
            $this->initRepo();

            if ($this->pullOnChange) {
                $this->pull();
            }

            if ($this->commitOnChange) {
                $author = $this->getAuthorString();

                $this->commit($this->commitMessage($action, $item, $url), $paths, $author);
            }

            if ($this->pushOnChange) {
                $this->push();
            }
        } catch (Exception $exception) {
            $message = $exception->getMessage();

            // enrich message with more info if we got a GitException
            if ($exception instanceof GitException) {
                if ($runnerResult = $exception->getRunnerResult()) {
                    $message .= "\n\n" . implode("\n", $runnerResult->getErrorOutput());
                }
            }

            // show exceptions by default
            if (option('thathoff.git-content.displayErrors', true)) {
                throw new Exception('Unable to update git: ' . $message);
            }

            error_log('Unable to update git: ' . $message);
        }
    }

    private function commitMessage(string $action, string $item, string $url): string
    {
        return strtr($this->commitMessageTemplate, [
            ':action:' => $action,
            ':item:' => $item,
            ':url:' => $url,
        ]);
    }
}
