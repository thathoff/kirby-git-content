<?php
require(__DIR__ . DS . 'helpers.php');

$gitHelper = new KirbyGitHelper();


/*
* Site
*/
kirby()->hook('panel.site.update', function ($site) use ($gitHelper) {
    $gitHelper->kirbyChange('update(site)');
});

/*
* Pages
*/
kirby()->hook('panel.page.create', function ($page) use ($gitHelper) {
    $gitHelper->kirbyChange('create(page): ' . $page->uri());
});
kirby()->hook('panel.page.update', function ($page) use ($gitHelper) {
    $gitHelper->kirbyChange('update(page): ' . $page->uri());
});
kirby()->hook('panel.page.delete', function ($page) use ($gitHelper){
    $gitHelper->kirbyChange('delete(page): ' . $page->uri());
});
kirby()->hook('panel.page.sort', function ($page) use ($gitHelper){
    $gitHelper->kirbyChange('sort(page): ' . $page->uri());
});
kirby()->hook('panel.page.hide', function ($page) use ($gitHelper){
    $gitHelper->kirbyChange('hide(page): ' . $page->uri());
});
kirby()->hook('panel.page.move', function ($page) use ($gitHelper){
    $gitHelper->kirbyChange('move(page): ' . $page->uri());
});

/*
* File
*/
kirby()->hook('panel.file.upload', function ($file) use ($gitHelper){
    $gitHelper->kirbyChange('upload(file): ' . $file->page()->uri() . '/' . $file->filename());
});
kirby()->hook('panel.file.replace', function ($file) use ($gitHelper){
    $gitHelper->kirbyChange('replace(file): ' . $file->page()->uri() . '/' . $file->filename());
});
kirby()->hook('panel.file.rename', function ($file) use ($gitHelper){
    $gitHelper->kirbyChange('rename(file): ' . $file->page()->uri() . '/' . $file->filename());
});
kirby()->hook('panel.file.update', function ($file) use ($gitHelper){
    $gitHelper->kirbyChange('update(file): ' . $file->page()->uri() . '/' . $file->filename());
});
kirby()->hook('panel.file.sort', function ($file) use ($gitHelper){
    $gitHelper->kirbyChange('sort(file): ' . $file->page()->uri() . '/' . $file->filename());
});
kirby()->hook('panel.file.delete', function ($file) use ($gitHelper){
    $gitHelper->kirbyChange('delete(file): ' . $file->page()->uri() . '/' . $file->filename());
});


if(c::get('gcapc-cron-hooks-enabled', true)) {
    kirby()->routes(array(
        array(
            'pattern' => 'gcapc/(:any)',
            'action'  => function($gitCommand) use ($gitHelper) {
                switch ($gitCommand) {
                    case "push":
                        try {
                            $gitHelper->push();

                            echo response::json(array(
                                "status" => "success",
                                "data" => null,
                                "message" => "successfully pushed the content folder",
                            ));
                        } catch (Exception $e) {
                            echo response::json(array(
                                "status" => "error",
                                "data" => null,
                                "message" => $e->getMessage(),
                            ));
                        }

                        break;
                    case "pull":
                        try {
                            $gitHelper->pull();

                            echo response::json(array(
                                "status" => "success",
                                "data" => null,
                                "message" => "successfully pulled the content folder",
                            ));
                        } catch (Exception $e) {
                            echo response::json(array(
                                "status" => "error",
                                "data" => null,
                                "message" => $e->getMessage(),
                            ));
                        }

                        break;
                }
            }
        )
    ));
}

if (c::get('gcapc-panel-widget', true)) {
    $kirby->set('widget', 'git-commit-and-push-content', __DIR__  . DS . 'widgets' . DS . 'git-commit-and-push-content');
}
