<?php

namespace Thathoff\GitContent;

use Kirby\Exception\PermissionException;
use Kirby\Panel\Panel;

class Permissions
{
    /**
     * Returns which actions are enabled for the current user, based on the `buttons` and
     * `disableBranchManagement` options and the `thathoff.git-content` role permissions.
     *
     * @return array<string, bool>
     */
    public static function buttons(): array
    {
        $defaultButtons = [
            'revert' => true,
            'reset' => false,
            'commit' => true,
            'pull' => true,
            'push' => true,
            'fetch' => true,
            'createBranch' => true,
            'switchBranch' => true,
            'removeIndexLock' => true,
        ];

        $configuredButtons = option('thathoff.git-content.buttons', []);
        if (!is_array($configuredButtons)) {
            $configuredButtons = [];
        }

        $normalizedButtons = [];
        foreach ($configuredButtons as $key => $value) {
            if (array_key_exists($key, $defaultButtons)) {
                $normalizedButtons[$key] = (bool)$value;
            }
        }

        $buttons = array_merge($defaultButtons, $normalizedButtons);

        if ($user = kirby()->user()) {
            $rolePermissions = $user->role()->permissions();

            foreach ($buttons as $key => $isEnabled) {
                if ($rolePermissions->for('thathoff.git-content', $key, true) === false) {
                    $buttons[$key] = false;
                }
            }
        }

        if (option('thathoff.git-content.disableBranchManagement', false)) {
            $buttons['createBranch'] = false;
            $buttons['switchBranch'] = false;
        }

        return $buttons;
    }

    /**
     * Makes sure the current user may access the git content area and, if given, run the action.
     * The buttons only hide the actions in the panel, so the endpoints need to check this as well.
     *
     * @throws PermissionException
     */
    public static function check(?string $action = null): void
    {
        Panel::firewall(kirby()->user(), 'git-content');

        if ($action !== null && (static::buttons()[$action] ?? false) !== true) {
            // pass the message as string, named arguments are only supported since Kirby 5
            throw new PermissionException('You are not allowed to use the "' . $action . '" action.');
        }
    }
}
