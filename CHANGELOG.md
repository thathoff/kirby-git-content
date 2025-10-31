<a name="5.4.0"></a>
# [5.4.0](https://github.com/thathoff/kirby-git-content/compare/v5.3.0...v5.4.0) (2025-11-01)

### Features
- Add support for buttons configuration (Thanks to @chrfickinger)
- Add fetch, reset and remove index lock buttons
- Add webhook for resetting the local repository to the remote repository (requires secret to be set)

### Bugfixes
- Fix git log parsing for messages containing pipe characters (See #118)

<a name="5.3.0"></a>
# [5.3.0](https://github.com/thathoff/kirby-git-content/compare/v5.2.0...v5.3.0) (2025-05-24)

### Features
- Add support for Kirby 5 (Thanks to @bastianallgeier)
- Display author in list of commits (Thanks to @hariom147)

<a name="5.2.0"></a>
# [5.2.0](https://github.com/thathoff/kirby-git-content/compare/v5.1.0...v5.2.0) (2023-12-09)

### Features
- Add support for Kirby 4
- Configurable panel menu item

<a name="5.1.0"></a>
# [5.1.0](https://github.com/thathoff/kirby-git-content/compare/v5.0.2...v5.1.0) (2023-10-16)

### Features
- Support git submodule installation (see #74, thanks to @CHE1RON)
- Enhanced panel UI with support to switch and create branches, manual commits and resets
- Add before and after hooks for push and pull
- Add optional help text in the UI

<a name="5.0.2"></a>
# [5.0.2](https://github.com/thathoff/kirby-git-content/compare/v5.0.1...v5.0.2) (2023-06-13)

### Bugfixes
- fix PHP 8.1 compatibility (see #98, thanks to @CHE1RON)


<a name="5.0.1"></a>
# [5.0.1](https://github.com/thathoff/kirby-git-content/compare/v5.0.0...v5.0.1) (2022-08-10)

### Bugfixes
- restore php 7.4 support

<a name="5.0.0"></a>
# [5.0.0](https://github.com/thathoff/kirby-git-content/compare/v4.1.0...v5.0.0) (2022-08-10)

### Features
- only add modified files with `git add` instead of iterating over entire directory tree (`git add .`) (see #90)
- Add overview panel
  - see remote synchronization status (like `git status`)
  - see modified files that have not been committed (like `git status`)
  - push and pull using buttons (see #85)
- show plugin information in Kirby plugin overview

### Bugfixes
- catch git errors that are caused by race conditions (see #84)
- inform about exclusion of Kirby .lock files in README.md (see #81)

### BREAKING CHANGES
- Plugin only supports Kirby 3.6 and upwards and git 2.24 and upwards


<a name="4.1.0"></a>
# [4.1.0](https://github.com/thathoff/kirby-git-content/compare/v4.0.1...4.1.0) (2021-02-19)

## Features
- Add the ability to disable the plugin via the config (Thanks @mrunkel)
- Add secret parameter to the webhooks (Thanks @mrunkel)


<a name="4.0.1"></a>
# [4.0.1](https://github.com/thathoff/kirby-git-content/compare/v4.0.0...v4.0.1) (2020-11-17)

### Bugfixes
- Setting path to git binary and enabling windows mode works again (thanks to @therephil)
- Fix committing when Kirby user has no name (thanks to @qwerdee, fixes #57)


<a name="4.0.0"></a>
# [4.0.0](https://github.com/thathoff/kirby-git-content/compare/v3.0.3...v4.0.0) (2020-10-25)

### Features
- finalize Maintainer switch (see Breaking Changes)
- finalize rename to GitContent (see Breaking Changes)
- Allow POST request to webhooks (thanks to @graphichavoc)
- Install into plugins folder (thanks to @s3ththompson)
- Switch to `coyl/git` PHP git library
- allow configuration of commit message (thanks to @JonasDoebertin)

### BREAKING CHANGES:
- Name Changed to Git Content
- Config options changed (`blankogmbh.gcapc` to `thathoff.git-content`)
- Webhook-URLS changed: `/gcapc/(push|pull)` changed to `/git-content/(push|pull)


<a name="3.0.3"></a>
# [3.0.3](https://github.com/thathoff/kirby-git-content/compare/v3.0.2...v3.0.3) (2020-07-12)

The maintainer has switched, be prepared for config option name changes in version 4 which will be released shortly.

### Bug Fixes

* Plugin works with Kirby 3.4 (#66)

<a name="3.0.2"></a>
# [3.0.2](https://github.com/thathoff/kirby-git-content/compare/v3.0.1...v3.0.2) (2020-03-06)

### Bug Fixes

* Only handle files attached to pages


<a name="3.0.1"></a>
# [3.0.1](https://github.com/thathoff/kirby-git-content/compare/v3.0.0...v3.0.1) (2019-09-24)

### Bug Fixes

* Fix hook for file:delete



<a name="3.0.0"></a>
# [3.0.0](https://github.com/thathoff/kirby-git-content/compare/v2.2.2...v3.0.0) (2019-09-24)


### Features

* add support for kirby 3 ([f4393b8](https://github.com/thathoff/kirby-git-content/commit/f4393b8))
* commit by default when plugin is installed ([48cd099](https://github.com/thathoff/kirby-git-content/commit/48cd099))
* install Git.php with composer only ([6d1b9d3](https://github.com/thathoff/kirby-git-content/commit/6d1b9d3))
* only log errors by default ([3f4e771](https://github.com/thathoff/kirby-git-content/commit/3f4e771))
* use users email and name as commit author ([ebcba17](https://github.com/thathoff/kirby-git-content/commit/ebcba17))


### BREAKING CHANGES

* The commit option now defaults to true
* Support for Kirby 2 and the panel widget has been removed.
* Remove support for installation with git submodules



<a name="2.2.2"></a>
## [2.2.2](https://github.com/thathoff/kirby-git-content/compare/v2.2.1...v2.2.2) (2018-01-24)


### Bug Fixes

* rename main file tow work with new install method ([2e96543](https://github.com/thathoff/kirby-git-content/commit/2e96543))



<a name="2.2.1"></a>
## [2.2.1](https://github.com/thathoff/kirby-git-content/compare/v2.2.0...v2.2.1) (2018-01-24)




<a name="2.2.0"></a>
# [2.2.0](https://github.com/thathoff/kirby-git-content/compare/v2.1.0...v2.2.0) (2017-08-21)


### Features

* track changes made to site.txt ([7aaf3be](https://github.com/thathoff/kirby-git-content/commit/7aaf3be))



<a name="2.1.0"></a>
# [2.1.0](https://github.com/thathoff/kirby-git-content/compare/v2.0.1...v2.1.0) (2017-03-29)


### Bug Fixes

* init repo if no branch is specified ([f69515e](https://github.com/thathoff/kirby-git-content/commit/f69515e))

### Features

* do not initialise repository twice ([f32e90e](https://github.com/thathoff/kirby-git-content/commit/f32e90e))



<a name="2.0.2"></a>
## [2.0.2](https://github.com/thathoff/kirby-git-content/compare/v2.0.1...v2.0.2) (2017-03-29)


### Bug Fixes

* init repo if no branch is specified ([f69515e](https://github.com/thathoff/kirby-git-content/commit/f69515e))

### Features

* do not initialise repository twice ([f32e90e](https://github.com/thathoff/kirby-git-content/commit/f32e90e))



<a name="2.0.1"></a>
## [2.0.1](https://github.com/thathoff/kirby-git-content/compare/1.5.0...v2.0.1) (2017-03-29)


### Bug Fixes

* allow usage of currently checked out branch ([25b57a4](https://github.com/thathoff/kirby-git-content/commit/25b57a4))



<a name="2.0.0"></a>
# [2.0.0](https://github.com/thathoff/kirby-git-content/compare/1.4.2...v2.0.0) (2017-02-20)


### Bug Fixes

* use slashes for url, not DS ([d4e24b7](https://github.com/thathoff/kirby-git-content/commit/d4e24b7))

### Features

* allow usage of currently checked out branch ([033d10f](https://github.com/thathoff/kirby-git-content/commit/033d10f))


### BREAKING CHANGES

* the default branch is not the master anymore

<a name="1.5.0"></a>
# [1.5.0](https://github.com/thathoff/kirby-git-content/compare/1.4.2...v1.5.0) (2017-02-24)


### Features

* implemented `gcapc-path` configuration ([151e6d1](https://github.com/thathoff/kirby-git-content/commit/151e6d1))
* improved error handling ([ff36ea4](https://github.com/thathoff/kirby-git-content/commit/ff36ea4))



<a name="1.4.2"></a>
## [1.4.2](https://github.com/thathoff/kirby-git-content/compare/v1.4.1...v1.4.2) (2017-02-09)




<a name="1.4.1"></a>
## [1.4.1](https://github.com/thathoff/kirby-git-content/compare/v1.4.0...v1.4.1) (2017-02-08)


### Bug Fixes

* push/pull success messages ([76bc9b9](https://github.com/thathoff/kirby-git-content/commit/76bc9b9))
* require Git.php ([d319cbd](https://github.com/thathoff/kirby-git-content/commit/d319cbd))



<a name="1.4.0"></a>
# [1.4.0](https://github.com/thathoff/kirby-git-content/compare/v1.3.0...v1.4.0) (2017-01-17)


### Features

* use composer dependency if applicable ([bd45077](https://github.com/thathoff/kirby-git-content/commit/bd45077))



<a name="1.3.0"></a>
# [1.3.0](https://github.com/thathoff/kirby-git-content/compare/v1.2.0...v1.3.0) (2016-07-10)


### Features

* add panel widget ([6a6e3fd](https://github.com/thathoff/kirby-git-content/commit/6a6e3fd))



<a name="1.2.0"></a>
# [1.2.0](https://github.com/thathoff/kirby-git-content/compare/v1.1.1...v1.2.0) (2016-06-27)


### Features

* add cron-hooks-enabled option ([2118b28](https://github.com/thathoff/kirby-git-content/commit/2118b28))



<a name="1.1.1"></a>
## [1.1.1](https://github.com/thathoff/kirby-git-content/compare/v1.1.0...v1.1.1) (2016-04-21)


### Bug Fixes

* make branch available for git pull and git push ([d1e17c3](https://github.com/thathoff/kirby-git-content/commit/d1e17c3))



<a name="1.1.0"></a>
# [1.1.0](https://github.com/thathoff/kirby-git-content/compare/v1.0.0...v1.1.0) (2016-04-21)


### Features

* add option to manually push and pull content ([833960f](https://github.com/thathoff/kirby-git-content/commit/833960f))



<a name="1.0.0"></a>
# [1.0.0](https://github.com/thathoff/kirby-git-content/compare/v0.0.7...v1.0.0) (2015-12-17)




<a name="0.0.7"></a>
## [0.0.7](//compare/v0.0.6...v0.0.7) (2015-12-11)


### Features

* make plugin opt-in instead of opt-out
* git commit -A to include deletions

<a name="0.0.6"></a>
## [0.0.6](//compare/v0.0.5...v0.0.6) (2015-10-29)


### Bug Fixes

* remove User and Avatar hook 1582ddc



<a name="0.0.5"></a>
## [0.0.5](//compare/v0.0.4...v0.0.5) (2015-09-29)


### Features

* add debug information 1486a6e
* add instructions how to use multi git user env d1e699e



<a name="v0.0.4"></a>
## v0.0.4 - 2015-09-23

### Bug Fixes

* **git/environment:** use $gitBin if set


<a name="v0.0.3"></a>
## v0.0.3 - 2015-09-23

### Refactoring

* use [Git.php](https://github.com/kbjr/Git.php) as php git library


<a name="v0.0.2"></a>
## v0.0.2 - 2015-09-21

### Refactoring

* use Multi-environment setup, provide more variables


<a name="v0.0.1"></a>
## v0.0.1 - 2015-09-18
+ Initial release
