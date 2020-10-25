<?php

// support manual installation in plugins folder
@include_once __DIR__ . '/vendor/autoload.php';

$kirbyGit = new Thathoff\GitContent\KirbyGit();

Kirby::plugin('thathoff/git-content', [
    'hooks' => $kirbyGit->getHooks(),
    'routes' => $kirbyGit->getRoutes(),
    'options' => [
        'path' => null,
        'branch' => null,
        'pull' => null,
        'push' => null,
        'commit' => null,
        'cronHooksEnabled' => null,
        'commitMessage' => ':action:(:item:): :url:',
        'windowsMode' => null,
        'gitBin' => null,
        'displayErrors' => null,
    ]
]);
