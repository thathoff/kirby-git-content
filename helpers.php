<?php

class KirbyGitHelper
{

    const DEFAULT_MESSAGES = array(
        'page.create'  => 'create(page): %s',
        'page.update'  => 'update(page): %s',
        'page.delete'  => 'delete(page): %s',
        'page.sort'    => 'sort(page): %s',
        'page.hide'    => 'hide(page): %s',
        'page.move'    => 'move(page): %s',

        'file.upload'  => 'create(page): %s',
        'file.replace' => 'update(page): %s',
        'file.rename'  => 'delete(page): %s',
        'file.update'  => 'sort(page): %s',
        'file.sort'    => 'hide(page): %s',
        'file.delete'  => 'move(page): %s',

        'user.suffix'  => "\n\nby %s", // appended to the commit message
    );

    private $repo;
    private $repoPath;
    private $branch;
    private $pullOnChange;
    private $pushOnChange;
    private $commitOnChange;
    private $gitBin;
    private $windowsMode;
    private $commitMessages;

    public function __construct($repoPath = false)
    {
        $this->repoPath = $repoPath ? $repoPath : c::get('gcapc-path', kirby()->roots()->content());
        $this->branch = c::get('gcapc-branch', '');
        $this->messages = c::get('gcapc-messages', self::DEFAULT_MESSAGES);
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
            trigger_error('git could not be found or is not working properly: ' . Git::get_bin());
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
        $this->getRepo()->run("push origin $branch"); // push('origin', $branch) inserts a spurious --tags option
    }

    public function pull($branch = false)
    {
        $branch = $branch ? $branch : $this->branch;
        $this->getRepo()->pull('origin', $branch);
    }

    public function kirbyChangePage($key, $page) {
        $commitMessage = $this->getMessage($key, $page->uri());

        $this->kirbyChange($commitMessage);
    }

    public function kirbyChangeFile($key, $file) {
        $commitMessage = $this->getMessage($key, $file->page()->uri() . '/' . $file->filename());

        $this->kirbyChange($commitMessage);
    }

    private function kirbyChange($commitMessage)
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
                $this->commit($commitMessage . $this->getMessage('user.suffix', site()->user()));
            }
            if ($this->pushOnChange) {
                $this->push();
            }
        } catch(Exception $exception) {
            trigger_error('Unable to update git: ' . $exception->getMessage());
        }
    }

    private function getMessage($key, ...$params)
    {
        $message = isset($this->messages[$key]) ? $this->messages[$key] : self::DEFAULT_MESSAGES[$key];

        return sprintf($message, ...$params);
    }
}
