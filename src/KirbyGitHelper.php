<?php

namespace Thathoff\GitContent;

use Coyl\Git\Git;
use Exception;

class KirbyGitHelper
{
    private $kirby;
    private $repo;
    private $repoPath;
    private $branch;
    private $commitMessageTemplate;
    private $pullOnChange;
    private $pushOnChange;
    private $commitOnChange;
    private $gitBin;
    private $windowsMode;

    public function __construct($repoPath = false)
    {
        $this->kirby = kirby();
        $this->repoPath = $repoPath ? $repoPath : option('thathoff.git-content.path', $this->kirby->root("content"));

        $this->branch = option('thathoff.git-content.branch', '');
        $this->commitMessageTemplate = option('thathoff.git-content.commitMessage', ':action:(:item:): :url:');
    }

    private function initRepo()
    {
        if ($this->repo) {
            return true;
        }

        if (!class_exists("Coyl\Git\Git")) {
            throw new Exception('Git class not found. Make sure you run composer install inside this plugins directory');
        }

        $this->pullOnChange = option('thathoff.git-content.pull', false);
        $this->pushOnChange = option('thathoff.git-content.push', false);
        $this->commitOnChange = option('thathoff.git-content.commit', true);
        $this->gitBin = option('thathoff.git-content.gitBin', '');
        $this->windowsMode = option('thathoff.git-content.windowsMode', false);

        if ($this->windowsMode) {
            Git::windows_mode();
        }
        if ($this->gitBin) {
            Git::set_bin($this->gitBin);
        }

        $this->repo = Git::open($this->repoPath);

        if (!$this->repo->test_git()) {
            throw new Exception('git could not be found or is not working properly. ' . Git::get_bin());
        }
    }

    private function getRepo()
    {
        if ($this->repo == null) {
            $this->initRepo();
        }

        return $this->repo;
    }

    public function commit($commitMessage, $author = null)
    {
        $this->getRepo()->add('-A');

        $command = "commit -m " . escapeshellarg($commitMessage);

        if ($author) {
            $command .= " --author=" . escapeshellarg($author);
        }

        // we use the raw run command here to optionally supply the git author
        // IMPORTANT: make sure all arguments are escaped through escapeshellarg();
        $this->getRepo()->run($command);
    }

    public function push($branch = false)
    {
        $branch = $branch ? $branch : $this->branch;

        // if branch is still empty we use the active branch
        // because otherwise pushes fail silently in some cases
        if (!$branch) {
            $branch = $this->getRepo()->getActiveBranch();
        }

        $this->getRepo()->push('origin', $branch);
    }

    public function pull($branch = false)
    {
        $branch = $branch ? $branch : $this->branch;
        $this->getRepo()->pull('origin', $branch);
    }

    public function kirbyChange($action, $item, $url = '')
    {
        try {
            $this->initRepo();

            if ($this->branch) {
                $this->getRepo()->checkout($this->branch);
            }

            if ($this->pullOnChange) {
                $this->pull();
            }
            if ($this->commitOnChange) {
                $user = $this->kirby->user();

                $author = null;
                if ($user) {
                    $author = $user->name() . " <" . $user->email() . ">";
                }

                $this->commit($this->commitMessage($action, $item, $url), $author);
            }
            if ($this->pushOnChange) {
                $this->push();
            }
        } catch(Exception $exception) {
            // only show exceptions when explicitly enabled
            if (option('thathoff.git-content.displayErrors', false)) {
                throw new Exception('Unable to update git: ' . $exception->getMessage());
            }

            // still log for debug
            error_log('Unable to update git: ' . $exception->getMessage(), E_USER_ERROR);
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
