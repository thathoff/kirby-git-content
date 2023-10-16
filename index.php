<?php

// support manual installation in plugins folder
@include_once __DIR__ . '/vendor/autoload.php';

// don't load plugin if it's disabled in the config.
if (option('thathoff.git-content.disable', false)) {
    return;
}

$kirbyGit = new Thathoff\GitContent\KirbyGit();

Kirby::plugin('thathoff/git-content', [
    'hooks' => $kirbyGit->getHooks(),
    'routes' => $kirbyGit->getRoutes(),
    'api' => [
        'routes' => $kirbyGit->getApiRoutes()
    ],
    'areas' => [
        'git-content' => require __DIR__ . '/src/areas/git-content.php',
    ],
    'options' => [
        'path'             => null,
        'pull'             => null,
        'push'             => null,
        'commit'           => null,
        'cronHooksEnabled' => null,
        'cronHooksSecret'  => null,
        'commitMessage'    => ':action:(:item:): :url:',
        'windowsMode'      => null,
        'gitBin'           => null,
        'displayErrors'    => null,
        'disableBranchManagement' => null,
        'disable'          => null,
    ],
]);
