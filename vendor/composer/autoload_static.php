<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0624fde4d8e07964e41d8b1e5bbe065a
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Thathoff\\GitContent\\' => 20,
        ),
        'K' => 
        array (
            'Kirby\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Thathoff\\GitContent\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Kirby\\' => 
        array (
            0 => __DIR__ . '/..' . '/getkirby/composer-installer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'CzProject\\GitPhp\\CommandProcessor' => __DIR__ . '/..' . '/czproject/git-php/src/CommandProcessor.php',
        'CzProject\\GitPhp\\Commit' => __DIR__ . '/..' . '/czproject/git-php/src/Commit.php',
        'CzProject\\GitPhp\\CommitId' => __DIR__ . '/..' . '/czproject/git-php/src/CommitId.php',
        'CzProject\\GitPhp\\Exception' => __DIR__ . '/..' . '/czproject/git-php/src/exceptions.php',
        'CzProject\\GitPhp\\Git' => __DIR__ . '/..' . '/czproject/git-php/src/Git.php',
        'CzProject\\GitPhp\\GitException' => __DIR__ . '/..' . '/czproject/git-php/src/exceptions.php',
        'CzProject\\GitPhp\\GitRepository' => __DIR__ . '/..' . '/czproject/git-php/src/GitRepository.php',
        'CzProject\\GitPhp\\Helpers' => __DIR__ . '/..' . '/czproject/git-php/src/Helpers.php',
        'CzProject\\GitPhp\\IRunner' => __DIR__ . '/..' . '/czproject/git-php/src/IRunner.php',
        'CzProject\\GitPhp\\InvalidArgumentException' => __DIR__ . '/..' . '/czproject/git-php/src/exceptions.php',
        'CzProject\\GitPhp\\InvalidStateException' => __DIR__ . '/..' . '/czproject/git-php/src/exceptions.php',
        'CzProject\\GitPhp\\RunnerResult' => __DIR__ . '/..' . '/czproject/git-php/src/RunnerResult.php',
        'CzProject\\GitPhp\\Runners\\CliRunner' => __DIR__ . '/..' . '/czproject/git-php/src/Runners/CliRunner.php',
        'CzProject\\GitPhp\\Runners\\MemoryRunner' => __DIR__ . '/..' . '/czproject/git-php/src/Runners/MemoryRunner.php',
        'CzProject\\GitPhp\\Runners\\OldGitRunner' => __DIR__ . '/..' . '/czproject/git-php/src/Runners/OldGitRunner.php',
        'CzProject\\GitPhp\\StaticClassException' => __DIR__ . '/..' . '/czproject/git-php/src/exceptions.php',
        'Kirby\\ComposerInstaller\\CmsInstaller' => __DIR__ . '/..' . '/getkirby/composer-installer/src/ComposerInstaller/CmsInstaller.php',
        'Kirby\\ComposerInstaller\\Installer' => __DIR__ . '/..' . '/getkirby/composer-installer/src/ComposerInstaller/Installer.php',
        'Kirby\\ComposerInstaller\\Plugin' => __DIR__ . '/..' . '/getkirby/composer-installer/src/ComposerInstaller/Plugin.php',
        'Kirby\\ComposerInstaller\\PluginInstaller' => __DIR__ . '/..' . '/getkirby/composer-installer/src/ComposerInstaller/PluginInstaller.php',
        'Thathoff\\GitContent\\KirbyGit' => __DIR__ . '/../..' . '/src/KirbyGit.php',
        'Thathoff\\GitContent\\KirbyGitHelper' => __DIR__ . '/../..' . '/src/KirbyGitHelper.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit0624fde4d8e07964e41d8b1e5bbe065a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit0624fde4d8e07964e41d8b1e5bbe065a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit0624fde4d8e07964e41d8b1e5bbe065a::$classMap;

        }, null, ClassLoader::class);
    }
}
