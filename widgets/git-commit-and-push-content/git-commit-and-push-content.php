<?php
return [
    'title' => 'GCAPC',
    'options' => [
        [
            'text' => 'Pull',
            'icon' => 'arrow-down',
            'link' => kirby()->urls()->index() . '/gcapc/pull',
        ],
        [
            'text' => 'Push',
            'icon' => 'arrow-up',
            'link' => kirby()->urls()->index() . '/gcapc/push',
        ]
    ],
    'html' => function () {
        return tpl::load(__DIR__ . DS . 'git-commit-and-push-content.html.php');
    }
];
