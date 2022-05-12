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

        $runner = $this->gitBin ? new CliRunner($this->gitBin) : new CliRunner();
        $this->git = new Git($runner);
        $this->repo = $this->git->open($this->repoPath);
    }

    public function log(int $limit = 10)
    {
        $log = $this->getRepo()->execute('log', '--pretty=format:%H|%s|%an|%ae|%cI', '--max-count=' . $limit);


        $log = array_map(
            function ($line) {
                $entry = explode("|", $line);

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

    public function commit($commitMessage, $author = null)
    {
        $this->getRepo()->addAllChanges();

        $params = [];
        if ($author) {
            $params[] = "--author=" . $author;
        }

        $this->getRepo()->commit($commitMessage, $params);
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

    public function kirbyChange($action, $item, $url = '')
    {
        try {
            $this->initRepo();

            if ($this->pullOnChange) {
                $this->pull();
            }

            if ($this->commitOnChange) {
                $user = $this->kirby->user();

                $author = null;
                if ($user) {
                    $author = $user->name()->or($user->email()) . " <" . $user->email() . ">";
                }

                $this->commit($this->commitMessage($action, $item, $url), $author);
            }

            if ($this->pushOnChange) {
                $this->push();
            }
        } catch (Exception $exception) {
            $message = $exception->getMessage();

            // enrich message with more info if we got a GitException
            if ($exception instanceof GitException) {
                $runnerResult = $exception->getRunnerResult();
                $message .= "\n\n" . implode("\n", $runnerResult->getErrorOutput());
            }

            // only show exceptions when explicitly enabled
            if (option('thathoff.git-content.displayErrors', false)) {
                throw new Exception('Unable to update git: ' . $message);
            }


            // still log for debug
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
