<?php

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
        'windowsMode' => null,
        'gitBin' => null,
        'displayErrors' => null,
    ]
]);
