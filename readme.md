# Kirby - Git Commit And Push Content

This is a plugin for [Kirby](http://getkirby.com/) that Commits and Pushes Changes made via the Panel to your Git Repository.

## Installation

### Create a new git repository for your content

Create a new git repository where you push your content to, name it `your-project_content`.

### Download and configure the Plugin

Put all the files into your `site/plugins/git-commit-and-push-content/` folder. If the `git-commit-and-push-content/` plugin folder doesn't exist then create it.

Or: go into your `site/plugins/` folder and  `git clone https://github.com/Pascalmh/kirby-git-commit-and-push-content git-commit-and-push-content`.

Add this to your `site/config/config.php` and adapt it to your needs (make use of kirbys [Multi-environment setup](http://getkirby.com/blog/multi-environment-setup)):
```php
// Plugin: git-commit-push-content
c::set('gcapc-branch', 'master'); // default: 'master'
c::set('gcapc-pull', true); // default: true
c::set('gcapc-push', true); // default: true
c::set('gcapc-commit', true); // default: true
c::set('gcapc-pushCommand', 'git push'); // default: 'git push'
```

`gcapc-branch` - (string): - [git branch](http://git-scm.com/docs/git-branch) to be pulled from and pushed to

`gcapc-pushCommand` - (string): [git push](http://git-scm.com/docs/git-push)

`gcapc-pull` - (bool): Should remote changes be pulled first?

`gcapc-commit` - (bool): Do you want your changes to be committed?

`gcapc-push` - (bool): Do you want your changes to be pushed?

### Init the content repo and push it

Remove `content/` folder from your current git repository
```
git rm --cached -r content
git add -A
git commit -m "Move Content Folder to separate repository"
``` 

Add `content/` folder to new git repository

```
cd content
git init
git remote add origin https://github.com/your-project/your-project_content.git
git add -A
git commit -m "Initial Content Commit"
git push origin master
```

## Usage 

Just keep using the Panel as you are used to and watch the commits appear in your git repository! 

If you are on localhost you need to push your changes manually.

## Author

Pascal 'Pascalmh' Küsgen <http://pascalmh.de>
