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
        $this->commitOnChange = option('blankogmbh.gcapc.commit', false);
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

    public function commit($commitMessage)
    {
        $this->getRepo()->add('-A');
        $this->getRepo()->commit($commitMessage);
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
                $this->commit($commitMessage . "\n\nby " . site()->user());
            }
            if ($this->pushOnChange) {
                $this->push();
            }
        } catch(Exception $exception) {
            throw new Exception('Unable to update git: ' . $exception->getMessage());
        }
    }
}
