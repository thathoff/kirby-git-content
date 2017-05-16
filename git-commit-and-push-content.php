<?php
require(__DIR__ . DS . 'helpers.php');

$gitHelper = new KirbyGitHelper();

/*
* Pages
*/
kirby()->hook('panel.page.create', function ($page) use ($gitHelper) {
    $gitHelper->kirbyChangePage('page.create', $page);
});
kirby()->hook('panel.page.update', function ($page) use ($gitHelper) {
    $gitHelper->kirbyChangePage('page.update', $page);
});
kirby()->hook('panel.page.delete', function ($page) use ($gitHelper){
    $gitHelper->kirbyChangePage('page.delete', $page);
});
kirby()->hook('panel.page.sort', function ($page) use ($gitHelper){
    $gitHelper->kirbyChangePage('page.sort', $page);
});
kirby()->hook('panel.page.hide', function ($page) use ($gitHelper){
    $gitHelper->kirbyChangePage('page.hide', $page);
});
kirby()->hook('panel.page.move', function ($page) use ($gitHelper){
    $gitHelper->kirbyChangePage('page.move', $page);
});

/*
* File
*/
kirby()->hook('panel.file.upload', function ($file) use ($gitHelper){
    $gitHelper->kirbyChangeFile('file.upload', $file);
});
kirby()->hook('panel.file.replace', function ($file) use ($gitHelper){
    $gitHelper->kirbyChangeFile('file.replace', $file);
});
kirby()->hook('panel.file.rename', function ($file) use ($gitHelper){
    $gitHelper->kirbyChangeFile('file.rename', $file);
});
kirby()->hook('panel.file.update', function ($file) use ($gitHelper){
    $gitHelper->kirbyChangeFile('file.update', $file);
});
kirby()->hook('panel.file.sort', function ($file) use ($gitHelper){
    $gitHelper->kirbyChangeFile('file.sort', $file);
});
kirby()->hook('panel.file.delete', function ($file) use ($gitHelper){
    $gitHelper->kirbyChangeFile('file.delete', $file);
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
                                "message" => "Successfully pushed the content folder.",
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
                                "message" => "Successfully pulled the content folder.",
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
