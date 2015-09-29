<?php

require_once('Git.php/Git.php');

/**
 * Compose Commit message, appends " by Username"
 *
 * @param string $commitMessage
 *
 * @return false
 */
function gitCommit($commitMessage)
{
    $debugMode = c::get('debug', false);
    $branch = c::get('gcapc-branch', 'master');
    $pull = c::get('gcapc-pull', true);
    $push = c::get('gcapc-push', true);
    $commit = c::get('gcapc-commit', true);
    $gitBin = c::get('gcapc-gitBin', '');
    $windowsMode = c::get('gcapc-windowsMode', false);

    /*
     * Setup git environment
     */
    if ($windowsMode) {
        Git::windows_mode();
    }
    if ($gitBin) {
        Git::set_bin($gitBin);
    }

    $repo = Git::open('../content');

    if ($debugMode) {
        if (!$repo->test_git()) {
            echo 'git could not be found or is not working properly. ' . Git::get_bin();
            exit;
        }

        if (!Git::is_repo($repo)) {
            echo '$repo is not an instance of GitRepo';
            exit;
        }
    }

    /*
     * Git Pull, Commit and Push
     */
    if ($pull) {
        $repo->checkout($branch);
        $repo->pull('origin', $branch);
    }
    if ($commit) {
        $repo->add('.');
        $repo->commit($commitMessage . ' by ' . site()->user());
    }
    if ($push) {
        $repo->push('origin', $branch);
    }

    return false;
}
