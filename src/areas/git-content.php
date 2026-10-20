<?php

use Thathoff\GitContent\KirbyGitHelper;
use Thathoff\GitContent\Permissions;

return [
    'label' => option('thathoff.git-content.menuLabel', 'Git Content'),
    'icon'  => option('thathoff.git-content.menuIcon', 'sitemap'),
    'menu'  => true,
    'link'  => 'git-content',
    'dialogs' => [
        'git-content.reset' => [
            'pattern' => 'git-content/reset',
            'load' => fn () => [
                'component' => 'k-remove-dialog',
                'props' => [
                    'text' => "Are you sure you want to reset the content folder?<br><br>⚠️ Will remove all local changes and reset to the remote branch.",
                    'submitButton' => 'Reset',
                    'icon' => 'undo',
                ]
            ],
            'submit' => function () {
                Permissions::check('reset');

                $git = new KirbyGitHelper();
                $git->resetToOrigin();
                $git->clean();

                return true;
            }
        ],
        'git-content.switchBranch' => [
            'pattern' => 'git-content/branch',
            'load' => function () {
                $git = new KirbyGitHelper();
                $branches = $git->getBranches() ?? [];
                $currentBranch = $git->getCurrentBranch();

                $branchesOptions = [];

                foreach ($branches as $branch) {
                    $branchesOptions[] = [
                        'value' => $branch,
                        'text' => $branch,
                    ];
                }

                return [
                    'component' => 'k-form-dialog',
                    'props' => [
                        'fields' => [
                            'branch' => [
                                'label'     => "Branch",
                                'type'      => 'select',
                                'options'   => $branchesOptions,
                                'empty'     => false,
                                'required'  => true,
                                'help'      => "Switching branches might take a while."
                            ]
                        ],
                        'value' => [
                            'branch' => $currentBranch,
                        ],
                        'submitButton' => 'Switch Branch',
                    ],
                ];
            },
            'submit' => function () {
                Permissions::check('switchBranch');

                $branchName = get('branch');

                $git = new KirbyGitHelper();
                $git->checkout($branchName);
                return true;
            }
        ],
        'git-content.createBranch' => [
            'pattern' => 'git-content/create-branch',
            'load' => fn () => [
                'component' => 'k-form-dialog',
                'props' => [
                    'fields' => [
                        'branch' => [
                            'label'     => "Branch",
                            'type'      => 'slug',
                            'empty'     => false,
                            'required'  => true,
                        ]
                    ],
                    'submitButton' => 'Create Branch',
                ],
            ],
            'submit' => function () {
                Permissions::check('createBranch');

                $branchName = get('branch');

                $git = new KirbyGitHelper();
                $git->createBranch($branchName);
                return true;
            }
        ],
    ],
    'views' => [
        [
            'pattern' => 'git-content',
            'action'  => function () {
                $git = new Thathoff\GitContent\KirbyGitHelper();

                $buttons = Permissions::buttons();

                $logFormatted = array_map(
                    function ($entry) {
                        return [
                            'hash'    => $entry['hash'],
                            'message' => $entry['message'],
                            'date'    => $entry['date']?->format(DateTimeInterface::ATOM),
                            'author'  => $entry['author'],
                            'email'  => $entry['email'],
                        ];
                    },
                    $git->log()
                );

                $disableBranchManagement = (bool)option('thathoff.git-content.disableBranchManagement', false);

                return [
                    'component' => 'git-content',
                    'title' => 'Git Content',
                    'props' => [
                        'disableBranchManagement' => $disableBranchManagement,
                        'buttons' => $buttons,
                        'log' => $logFormatted,
                        'helpText' => option('thathoff.git-content.helpText'),
                        'branch' => $git->getCurrentBranch(),
                        'hasIndexLock' => $git->hasIndexLock(),
                        'status' => $git->status(), // is associative array consisting of changed files and whether repo is ahead/behind to origin
                    ],
                ];
            }
        ],
    ],
];
