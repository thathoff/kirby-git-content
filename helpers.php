<?php

require_once('Git.php/Git.php');

/**
 * Compose Commit message, appends " by Username"
 *
 * @param string $commitMessage
 * @return false
 */
function gitCommit($commitMessage) {
    $branch = c::get('gcapc-branch', 'master');
    $pull = c::get('gcapc-pull', true);
    $push = c::get('gcapc-push', true);
    $commit = c::get('gcapc-commit', true);
    $gitBin = c::get('gcapc-gitBin', '');
    $windowsMode = c::get('gcapc-windowsMode', false);

    /*
     * Setup git environment
     */
    if($windowsMode) {
        Git::windows_mode();
    }
    if ($gitBin) {
        Git::set_bin('/usr/local/bin/git');
    }

    $repo = Git::open('../content');

    // We are in the panel-Folder - go into content-Folder
    chdir('../content');

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

    // Go back into panel-Folder
    chdir('../panel');

    return false;
}
