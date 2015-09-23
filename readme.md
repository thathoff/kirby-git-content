# Kirby - Git Commit And Push Content

This is a plugin for [Kirby](http://getkirby.com/) that Commits and Pushes Changes made via the Panel to your Git Repository.

## Usage 

Just keep using the Panel as you are used to and watch the commits appear in your git repository!

## Installation

### Create a new git repository for your content

Create a new git repository where you push your content to, name it `your-project_content`.

Init the content repo and push it

Remove the `content/` folder from your current git repository
```
git rm --cached -r content
git add -A
git commit -m "Move Content Folder to separate repository"
``` 

Add the `content/` folder to new git repository

```
cd content
git init
git remote add origin https://github.com/your-project/your-project_content.git
git add -A
git commit -m "Initial Content Commit"
git push origin master
```

### Download and configure the Plugin

Go into your `site/plugins/` folder and  
```
git submodule add --name git-commit-and-push-content https://github.com/Pascalmh/kirby-git-commit-and-push-content.git site/plugins/git-commit-and-push-content
cd site/plugins/
git submodule update --init --recursive
```

Or: Put all the files into your `site/plugins/git-commit-and-push-content/` folder. If the `git-commit-and-push-content/` plugin folder doesn't exist then create it.

### Options

You can use the following [Options](http://getkirby.com/docs/advanced/options) - make use of kirbys [Multi-environment setup](http://getkirby.com/blog/multi-environment-setup).

#### gcapc-branch
Type: `String`
Default value: `'master'`

branch name to be checked out

#### gcapc-pull
Type: `Boolean`
Default value: `true`
 
Pull remote changes first?

#### gcapc-commit
Type: `Boolean`
Default value: `true`
 
Commit your changes?

#### gcapc-push
Type: `Boolean`
Default value: `true`
 
Push your changes to remote?

#### gcapc-gitBin
Type: `String`
Default value: `''`

Sets the location where git can be found 

[See Git.php](http://kbjr.github.io/Git.php/) `void Git::set_bin ( string $path )`

#### gcapc-windowsMode
Type: `Boolean`
Default value: `false`

[See Git.php](http://kbjr.github.io/Git.php/) `void Git::windows_mode ( void )`

## Author

Pascal 'Pascalmh' Küsgen <http://pascalmh.de>
