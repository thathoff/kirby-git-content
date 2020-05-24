# Kirby - Git Content

This is a plugin for [Kirby 3](http://getkirby.com/) that commits and pushes changes made via the Panel to your git repository.

**⚠️ The current version only supports Kirby 3. For Kirby 2 support please use version 2 of this plugin.**

## Usage

Just keep using the Panel as you are used to and watch the commits appear in your git repository!

## Installation

### Create a new git repository for your content

Create a new git repository where you push your content to, name it `your-project_content`.

Init the content repo and push it

Remove the `content/` folder from your current git repository
```bash
git rm --cached -r content
git add -A
git commit -m "Move Content Folder to separate repository"
```

Add the `content/` folder to new git repository

```bash
cd content
git init
git remote add origin https://github.com/your-project/your-project_content.git
git add -A
git commit -m "Initial Content Commit"
git push origin master
```

### Download and configure the Plugin

`composer require thathoff/kirby-git-content`

To install this plugin without composer (not recommended):

- [download the source code](https://github.com/thathoff/kirby-git-content/archive/master.zip)
- run `composer install` locally
- run `composer remove getkirby/cms` (See https://github.com/getkirby/getkirby.com/issues/138)
- copy the folder to your site/plugins folder.

We might create downloadable releases in the future which will make the above steps unnecessary.

### Options

By default this plugin just commits changes to the content repository. It’s recommended to setup a cron job
which calls `yourdomain.com/gcapc/push`. This will push changes to the remote repository. By using a cron job
saving pages in panel is a lot faster then enabling the `push` option which will push changes after every commit.

This plugin is configurable via [Kirby Options](https://getkirby.com/docs/guide/configuration). Add the
following entires to your `config.php`.

```php
return [
  // other configuration options
  'blankogmbh' => [
    'gcapc' => [
      'commit' => true,
    ],
  ],
]
```

#### Configuration Options

- `path` (String): Path to the repository, (default: `kirby()->root("content")`)
- `branch` (String): branch name to be checked out (defaut: currently checked out branch)
- `pull` (Boolean): Pull remote changes first? (default: `false`)
- `commit` (Boolean): Commit your changes? (default: `true`)
- `push` (Boolean): Push your changes to remote? (default: `false`)
- `cronHooksEnabled` (Boolean): Whether `/gcapc/push` and `/gcapc/pull` endpoints are enabled or not. (default: `true`)
- `displayErrors` (Boolean): Display git errors when saving pages (default: `false`)
- `gitBin` (String): Path to the `git` binary, [See Git.php](http://kbjr.github.io/Git.php/) `Git::set_bin(string $path)`
- `windowsMode` (Boolean): [See Git.php](http://kbjr.github.io/Git.php/) `Git::windows_mode()` (default: `false`)


## Git LFS
Your repository might increase over time, by adding Images, Audio, Video, Binaries, etc.
cloning and updating your content repostory can take a lot of time. If you are able to use
[Git LFS](https://git-lfs.github.com/) you probably should. Here is what the .gitattributes-File could look like:

```
*.zip filter=lfs diff=lfs merge=lfs -text
*.jpg filter=lfs diff=lfs merge=lfs -text
*.jpeg filter=lfs diff=lfs merge=lfs -text
*.png filter=lfs diff=lfs merge=lfs -text
*.gif filter=lfs diff=lfs merge=lfs -text
```

## Authors

- Pascal 'Pascalmh' Küsgen <http://pascalmh.de>
- Markus Denhoff <https://markus.denhoff.com>
