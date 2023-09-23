<?php

use Thathoff\GitContent\KirbyGitHelper;

return [
    'label' => 'Git Content',
    'icon'  => 'sitemap',
    'menu'  => true,
    'link'  => 'git-content',
    'dialogs' => [
        'git-content.revert' => [
            'pattern' => 'git-content/revert',
            'load' => fn () => [
                'component' => 'k-remove-dialog',
                'props' => [
                    'text' => "Are you sure you want to revert all changes?<br><br>⚠️ This cannot be undone.",
                    'submitButton' => 'Revert changes',
                    'icon' => 'undo',
                ]
            ],
            'submit' => function () {
                $git = new KirbyGitHelper();
                $git->reset();
                $git->clean();

                return true;
            }
        ],
        'git-content.commit' => [
            'pattern' => 'git-content/commit',
            'load' => fn () => [
                'component' => 'k-form-dialog',
                'props' => [
                    'fields' => [
                        'title' => [
                            'label'    => "Title",
                            'type'     => 'text',
                            'counter'  => true,
                            'maxlength' => 72,
                            'required' => true,
                        ],
                        'description' => [
                            'label'    => "Description",
                            'type'     => 'textarea',
                            'buttons'  => false,
                            'required' => false,
                        ],
                    ],
                    'size' => 'large'
                ]
            ],
            'submit' => function () {
                $message = get('title');
                $description = get('description');

                if ($description) {
                    $message .= "\n\n" . $description;
                }

                $git = new KirbyGitHelper();
                $git->addAll();
                $git->commit($message, null, $git->getAuthorString());

                return true;
            }
        ],
        'git-content.switchBranch' => [
            'pattern' => 'git-content/branch',
            'load' => function () {
                $git = new KirbyGitHelper();
                $branches = $git->getBranches();
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

                $logFormatted = array_map(
                    function ($entry) {
                        return [
                            'hash'    => $entry['hash'],
                            'message' => $entry['message'],
                            'date'    => $entry['date']->format(DateTime::ISO8601),
                            'author'  => $entry['author'],
                            'email'  => $entry['email'],
                        ];
                    },
                    $git->log()
                );

                return [
                    'component' => 'git-content',
                    'title' => 'Git Content',
                    'props' => [
                        'disableBranchManagement' => (bool)option('thathoff.git-content.disableBranchManagement', false),
                        'log' => $logFormatted,
                        'helpText' => option('thathoff.git-content.helpText'),
                        'branch' => $git->getCurrentBranch(),
                        'status' => $git->status(), // is associative array consisting of changed files and whether repo is ahead/behind to origin
                    ],
                ];
            }
        ],
    ],
];
