<?php

namespace Blanko\Kirby\GCAPC;

use Git;
use Exception;

class KirbyGitHelper
{
    private $kirby;
    private $repo;
    private $repoPath;
    private $branch;
    private $pullOnChange;
    private $pushOnChange;
    private $commitOnChange;
    private $gitBin;
    private $windowsMode;

    public function __construct($repoPath = false)
    {
        $this->kirby = kirby();
        $this->repoPath = $repoPath ? $repoPath : option('blankogmbh.gcapc.path', $this->kirby->root("content"));

        $this->branch = option('blankogmbh.gcapc.branch', '');
    }

    private function initRepo()
    {
        if ($this->repo) {
            return true;
        }

        if (!class_exists("Git")) {
            throw new Exception('Git class not found. Make sure you run composer install inside this plugins directory');
        }

        $this->pullOnChange = option('blankogmbh.gcapc.pull', false);
        $this->pushOnChange = option('blankogmbh.gcapc.push', false);
        $this->commitOnChange = option('blankogmbh.gcapc.commit', true);
        $this->gitBin = option('blankogmbh.gcapc.gitBin', '');
        $this->windowsMode = option('blankogmbh.gcapc.windowsMode', false);

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
        $this->getRepo()->push('origin', $branch);
    }

    public function pull($branch = false)
    {
        $branch = $branch ? $branch : $this->branch;
        $this->getRepo()->pull('origin', $branch);
    }

    public function kirbyChange($commitMessage)
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

                $this->commit($commitMessage, $author);
            }
            if ($this->pushOnChange) {
                $this->push();
            }
        } catch(Exception $exception) {
            // only show exceptions when explicitly enabled
            if (option('blankogmbh.gcapc.displayErrors', false)) {
                throw new Exception('Unable to update git: ' . $exception->getMessage());
            }

            // still log for debug
            error_log('Unable to update git: ' . $exception->getMessage(), E_USER_ERROR);
        }
    }
}
