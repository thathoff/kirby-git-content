<?php

use Kirby\Cms\Pages;
use Kirby\Toolkit\Str;
use Thathoff\GitContent\KirbyGit;
use Thathoff\GitContent\KirbyGitHelper;

return [
    'label' => option('thathoff.git-content.menuLabel', 'Git Content'),
    'icon'  => option('thathoff.git-content.menuIcon', 'sitemap'),
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
            'load' => function () {
                $pageId = get('page');
                $files = KirbyGit::parseFilesData(get('files'));

                $pageTitle = null;
                $page = null;
                $message = '';

                if ($pageId === 'site') {
                    $pageTitle = 'Site';
                } elseif ($page = $pageId ? kirby()->page($pageId) : null) {
                    $pageTitle = $page->title();
                }

                if ($pageTitle) {
                    $message = 'Update ' . $pageTitle;
                }

                $filesField = [
                    'label' => 'Changes',
                    'type' => 'info',
                    'text' => 'You are about to commit changes to <strong>all files</strong>.'
                ];

                if (count($files) > 0) {
                    // show files with git status codes in dialog (e.g. M site.txt)
                    $filesField['text'] = implode('<br>', array_map(function ($file) {
                        $code = trim(htmlspecialchars($file['code']));

                        // pad code to 3 characters
                        $code = str_pad($code, 3, ' ');

                        // replace spaces with non-breaking spaces
                        $code = str_replace(' ', '&nbsp;', $code);

                        return $code . ' ' . htmlspecialchars($file['filename']);
                    }, $files));
                    $filesField['theme'] = 'code';
                    $filesField['help'] = "Refer to the <a target='_blank' href='https://git-scm.com/docs/git-status#_short_format'>Git documentation</a>
              on how to interpret the status codes.";
                }

                return [
                    'component' => 'k-form-dialog',
                    'props' => [
                        'fields' => [
                            'title' => [
                                'label'    => 'Message',
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
                            'filesInfo' => $filesField,
                            'files' => [
                                'type' => 'hidden',
                            ]
                        ],
                        'value' => [
                            'title' => $message,
                            'description' => '',
                            'files' => json_encode($files),
                        ],
                        'size' => 'large'
                    ],
                ];
            },
            'submit' => function () {
                // build commit message
                $message = get('title');
                $description = get('description');
                if ($description) {
                    $message .= "\n\n" . $description;
                }

                $git = new KirbyGitHelper();

                // if files are provided, only add those files
                $files = KirbyGit::parseFilesData(get('files'));
                if (count($files) > 0) {
                    $git->addFile(array_map(fn ($file) => $file['filename'], $files));
                    $git->commit($message, null, $git->getAuthorString());
                    return true;
                }

                // if no files are provided, add all files
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
                        $user = null;

                        if ($userObject = KirbyGit::userByEmail($entry['email'])) {
                            $user = [
                                'username' => $userObject->username(),
                                'id' => $userObject->id(),
                                'avatar' => $userObject->avatar()?->resize(200)->url(),
                            ];
                        }

                        return [
                            'hash'    => $entry['hash'],
                            'message' => $entry['message'],
                            'date'    => $entry['date']->format(DateTime::ISO8601),
                            'author'  => $entry['author'],
                            'user'    => $user,
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
                        'status' => $git->statusByPages(), // is associative array consisting of changed files and whether repo is ahead/behind to origin
                    ],
                ];
            }
        ],
    ],
];
