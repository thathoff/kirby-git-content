# Kirby - Git Content

This is a plugin for [Kirby 3](http://getkirby.com/) that commits and pushes content changes made via the Panel to your git repository.

## Requirements

This plugin supports **Kirby from version 3.6** and requires **git version > 2.24**

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

Optional step: We advise you to include `.lock` files into the .gitignore of your newly created content repository.

```bash
echo ".lock" >> .gitignore
```

That's it! The plugin should work as of now.

### Download and configure the Plugin

`composer require thathoff/kirby-git-content`

To install this plugin without composer (not recommended):

- [download the source code](https://github.com/thathoff/kirby-git-content/archive/master.zip)
- run `composer install` locally
- copy the folder to your site/plugins folder.

We might create downloadable releases in the future which will make the above steps unnecessary.

### Options

By default this plugin just commits changes to the content repository. It’s recommended to setup a cron job
which calls `yourdomain.com/git-content/push`. This will push changes to the remote repository. By using a cron job
saving pages in panel is a lot faster then enabling the `push` option which will push changes after every commit.

This plugin is configurable via [Kirby Options](https://getkirby.com/docs/guide/configuration). Add the
following entires to your `config.php`.

```php
return [
  // other configuration options
  'thathoff' => [
    'git-content' => [
      'commit' => true,
    ],
  ],
];
```

#### Configuration Options

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

#### Custom Commit Message

By default the commit message is composed from the template `:action:(:item:): :url:`. So for example a change to
the page `example` will be committed with the message `update(page): example`. If you would like to change that
message you can use the `thathoff.git-content.commitMessage` option to overwrite the template.

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

- Pascal 'Pascalmh' Küsgen <http://pascalmh.de>
- Markus Denhoff <https://markus.denhoff.com>
