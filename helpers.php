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

    public function __construct($repoPath = false)
    {
        $this->repoPath = $repoPath ? $repoPath : c::get('gcapc-path', kirby()->roots()->content());
        $this->branch = c::get('gcapc-branch', 'master');
    }

    private function initRepo()
    {
        if ($this->repo) {
            return true;
        }

        if (!class_exists("Git")) {
            if (file_exists(__DIR__ . DS . 'Git.php' . DS. 'Git.php')) {
                require __DIR__ . DS . 'Git.php' . DS. 'Git.php';
            } else {
                require kirby()->roots()->index() .
                DS . 'vendor' . DS . 'pascalmh' . DS . 'git.php' . DS . 'Git.php';
            }
        }

        if (!class_exists("Git")) {
            die('Git class not found. Is the Git.php submodule installed?');
        }

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
            trigger_error('Unable to update git: ' . $exception->getMessage());
        }
    }
}
