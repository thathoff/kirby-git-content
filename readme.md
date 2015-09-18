# Kirby - Git Commit And Push Content

This is a plugin for [Kirby](http://getkirby.com/) that Commits and Pushes Changes made via the Panel to your Git Repository.

## Installation

### Create a new git repository for your content

Create a new git repository where you push your content to, name it `your-project_content`.

### Download and configure the Plugin

Put all the files into your `site/plugins/git-commit-and-push-content/` folder. If the `git-commit-and-push-content/` plugin folder doesn't exist then create it.

Or: go into you `site/plugins/` folder and  `git clone https://github.com/Pascalmh/kirby-git-commit-and-push-content git-commit-and-push-content`.

Add this to your `site/config/config.php` and adapt it to your needs:
```php
// Plugin: git-commit-push-content
c::set('gcapcSshKeyPath', '.ssh/id_rsa'); // .ssh/id_rsa
c::set('gcapcGitServer', 'git@your-git-remote.tld'); // git@yyour-git-remote.tld
c::set('gcapcGitRepository', 'your-project/your-project_content.git'); // your-project/your-project_content.git
```

`gcapcSshKeyPath` - Create a new Git User with Push Permissions, generate an ssh-key for it and put it on the server
`gcapcGitServer` - Hostname with optional user, for example `git@your-git-remote.tld`
`gcapcGitRepository` - your git repository `your-project/your-project_content.git`

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

Just keep using the Panel as you are used to and watch the commits appear in you git repository! 

If you are on localhost you need to push your changes manually.

## Author

Pascal 'Pascalmh' Küsgen <http://pascalmh.de>
