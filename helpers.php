<?php
/**
* Compose Commit message, appends " by Username"
*
* @param string $commitMessage
* @return false
*/
function gitCommit($commitMessage) {
    $branch = c::get('gcapc-branch') || 'master';
    $pull = c::get('gcapc-pull') || true;
    $push = c::get('gcapc-push') || true;
    $commit = c::get('gcapc-commit') || true;
    $pushCommand = c::get('gcapc-pushCommand') || 'git push';

    exec(
        'cd ../content/' .
        ' && git checkout -b ' . $branch . '' .
        $pull ? ' && git pull' : '' .
        $commit ? ' && git add -A' : '' .
        $commit ? ' && git commit -m "' . $commitMessage . ' by ' . site()->user() . '"' : '' .
        $push ? ' && ' . $pushCommand : ''
    );

    return false;
}
