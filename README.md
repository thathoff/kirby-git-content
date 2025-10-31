# Kirby Git Content

This is a plugin for [Kirby](http://getkirby.com/) that commits and pushes content changes made via the Panel to your git repository.

![Screnshot of Panel Area](/.github/screenshot.png?raw=true)

## Requirements

This plugin supports **Kirby from version 3.6 (including Kirby 4 and 5)** and requires **git version > 2.24**

Please make sure that PHP on your server has not disabled both `proc_open` and `proc_close` as they are required
by czproject/git-php used to communicate with git.

Another requirement is that the git repository in your content folder is owned by the user running the PHP process
otherwise git failes with a `dubious ownership` error.

If you are **updating to Kirby 5**, please make sure to add the `_changes` folder to your `.gitignore` file.

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
- [download the source code](https://github.com/thathoff/kirby-git-content/archive/main.zip)
- copy the folder to `site/plugins/git-content`.

### Create a new git repository for your content

Create a new git repository where you push your content to init the content repo and push it.

```bash
cd content

# for Kirby 3 and 4 include .lock files in .gitignore
echo ".lock" >> .gitignore

# for Kirby 5 add the _changes folder to .gitignore
echo "_changes" >> .gitignore

# init repo
git init
git add .
git commit -m "Initial Commit"
```

Please note: Make a first commit to the repository. If you have a repository with no commits, the git content view
in the panel will not work.

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
- `cronHooksSecret` (String): When set, this secret must be sent with the cronHooks as a get parameter, see [Cron / Webhooks](#cron--webhooks) for more details.
- `displayErrors` (Boolean): Display git errors when saving pages (default: `true`)
- `gitBin` (String): Path to the `git` binary
- `disable` (Boolean): If set to `true`, the plugin won't initialize. (default: `false`)
- `disableBranchManagement` (Boolean): If set to `true`, the options to create and switch branches are hidden. (default: `false`)
- `helpText` (String): Supply a custom help text shown in the panel UI. (default: `null`)
- `menuIcon` (String): Supply a custom icon for the panel menu item. (default: `sitemap`)
- `menuLabel` (String): Supply a custom label for the panel menu item. (default: `Git Content`)
- `buttons` (Array): Enable or disable buttons in the panel UI. See [Buttons & Permissions](#buttons--permissions) for options.

### Cron / Webhooks

The plugin provides three webhook that you can trigger via cron or webhooks in your CI/CD pipeline. You can
enable or disable the webhooks by setting the `cronHooksEnabled` option.

- `/git-content/push`: Pushes changes to the remote repository.
- `/git-content/pull`: Fetches the latest changes from the remote repository.
- `/git-content/reset`: Resets the local repository to the remote repository (requires a secret to be set).

You can call the webhooks via HTTP GET or POST request. If you have a secret set, you can either provide the
secret as a query parameter or as a body parameter.

```bash
curl https://example.com/git-content/pull?secret=S0up3rS3c3t"
curl -X POST https://example.com/git-content/push --data "secret=S0up3rS3c3t"
```

### Buttons & Permissions

The plugin allows you to enable or disable buttons in the panel UI either globally or per role.

To enable or disable buttons globally, set the `buttons` option to an array of button names. The keys are:

- `fetch`: Fetches the latest changes from the remote repository.
- `commit`: Allows to commit changes to the repository.
- `pull`: Allows to pull changes from the remote repository.
- `push`: Allows to push changes to the remote repository.
- `createBranch`: Allows to create a new branch.
- `switchBranch`: Allows to switch to a different branch.
- `removeIndexLock`: Removes the index lock file if it exists.
- `reset`: Resets the local repository to the remote repository (disabled by default).

```php
return [
  'thathoff.git-content.buttons' => [
    'reset' => true, // enables the reset to origin button (default: false)
    'fetch' => false, // disables the fetch button (default: true)
  ],
];
```

You also can enable or disable buttons per role by adding the permission to to users blueprint. For example to
disable the fetch button for the role `editor`, add the following to the `editor` blueprint:

```yaml
permissions:
  thathoff.git-content:
    fetch: false
```

You can use the same keys as in the `buttons` option to enable or disable buttons per role.

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

## Running on Shared Hosting

Running this plugin on shared hosting can be tricky. You need to ensure that the PHP process has access to the Git binary,
and that the repository is owned by the user running the PHP process.

Accessing the repository via SSH may also be difficult. In these cases, it may be easier to use the HTTPS URL.
Check out the repository using HTTPS, a personal access token or password, depending on your Git provider.

When cloning the repository, you can include the username and password like this:

```bash
git clone https://username:personal-access-token@github.com/yourusername/yourrepository.git
```

## Authors

Maintained and developed by [Markus Denhoff](https://markus.denhoff.com) and [Contributors](https://github.com/thathoff/kirby-git-content/graphs/contributors). Initial version by [Pascal Küsgen](https://github.com/Pascalmh).

Supported by [reinorange GmbH](https://reinorange.com).
