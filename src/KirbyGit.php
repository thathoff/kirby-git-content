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
            ],
            [
                'pattern' => 'git-content/fetch',
                'method'  => 'POST',
                'action'  => function () use ($kirbyGit) {
                    return $kirbyGit->httpGitHelperAction('fetch', "successfully fetched remote changes");
                },
            ],
			[
                'pattern' => 'git-content/reset',
                'method'  => 'POST',
                'action'  => function () use ($kirbyGit) {
                    return $kirbyGit->httpGitHelperAction('reset', "successfully reset the content folder");
                },
            ],
            [
                'pattern' => 'git-content/remove-index-lock',
                'method'  => 'POST',
                'action'  => function () use ($kirbyGit) {
                    return $kirbyGit->httpGitHelperAction('removeIndexLock', "successfully removed the index lock");
                },
            ],
            [
                'pattern' => 'git-content/status',
                'method' => 'GET',
                'action' => function () use ($kirbyGit) {
                    return $kirbyGit->httpGitHelperAction('status', null); // response message is response of 'git status'
                }
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
                case "pull":
                    return $helper->httpGitHelperAction('pull', "successfully pulled the content folder");
                case "reset":
                    if (!$secret) {
                        return [
                            "status" => "forbidden",
                            "message" => "Please configure a cron hook secret for the reset command.",
                        ];
                    }

                    $helper->httpGitHelperAction('fetch', "successfully fetched remote changes");
                    return $helper->httpGitHelperAction('resetToOrigin', "successfully reset the content folder");
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
            kirby()->trigger('thathoff.git-content.' . $action . ':before');

            // when no $successMessage is provided, the response of the $action call is returned
            $response = $this->gitHelper->$action();

            kirby()->trigger('thathoff.git-content.' . $action . ':after', ['response' => $response]);

            return [
                "status" => "ok",
                "message" => $successMessage ?? $response,
            ];
        } catch (Exception $e) {
            $result = [
                "status" => "error",
                "message" => $e->getMessage(),
            ];

            // enhance message when exception is a GitException
            if ($e instanceof GitException && $runnerResult = $e->getRunnerResult()) {
                $result['message'] = implode("\n", $runnerResult->getErrorOutput());
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
            'site.update:after' => function ($newSite) use ($gitHelper) {
                $gitHelper->kirbyChange('update', 'site', [$newSite->root()], $newSite);
            },

            /*
            * Pages
            */
            'page.create:after' => function ($page) use ($gitHelper) {
                $gitHelper->kirbyChange('create', 'page', [$page->root()], $page->id());
            },
            'page.update:after' => function ($newPage) use ($gitHelper) {
                $gitHelper->kirbyChange('update', 'page', [$newPage->root()], $newPage->id());
            },
            'page.delete:after' => function ($status, $page) use ($gitHelper) {
                $gitHelper->kirbyChange('delete', 'page', [$page->root()], $page->id());
            },
            'page.changeNum:after' => function ($newPage) use ($gitHelper) {
                $parent = $newPage->parent() ?: kirby()->site();
                $gitHelper->kirbyChange('sort', 'page', [$parent->root()], $newPage->id());
            },
            'page.changeSlug:after' => function ($newPage, $oldPage) use ($gitHelper) {
                $gitHelper->kirbyChange('update', 'page', [$newPage->root(), $oldPage->root()], $newPage->id());
            },
            'page.changeStatus:after' => function ($newPage, $oldPage) use ($gitHelper) {
                $parent = $newPage->parent() ?: kirby()->site();
                $gitHelper->kirbyChange('update', 'page', [$parent->root(), $oldPage->root()], $newPage->id());
            },
            'page.changeTemplate:after' => function ($newPage) use ($gitHelper) {
                $gitHelper->kirbyChange('update', 'page', [$newPage->root()], $newPage->id());
            },
            'page.changeTitle:after' => function ($newPage) use ($gitHelper) {
                $gitHelper->kirbyChange('update', 'page', [$newPage->root()], $newPage->id());
            },

            /*
            * File
            */
            'file.create:after' => function ($file) use ($gitHelper) {
                $gitHelper->kirbyChange('create', 'file', [dirname($file->root())], $file->id());
            },
            'file.replace:after' => function ($newFile) use ($gitHelper) {
                $gitHelper->kirbyChange('replace', 'file', [dirname($newFile->root())], $newFile->id());
            },
            'file.changeName:after' => function ($newFile) use ($gitHelper) {
                $gitHelper->kirbyChange('rename', 'file', [dirname($newFile->root())], $newFile->id());
            },
            'file.update:after' => function ($newFile) use ($gitHelper) {
                $gitHelper->kirbyChange('update', 'file', [dirname($newFile->root())], $newFile->id());
            },
            'file.changeSort:after' => function ($newFile) use ($gitHelper) {
                $gitHelper->kirbyChange('sort', 'file', [dirname($newFile->root())], $newFile->id());
            },
            'file.delete:after' => function ($status, $file) use ($gitHelper) {
                $gitHelper->kirbyChange('delete', 'file', [dirname($file->root())], $file->id());
            },
        ];
    }
}
