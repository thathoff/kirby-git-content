<?php
require(__DIR__ . DS . 'helpers.php');

/*
* Pages
*/
kirby()->hook('panel.page.create', function ($page) {
    gitCommit('create(page): ' . $page->uri());
});
kirby()->hook('panel.page.update', function ($page) {
    gitCommit('update(page): ' . $page->uri());
});
kirby()->hook('panel.page.delete', function ($page) {
    gitCommit('delete(page): ' . $page->uri());
});
kirby()->hook('panel.page.sort', function ($page) {
    gitCommit('sort(page): ' . $page->uri());
});
kirby()->hook('panel.page.hide', function ($page) {
    gitCommit('hide(page): ' . $page->uri());
});
kirby()->hook('panel.page.move', function ($page) {
    gitCommit('move(page): ' . $page->uri());
});

/*
* File
*/
kirby()->hook('panel.file.move', function ($file) {
    gitCommit('move(file): ' . $file->page()->uri() . '/' . $file->filename());
});
kirby()->hook('panel.file.upload', function ($file) {
    gitCommit('upload(file): ' . $file->page()->uri() . '/' . $file->filename());
});
kirby()->hook('panel.file.replace', function ($file) {
    gitCommit('replace(file): ' . $file->page()->uri() . '/' . $file->filename());
});
kirby()->hook('panel.file.rename', function ($file) {
    gitCommit('rename(file): ' . $file->page()->uri() . '/' . $file->filename());
});
kirby()->hook('panel.file.update', function ($file) {
    gitCommit('update(file): ' . $file->page()->uri() . '/' . $file->filename());
});
kirby()->hook('panel.file.sort', function ($file) {
    gitCommit('sort(file): ' . $file->page()->uri() . '/' . $file->filename());
});
kirby()->hook('panel.file.delete', function ($file) {
    gitCommit('delete(file): ' . $file->page()->uri() . '/' . $file->filename());
});

/*
* User
*/
kirby()->hook('panel.user.create', function ($user) {
    gitCommit('create(user): ' . $user->username());
});
kirby()->hook('panel.user.update', function ($user) {
    gitCommit('update(user): ' . $user->username());
});
kirby()->hook('panel.user.delete', function ($user) {
    gitCommit('delete(user): ' . $user->username());
});

/*
* Avatar
*/
kirby()->hook('panel.avatar.upload', function ($avatar) {
    gitCommit('upload(avatar): ' . $avatar->filename());
});
kirby()->hook('panel.avatar.delete', function ($avatar) {
    gitCommit('delete(avatar): ' . $avatar->filename());
});
