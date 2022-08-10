<?php
return [
    'label' => 'Git Content',
    'icon'  => 'sitemap',
    'menu'  => true,
    'link'  => 'git-content',
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


                $status = $git->status();
                return [
                    'component' => 'git-content',
                    'title' => 'Git Content',
                    'props' => [
                        'log' => $logFormatted,
                        'branch' => $git->getCurrentBranch(),
                        'status' => $git->status(), // is associative array consisting of changed files and whether repo is ahead/behind to origin
                    ],
                ];
            }
        ],
    ],
];
