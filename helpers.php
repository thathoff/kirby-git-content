<?php

class KirbyGitHelper
{
    private $repo;
    private $repoPath;
    private $branch;
    private $pullOnChange;
    private $pushOnChange;
    private $commitOnChange;
    private $gitBin;
    private $windowsMode;

    public function __construct($repoPath = __DIR__ . "/../../../content")
    {
        $this->repoPath = $repoPath;
    }

    private function initRepo()
    {
        if (!class_exists("Git")) {
            require('Git.php/Git.php');
        }

        $this->branch = c::get('gcapc-branch', 'master');
        $this->pullOnChange = c::get('gcapc-pull', false);
        $this->pushOnChange = c::get('gcapc-push', false);
        $this->commitOnChange = c::get('gcapc-commit', false);
        $this->gitBin = c::get('gcapc-gitBin', '');
        $this->windowsMode = c::get('gcapc-windowsMode', false);

        if ($this->windowsMode) {
            Git::windows_mode();
        }
        if ($this->gitBin) {
            Git::set_bin($this->gitBin);
        }

        $this->repo = Git::open($this->repoPath);

        if (!$this->repo->test_git()) {
            trigger_error('git could not be found or is not working properly. ' . Git::get_bin());
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
        $this->getRepo()->checkout($this->branch);

        if ($this->pullOnChange) {
            $this->pull();
        }
        if ($this->commitOnChange) {
            $this->commit($commitMessage . "\n\nby " . site()->user());
        }
        if ($this->pushOnChange) {
            $this->push();
        }
    }
}
