<?php

namespace Thathoff\GitContent;

use Exception;
use Kirby\Http\Header;
use CzProject\GitPhp\GitException;

class KirbyGit
{
    /**
     * @var KirbyGitHelper
     */
    private $gitHelper = null;

    public function __construct()
    {
        $this->gitHelper = new KirbyGitHelper();
    }

    public function getApiRoutes()
    {
        // save instance in variable because $this is not available in closures
        $kirbyGit = $this;

        return [
            [
                'pattern' => 'git-content/push',
                'method'  => 'POST',
                'action'  => function () use ($kirbyGit) {
                    return $kirbyGit->httpGitHelperAction('push', "successfully pushed the content folder");
                },
            ],
            [
                'pattern' => 'git-content/pull',
                'method'  => 'POST',
                'action'  => function () use ($kirbyGit) {
                    return $kirbyGit->httpGitHelperAction('pull', "successfully pulled the content folder");
                },
            ]
        ];
    }

    public function getRoutes()
    {
        if (!option("thathoff.git-content.cronHooksEnabled", true)) {
            return [];
        }

        $route = [];
        $helper = $this;

        $route['pattern'] = 'git-content/(:any)';
        $route['method'] = 'GET|POST';
        $route['action'] = function ($gitCommand) use ($helper) {
            // check to see if a secret is set, and if it is, verify it
            $secret = option('thathoff.git-content.cronHooksSecret', '');
            if ($secret !== '') {
                $passedSecret = kirby()->request()->get('secret', '');
                if ($passedSecret !== $secret) {
                    return [
                        'status' => 'forbidden',
                        'message' => 'Invalid secret passed',
                    ];
                }
            }

            switch ($gitCommand) {
                case "push":
                    return $helper->httpGitHelperAction('push', "successfully pushed the content folder");
                    break;
                case "pull":
                    return $helper->httpGitHelperAction('pull', "successfully pulled the content folder");
                    break;
            }

            Header::missing();
            return [
                "status" => "error",
                "data" => "Unknown command " . $gitCommand . ".",
            ];
        };

        return [$route];
    }

    public function httpGitHelperAction(string $action, ?string $successMessage = null)
    {
        try {
            $this->gitHelper->$action();

            return [
                "status" => "ok",
                "message" => $successMessage,
            ];
        } catch (Exception $e) {
            $result = [
                "status" => "error",
                "message" => $e->getMessage(),
            ];

            if ($e instanceof GitException) {
                $runnerResult = $e->getRunnerResult();
                $result['output'] = $runnerResult->getOutput();
                $result['errorOutput'] = $runnerResult->getErrorOutput();
            }

            Header::panic();
            return $result;
        }
    }

    public function getHooks()
    {
        $gitHelper = $this->gitHelper;

        return [
            /*
            * Site
            */
            'site.update:after' => function () use ($gitHelper) {
                $gitHelper->kirbyChange('update', 'site');
            },

            /*
            * Pages
            */
            'page.create:after' => function ($page) use ($gitHelper) {
                $gitHelper->kirbyChange('create', 'page', $page->uri());
            },
            'page.update:after' => function ($newPage) use ($gitHelper) {
                $gitHelper->kirbyChange('update', 'page', $newPage->uri());
            },
            'page.delete:after' => function ($status, $page) use ($gitHelper) {
                $gitHelper->kirbyChange('delete', 'page', $page->uri());
            },
            'page.changeNum:after' => function ($newPage) use ($gitHelper) {
                $gitHelper->kirbyChange('sort', 'page', $newPage->uri());
            },
            'page.changeSlug:after' => function ($newPage) use ($gitHelper) {
                $gitHelper->kirbyChange('update', 'page', $newPage->uri());
            },
            'page.changeStatus:after' => function ($newPage) use ($gitHelper) {
                $gitHelper->kirbyChange('update', 'page', $newPage->uri());
            },
            'page.changeTemplate:after' => function ($newPage) use ($gitHelper) {
                $gitHelper->kirbyChange('update', 'page', $newPage->uri());
            },
            'page.changeTitle:after' => function ($newPage) use ($gitHelper) {
                $gitHelper->kirbyChange('update', 'page', $newPage->uri());
            },

            /*
            * File
            */
            'file.create:after' => function ($file) use ($gitHelper) {
                if (!$file->page()) {
                    return;
                }

                $gitHelper->kirbyChange('create', 'file', $file->page()->uri() . '/' . $file->filename());
            },
            'file.replace:after' => function ($newFile) use ($gitHelper) {
                if (!$newFile->page()) {
                    return;
                }

                $gitHelper->kirbyChange('replace', 'file', $newFile->page()->uri() . '/' . $newFile->filename());
            },
            'file.changeName:after' => function ($newFile) use ($gitHelper) {
                if (!$newFile->page()) {
                    return;
                }

                $gitHelper->kirbyChange('rename', 'file', $newFile->page()->uri() . '/' . $newFile->filename());
            },
            'file.update:after' => function ($newFile) use ($gitHelper) {
                if (!$newFile->page()) {
                    return;
                }

                $gitHelper->kirbyChange('update', 'file', $newFile->page()->uri() . '/' . $newFile->filename());
            },
            'file.changeSort:after' => function ($newFile) use ($gitHelper) {
                if (!$newFile->page()) {
                    return;
                }

                $gitHelper->kirbyChange('sort', 'file', $newFile->page()->uri() . '/' . $newFile->filename());
            },
            'file.delete:after' => function ($status, $file) use ($gitHelper) {
                if (!$file->page()) {
                    return;
                }

                $gitHelper->kirbyChange('delete', 'file', $file->page()->uri() . '/' . $file->filename());
            },
        ];
    }

}
