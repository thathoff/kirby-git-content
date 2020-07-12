<?php

namespace Blanko\Kirby\GCAPC;

use Exception;
use Kirby\Http\Header;

class KirbyGit
{
    public function __construct()
    {
        $this->gitHelper = new KirbyGitHelper();
    }

    public function getRoutes()
    {
        if (!option("blankogmbh.gcapc.cronHooksEnabled", true)) {
            return [];
        }

        $gitHelper = $this->gitHelper;

        $route = [];
        $route['pattern'] = 'gcapc/(:any)';
        $route['action'] = function($gitCommand) use ($gitHelper) {
            switch ($gitCommand) {
                case "push":
                    try {
                        $gitHelper->push();

                        return [
                            "status" => "ok",
                            "message" => "successfully pushed the content folder",
                        ];
                    } catch (Exception $e) {
                        Header::panic();
                        return [
                            "status" => "error",
                            "message" => $e->getMessage(),
                        ];
                    }

                case "pull":
                    try {
                        $gitHelper->pull();

                        return [
                            "status" => "ok",
                            "message" => "successfully pulled the content folder",
                        ];
                    } catch (Exception $e) {
                        Header::panic();
                        return [
                            "status" => "error",
                            "message" => $e->getMessage(),
                        ];
                    }
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

    public function getHooks()
    {
        $gitHelper = $this->gitHelper;

        return [
            /*
            * Site
            */
            'site.update:after' => function () use ($gitHelper) {
                $gitHelper->kirbyChange('update(site)');
            },

            /*
            * Pages
            */
            'page.create:after' => function ($page) use ($gitHelper) {
                $gitHelper->kirbyChange('create(page): ' . $page->uri());
            },
            'page.update:after' => function ($newPage) use ($gitHelper) {
                $gitHelper->kirbyChange('update(page): ' . $newPage->uri());
            },
            'page.delete:after' => function ($status, $page) use ($gitHelper) {
                $gitHelper->kirbyChange('delete(page): ' . $page->uri());
            },
            'page.changeNum:after' => function ($newPage) use ($gitHelper) {
                $gitHelper->kirbyChange('sort(page): ' . $newPage->uri());
            },
            'page.changeSlug:after' => function ($newPage) use ($gitHelper) {
                $gitHelper->kirbyChange('update(page): ' . $newPage->uri());
            },
            'page.changeStatus:after' => function ($newPage) use ($gitHelper) {
                $gitHelper->kirbyChange('update(page): ' . $newPage->uri());
            },
            'page.changeTemplate:after' => function ($newPage) use ($gitHelper) {
                $gitHelper->kirbyChange('update(page): ' . $newPage->uri());
            },
            'page.changeTitle:after' => function ($newPage) use ($gitHelper) {
                $gitHelper->kirbyChange('update(page): ' . $newPage->uri());
            },

            /*
            * File
            */
            'file.create:after' => function ($file) use ($gitHelper) {
                if (!$file->page()) {
                    return;
                }

                $gitHelper->kirbyChange('create(file): ' . $file->page()->uri() . '/' . $file->filename());
            },
            'file.replace:after' => function ($newFile) use ($gitHelper) {
                if (!$newFile->page()) {
                    return;
                }

                $gitHelper->kirbyChange('replace(file): ' . $newFile->page()->uri() . '/' . $newFile->filename());
            },
            'file.changeName:after' => function ($newFile) use ($gitHelper) {
                if (!$newFile->page()) {
                    return;
                }

                $gitHelper->kirbyChange('rename(file): ' . $newFile->page()->uri() . '/' . $newFile->filename());
            },
            'file.update:after' => function ($newFile) use ($gitHelper) {
                if (!$newFile->page()) {
                    return;
                }

                $gitHelper->kirbyChange('update(file): ' . $newFile->page()->uri() . '/' . $newFile->filename());
            },
            'file.changeSort:after' => function ($newFile) use ($gitHelper) {
                if (!$newFile->page()) {
                    return;
                }

                $gitHelper->kirbyChange('sort(file): ' . $newFile->page()->uri() . '/' . $newFile->filename());
            },
            'file.delete:after' => function ($status, $file) use ($gitHelper) {
                if (!$file->page()) {
                    return;
                }

                $gitHelper->kirbyChange('delete(file): ' . $file->page()->uri() . '/' . $file->filename());
            },
        ];
    }

}
