# Kirby Git Content

This is a plugin for [Kirby](http://getkirby.com/) that commits and pushes content changes made via the Panel to your git repository.

![Screnshot of Panel Area](/.github/screenshot.png?raw=true)

## Requirements

This plugin supports **Kirby from version 3.6 (including Kirby 4)** and requires **git version > 2.24**

## Usage

You can use this plugin to commit and push changes made via the Panel to your git repository. Either automatically
by setting the `commit` option to `true` or manually by visiting the panel view and adding a commit.

## Setup

### Download and configure the Plugin

#### Installation via composer (recommended)

`composer require thathoff/kirby-git-content`

#### Installation via git submodule

`git submodule add https://github.com/thathoff/kirby-git-content.git site/plugins/git-content`

#### Manual Installation
- [download the source code](https://github.com/thathoff/kirby-git-content/archive/master.zip)
- copy the folder to `site/plugins/git-content`.


### Create a new git repository for your content

Create a new git repository where you push your content to init the content repo and push it.

```bash
cd content

# include .lock files in .gitignore
echo ".lock" >> .gitignore

# init repo
git init
git add .
git commit -m "Initial Commit"
```

Remove the `content/` folder from your current git repository

```bash
cd ..
git rm --cached -r content
git add -A
git commit -m "Move Content Folder to separate repository"
```

## Configuration

By default this plugin just commits changes to the content repository. It’s recommended to setup a cron job
which calls `yourdomain.com/git-content/push`. This will push changes to the remote repository. By using a cron job
saving pages in panel is a lot faster then enabling the `push` option which will push changes after every commit.

This plugin is configurable via [Kirby Options](https://getkirby.com/docs/guide/configuration). Add the following entires to your `config.php`.

```php
return [
  // other configuration options
  'thathoff.git-content.commit' => true,
];
```

### Configuration Options

- `path` (String): Path to the repository, (default: `kirby()->root("content")`)
- `pull` (Boolean): Pull remote changes first? (default: `false`)
- `commit` (Boolean): Commit your changes? (default: `true`)
- `push` (Boolean): Push your changes to remote? (default: `false`)
- `commitMessage` (String): Configure the template for the commit message (default: `:action:(:item:): :url:`)
- `cronHooksEnabled` (Boolean): Whether `/git-content/push` and `/git-content/pull` endpoints are enabled or not. (default: `true`)
- `cronHooksSecret` (String): When set, this secret must be sent with the cronHooks as a get parameter.  Note: If you set
  a secret, only the GET method will work on the webhooks.   `/git-content/(pull|push)?secret=S0up3rS3c3t`
- `displayErrors` (Boolean): Display git errors when saving pages (default: `true`)
- `gitBin` (String): Path to the `git` binary
- `disable` (Boolean): If set to `true`, the plugin won't initialize. (default: `false`)
- `disableBranchManagement` (Boolean): If set to `true`, the options to create and switch branches are hidden. (default: `false`)
- `helpText` (String): Supply a custom help text shown in the panel UI. (default: `null`)
- `menuIcon` (String): Supply a custom icon for the panel menu item. (default: `sitemap`)
- `menuLabel` (String): Supply a custom label for the panel menu item. (default: `Git Content`)

### Custom Commit Message

By default the commit message is composed from the template `:action:(:item:): :url:`. So for example a change to
the page `example` will be committed with the message `update(page): example`. If you would like to change that
message you can use the `thathoff.git-content.commitMessage` option to overwrite the template.

## Hooks

The plugin triggers hooks before and after content is pulled or pushed via the interface or the web endpoints.
You can use these hooks to trigger other actions, for example to deploy your site after a push or clear caches
after a pull.

```php
// site/config/config.php

return [
  // other configuration options
  'hooks' => [
    'thathoff.git-content.push:before' => function () {
      // do something before a push
    },
    'thathoff.git-content.push:after' => function ($response) {
      // do something after a push
    },
    'thathoff.git-content.pull:before' => function () {
      // do something before a pull
    },
    'thathoff.git-content.pull:after' => function ($response) {
      // do something after a pull
    },
  ],
];
```

## Git LFS
Your repository might increase over time, by adding Images, Audio, Video, Binaries, etc.
cloning and updating your content repository can take a lot of time. If you are able to use
[Git LFS](https://git-lfs.github.com/) you probably should. Here is what the .gitattributes-File could look like:

```
*.zip filter=lfs diff=lfs merge=lfs -text
*.jpg filter=lfs diff=lfs merge=lfs -text
*.jpeg filter=lfs diff=lfs merge=lfs -text
*.png filter=lfs diff=lfs merge=lfs -text
*.gif filter=lfs diff=lfs merge=lfs -text
```

## Authors

Maintained and developed by [Markus Denhoff](https://markus.denhoff.com) and [Contributors](https://github.com/thathoff/kirby-git-content/graphs/contributors). Initial version by [Pascal Küsgen](https://github.com/Pascalmh).

Supported by [reinorange GmbH](https://reinorange.com).
