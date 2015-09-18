<?php
/**
* Compose Commit message, appends " by Username"
*
* @param string $commitMessage
* @return false
*/
function gitCommit($commitMessage) {
    exec(
        'cd ../content/ && ' .
        'git add -A &&' .
        'git commit -m "' . $commitMessage . ' by ' . site()->user() . '"' .
        (
            server::get('SERVER_ADDR') != "localhost"
                ? ' && git push "ext::ssh -i ' .
                    c::get('gcapcSshKeyPath') . ' ' .
                    c::get('gcapcGitServer') . ' %S ' .
                    c::get('gcapcGitRepository') . '"'
                : ''
        ),
        $output
    );

    return false;
}
