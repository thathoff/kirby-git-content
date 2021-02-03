<?php

// support manual installation in plugins folder
@include_once __DIR__ . '/vendor/autoload.php';

$kirbyGit = new Thathoff\GitContent\KirbyGit();

# don't load plugin if it's disabled in the config.
if (!option('thathoff.git-content.disable', false)) {
    Kirby::plugin('thathoff/git-content', [
        'hooks'   => $kirbyGit->getHooks(),
        'routes'  => $kirbyGit->getRoutes(),
        'options' => [
            'path'             => null,
            'branch'           => null,
            'pull'             => null,
            'push'             => null,
            'commit'           => null,
            'cronHooksEnabled' => null,
            'commitMessage'    => ':action:(:item:): :url:',
            'windowsMode'      => null,
            'gitBin'           => null,
            'displayErrors'    => null,
            'disable'          => null,
        ],
    ]);
}
