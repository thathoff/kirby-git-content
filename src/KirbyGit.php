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
            'site.update:after' => function ($site) use ($gitHelper) {
                $gitHelper->kirbyChange('update(site)');
            },

            /*
            * Pages
            */
            'page.create:after' => function ($page) use ($gitHelper) {
                $gitHelper->kirbyChange('create(page): ' . $page->uri());
            },
            'page.update:after' => function ($page) use ($gitHelper) {
                $gitHelper->kirbyChange('update(page): ' . $page->uri());
            },
            'page.delete:after' => function ($page) use ($gitHelper) {
                $gitHelper->kirbyChange('delete(page): ' . $page->uri());
            },
            'page.sort:after' => function ($page) use ($gitHelper) {
                $gitHelper->kirbyChange('sort(page): ' . $page->uri());
            },
            'page.hide:after' => function ($page) use ($gitHelper) {
                $gitHelper->kirbyChange('hide(page): ' . $page->uri());
            },
            'page.move:after' => function ($page) use ($gitHelper) {
                $gitHelper->kirbyChange('move(page): ' . $page->uri());
            },

            /*
            * File
            */
            'file.upload:after' => function ($file) use ($gitHelper) {
                $gitHelper->kirbyChange('upload(file): ' . $file->page()->uri() . '/' . $file->filename());
            },
            'file.replace:after' => function ($file) use ($gitHelper) {
                $gitHelper->kirbyChange('replace(file): ' . $file->page()->uri() . '/' . $file->filename());
            },
            'file.rename:after' => function ($file) use ($gitHelper) {
                $gitHelper->kirbyChange('rename(file): ' . $file->page()->uri() . '/' . $file->filename());
            },
            'file.update:after' => function ($file) use ($gitHelper) {
                $gitHelper->kirbyChange('update(file): ' . $file->page()->uri() . '/' . $file->filename());
            },
            'file.sort:after' => function ($file) use ($gitHelper) {
                $gitHelper->kirbyChange('sort(file): ' . $file->page()->uri() . '/' . $file->filename());
            },
            'file.delete:after' => function ($file) use ($gitHelper) {
                $gitHelper->kirbyChange('delete(file): ' . $file->page()->uri() . '/' . $file->filename());
            },
        ];
    }

}
