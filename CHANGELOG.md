<a name="3.0.3"></a>
# [3.0.3](https://github.com/blankogmbh/kirby-git-commit-and-push-content/compare/v3.0.2...v3.0.3) (2020-07-12)

The maintainer has switched, be prepared for config option name changes in version 4 which will be released shortly.

### Bug Fixes

* Plugin works with Kirby 3.4 (#66)

<a name="3.0.2"></a>
# [3.0.2](https://github.com/blankogmbh/kirby-git-commit-and-push-content/compare/v3.0.1...v3.0.2) (2020-03-06)

### Bug Fixes

* Only handle files attached to pages


<a name="3.0.1"></a>
# [3.0.1](https://github.com/blankogmbh/kirby-git-commit-and-push-content/compare/v3.0.0...v3.0.1) (2019-09-24)

### Bug Fixes

* Fix hook for file:delete



<a name="3.0.0"></a>
# [3.0.0](https://github.com/blankogmbh/kirby-git-commit-and-push-content/compare/v2.2.2...v3.0.0) (2019-09-24)


### Features

* add support for kirby 3 ([f4393b8](https://github.com/blankogmbh/kirby-git-commit-and-push-content/commit/f4393b8))
* commit by default when plugin is installed ([48cd099](https://github.com/blankogmbh/kirby-git-commit-and-push-content/commit/48cd099))
* install Git.php with composer only ([6d1b9d3](https://github.com/blankogmbh/kirby-git-commit-and-push-content/commit/6d1b9d3))
* only log errors by default ([3f4e771](https://github.com/blankogmbh/kirby-git-commit-and-push-content/commit/3f4e771))
* use users email and name as commit author ([ebcba17](https://github.com/blankogmbh/kirby-git-commit-and-push-content/commit/ebcba17))


### BREAKING CHANGES

* The commit option now defaults to true
* Support for Kirby 2 and the panel widget has been removed.
* Remove support for installation with git submodules



<a name="2.2.2"></a>
## [2.2.2](https://github.com/blankogmbh/kirby-git-commit-and-push-content/compare/v2.2.1...v2.2.2) (2018-01-24)


### Bug Fixes

* rename main file tow work with new install method ([2e96543](https://github.com/blankogmbh/kirby-git-commit-and-push-content/commit/2e96543))



<a name="2.2.1"></a>
## [2.2.1](https://github.com/blankogmbh/kirby-git-commit-and-push-content/compare/v2.2.0...v2.2.1) (2018-01-24)




<a name="2.2.0"></a>
# [2.2.0](https://github.com/blankogmbh/kirby-git-commit-and-push-content/compare/v2.1.0...v2.2.0) (2017-08-21)


### Features

* track changes made to site.txt ([7aaf3be](https://github.com/blankogmbh/kirby-git-commit-and-push-content/commit/7aaf3be))



<a name="2.1.0"></a>
# [2.1.0](https://github.com/blankogmbh/kirby-git-commit-and-push-content/compare/v2.0.1...v2.1.0) (2017-03-29)


### Bug Fixes

* init repo if no branch is specified ([f69515e](https://github.com/blankogmbh/kirby-git-commit-and-push-content/commit/f69515e))

### Features

* do not initialise repository twice ([f32e90e](https://github.com/blankogmbh/kirby-git-commit-and-push-content/commit/f32e90e))



<a name="2.0.2"></a>
## [2.0.2](https://github.com/blankogmbh/kirby-git-commit-and-push-content/compare/v2.0.1...v2.0.2) (2017-03-29)


### Bug Fixes

* init repo if no branch is specified ([f69515e](https://github.com/blankogmbh/kirby-git-commit-and-push-content/commit/f69515e))

### Features

* do not initialise repository twice ([f32e90e](https://github.com/blankogmbh/kirby-git-commit-and-push-content/commit/f32e90e))



<a name="2.0.1"></a>
## [2.0.1](https://github.com/blankogmbh/kirby-git-commit-and-push-content/compare/1.5.0...v2.0.1) (2017-03-29)


### Bug Fixes

* allow usage of currently checked out branch ([25b57a4](https://github.com/blankogmbh/kirby-git-commit-and-push-content/commit/25b57a4))



<a name="2.0.0"></a>
# [2.0.0](https://github.com/blankogmbh/kirby-git-commit-and-push-content/compare/1.4.2...v2.0.0) (2017-02-20)


### Bug Fixes

* use slashes for url, not DS ([d4e24b7](https://github.com/blankogmbh/kirby-git-commit-and-push-content/commit/d4e24b7))

### Features

* allow usage of currently checked out branch ([033d10f](https://github.com/blankogmbh/kirby-git-commit-and-push-content/commit/033d10f))


### BREAKING CHANGES

* the default branch is not the master anymore

<a name="1.5.0"></a>
# [1.5.0](https://github.com/blankogmbh/kirby-git-commit-and-push-content/compare/1.4.2...v1.5.0) (2017-02-24)


### Features

* implemented `gcapc-path` configuration ([151e6d1](https://github.com/blankogmbh/kirby-git-commit-and-push-content/commit/151e6d1))
* improved error handling ([ff36ea4](https://github.com/blankogmbh/kirby-git-commit-and-push-content/commit/ff36ea4))



<a name="1.4.2"></a>
## [1.4.2](https://github.com/blankogmbh/kirby-git-commit-and-push-content/compare/v1.4.1...v1.4.2) (2017-02-09)




<a name="1.4.1"></a>
## [1.4.1](https://github.com/blankogmbh/kirby-git-commit-and-push-content/compare/v1.4.0...v1.4.1) (2017-02-08)


### Bug Fixes

* push/pull success messages ([76bc9b9](https://github.com/blankogmbh/kirby-git-commit-and-push-content/commit/76bc9b9))
* require Git.php ([d319cbd](https://github.com/blankogmbh/kirby-git-commit-and-push-content/commit/d319cbd))



<a name="1.4.0"></a>
# [1.4.0](https://github.com/blankogmbh/kirby-git-commit-and-push-content/compare/v1.3.0...v1.4.0) (2017-01-17)


### Features

* use composer dependency if applicable ([bd45077](https://github.com/blankogmbh/kirby-git-commit-and-push-content/commit/bd45077))



<a name="1.3.0"></a>
# [1.3.0](https://github.com/blankogmbh/kirby-git-commit-and-push-content/compare/v1.2.0...v1.3.0) (2016-07-10)


### Features

* add panel widget ([6a6e3fd](https://github.com/blankogmbh/kirby-git-commit-and-push-content/commit/6a6e3fd))



<a name="1.2.0"></a>
# [1.2.0](https://github.com/blankogmbh/kirby-git-commit-and-push-content/compare/v1.1.1...v1.2.0) (2016-06-27)


### Features

* add cron-hooks-enabled option ([2118b28](https://github.com/blankogmbh/kirby-git-commit-and-push-content/commit/2118b28))



<a name="1.1.1"></a>
## [1.1.1](https://github.com/blankogmbh/kirby-git-commit-and-push-content/compare/v1.1.0...v1.1.1) (2016-04-21)


### Bug Fixes

* make branch available for git pull and git push ([d1e17c3](https://github.com/blankogmbh/kirby-git-commit-and-push-content/commit/d1e17c3))



<a name="1.1.0"></a>
# [1.1.0](https://github.com/blankogmbh/kirby-git-commit-and-push-content/compare/v1.0.0...v1.1.0) (2016-04-21)


### Features

* add option to manually push and pull content ([833960f](https://github.com/blankogmbh/kirby-git-commit-and-push-content/commit/833960f))



<a name="1.0.0"></a>
# [1.0.0](https://github.com/blankogmbh/kirby-git-commit-and-push-content/compare/v0.0.7...v1.0.0) (2015-12-17)




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
