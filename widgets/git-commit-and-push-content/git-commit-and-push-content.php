<?php
return array(
    'title' => 'GCAPC',
    'options' => array(
        array(
            'text' => 'Pull',
            'icon' => 'arrow-down',
            'link' => kirby()->urls()->index() . '/gcapc/pull',
        ),
        array(
            'text' => 'Push',
            'icon' => 'arrow-up',
            'link' => kirby()->urls()->index() . '/gcapc/push',
        )
    ),
    'html' => function () {
        return tpl::load(__DIR__ . DS . 'git-commit-and-push-content.html.php');
    }
);
