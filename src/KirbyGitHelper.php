<?php

namespace Thathoff\GitContent;

use CzProject\GitPhp\Git;
use CzProject\GitPhp\Runners\CliRunner;
use CzProject\GitPhp\GitException;
use CzProject\GitPhp\GitRepository;
use DateTime;
use Exception;

class KirbyGitHelper
{
    private $kirby;
    private $repo;
    private $repoPath;
    private $commitMessageTemplate;
    private $pullOnChange;
    private $pushOnChange;
    private $commitOnChange;
    private $gitBin;
    private $git;

    public function __construct($repoPath = false)
    {
        $this->kirby = kirby();
        $this->repoPath = $repoPath ? $repoPath : option('thathoff.git-content.path', $this->kirby->root("content"));
        $this->commitMessageTemplate = option('thathoff.git-content.commitMessage', ':action:(:item:): :url:');
    }

    private function initRepo()
    {
        if ($this->repo) {
            return true;
        }

        if (!class_exists("CzProject\GitPhp\Git")) {
            throw new Exception('Git class not found. Make sure you run composer install inside this plugins directory');
        }

        $this->pullOnChange = option('thathoff.git-content.pull', false);
        $this->pushOnChange = option('thathoff.git-content.push', false);
        $this->commitOnChange = option('thathoff.git-content.commit', true);
        $this->gitBin = option('thathoff.git-content.gitBin', '');
        if (!$this->gitBin) {
            $this->gitBin = 'git';
        }
        // force English locale for predictable command outputs
        $runner = new CliRunner('LC_ALL=C ' . $this->gitBin);
        $this->git = new Git($runner);
        $this->repo = $this->git->open($this->repoPath);
    }

    public function log(int $limit = 10)
    {
        $separator = "\|";
        $format = implode($separator, ["%H", "%s", "%an", "%ae", "%cI"]);

		try {
			$log = $this->getRepo()->execute('log', '--pretty=format:' . $format, '--max-count=' . $limit);
		} catch (GitException $e) {
			$this->catchGitException($e);
		}

        $log = array_map(
            function ($line) use ($separator) {
                $entry = explode($separator, $line);

                return [
                    'hash' => $entry[0],
                    'message' => $entry[1],
                    'author' => $entry[2],
                    'email' => $entry[3],
                    'date' => DateTime::createFromFormat(DateTime::ISO8601, $entry[4]),
                ];
            },
            $log
        );

        return $log;
    }

    private function getRepo(): GitRepository
    {
        if ($this->repo == null) {
            $this->initRepo();
        }

        return $this->repo;
    }

    public function commit($commitMessage, $paths, $author = null)
    {
        try {
            if ($paths) {
                $uniquePaths = array_unique($paths);
                $this->getRepo()->execute('add', '--', ...$uniquePaths);
            }

            $params = [];
            if ($author) {
                $params[] = "--author=" . $author;
            }

            $this->getRepo()->commit($commitMessage, $params);
        } catch (GitException $e) {
			$this->catchGitException($e);
        }
    }

	private function catchGitException(GitException $e) {
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
			$phpUserName = $phpUser['name'];

			throw new Exception('The content repository is not owned by the user running the PHP process. ' .
				'Please change the owner to ' . $phpUserName . ', eg. by running `chown -R ' . $phpUserName . ' "' . $this->repoPath . '"`.'
			);
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

    public function push()
    {
        $this->getRepo()->push();
    }

    public function getCurrentBranch()
    {
        return $this->getRepo()->getCurrentBranchName();
    }

    public function pull()
    {
        $this->getRepo()->pull(null, ['--no-rebase']);
    }

    public function fetch()
    {
        $this->getRepo()->fetch();
    }

    public function reset()
    {
        $this->getRepo()->execute('reset', '--hard', 'HEAD');
    }

    public function resetToOrigin()
    {
        $remoteBranch = $this->getRepo()->execute('rev-parse', '--abbrev-ref', '@{u}');
        $remoteBranch = $remoteBranch[0] ?? null;
        if (empty($remoteBranch)) {
            throw new Exception('No remote branch found. Please add a remote branch first.');
        }
        $this->getRepo()->execute('reset', '--hard', $remoteBranch);
    }

    public function removeIndexLock()
    {
        if (!$this->hasIndexLock()) {
            return;
        }

        unlink($this->repoPath . '/.git/index.lock');
    }

    public function hasIndexLock()
    {
        return file_exists($this->repoPath . '/.git/index.lock');
    }

    public function clean()
    {
        $this->getRepo()->execute('clean', '-fd');
    }

    public function addAll()
    {
        $this->getRepo()->addAllChanges();
    }

    public function checkout(string $branch)
    {
        $this->getRepo()->checkout($branch);
    }

    public function getBranches()
    {
        return $this->getRepo()->getLocalBranches();
    }

    public function createBranch(string $branch)
    {
        return $this->getRepo()->createBranch($branch, true);
    }

    public function status() {
        /* git returns a two character code for every entry in 'git status --porcelain'. these codes are shown below, split in index and worktree codes.
           the first code character always refers to the index state of the file, the second for the worktree
           for more info refer to https://git-scm.com/docs/git-status#_short_format
        */
        $upstreamResponse = $this->getRepo()->execute('status',  '--porcelain=2', '--branch');
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
                $ahead = substr($ahead[0], 1);
                $behind = substr($behind[0], 1);

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

    public function kirbyChange($action, $item, $paths, $url = '')
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

            error_log('Unable to update git: ' . $message, E_USER_ERROR);
        }
    }

    private function commitMessage($action, $item, $url)
    {
        return strtr($this->commitMessageTemplate, [
            ':action:' => $action,
            ':item:' => $item,
            ':url:' => $url,
        ]);
    }
}
