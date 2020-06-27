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
                $gitHelper->kirbyChange('update', 'site');
            },

            /*
            * Pages
            */
            'page.create:after' => function ($page) use ($gitHelper) {
                $gitHelper->kirbyChange('create', 'page', $page->uri());
            },
            'page.update:after' => function ($page) use ($gitHelper) {
                $gitHelper->kirbyChange('update', 'page', $page->uri());
            },
            'page.delete:after' => function ($status, $page) use ($gitHelper) {
                $gitHelper->kirbyChange('delete', 'page', $page->uri());
            },
            'page.changeNum:after' => function ($page) use ($gitHelper) {
                $gitHelper->kirbyChange('sort', 'page', $page->uri());
            },
            'page.changeSlug:after' => function ($page) use ($gitHelper) {
                $gitHelper->kirbyChange('update', 'page', $page->uri());
            },
            'page.changeStatus:after' => function ($page) use ($gitHelper) {
                $gitHelper->kirbyChange('update', 'page', $page->uri());
            },
            'page.changeTemplate:after' => function ($page) use ($gitHelper) {
                $gitHelper->kirbyChange('update', 'page', $page->uri());
            },
            'page.changeTitle:after' => function ($page) use ($gitHelper) {
                $gitHelper->kirbyChange('update', 'page', $page->uri());
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
            'file.replace:after' => function ($file) use ($gitHelper) {
                if (!$file->page()) {
                    return;
                }

                $gitHelper->kirbyChange('replace', 'file', $file->page()->uri() . '/' . $file->filename());
            },
            'file.changeName:after' => function ($file) use ($gitHelper) {
                if (!$file->page()) {
                    return;
                }

                $gitHelper->kirbyChange('rename', 'file', $file->page()->uri() . '/' . $file->filename());
            },
            'file.update:after' => function ($file) use ($gitHelper) {
                if (!$file->page()) {
                    return;
                }

                $gitHelper->kirbyChange('update', 'file', $file->page()->uri() . '/' . $file->filename());
            },
            'file.changeSort:after' => function ($file) use ($gitHelper) {
                if (!$file->page()) {
                    return;
                }

                $gitHelper->kirbyChange('sort', 'file', $file->page()->uri() . '/' . $file->filename());
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
