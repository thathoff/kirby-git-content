<style><?= f::read(__DIR__ . '/assets/css/git-commit-and-push-content.css'); ?></style>

<div class="gcapc-info">
    <ul class="gcapc-info-list">
        <?php if (c::get('gcapc-branch') && c::get('gcapc-branch') != ''): ?>
            <li>Branch: <?= c::get('gcapc-branch') ?></li>
        <?php endif ?>
        <li>Commit on change: <i class="icon fa fa-<?= c::get('gcapc-commit', false) ? 'check' : 'times' ?>"></i></li>
        <li>Pull on change: <i class="icon fa fa-<?= c::get('gcapc-pull', false) ? 'check' : 'times' ?>"></i></li>
        <li>Push on change: <i class="icon fa fa-<?= c::get('gcapc-push', false) ? 'check' : 'times' ?>"></i></li>
    </ul>
</div>

<div class="gcapc-status"></div>

<script>
    <?php
    echo 'window.gcapcSettings = "' . kirby()->urls()->index() . '/gcapc/";';
    echo f::read(__DIR__ . '/assets/js/git-commit-and-push-content.js');
    ?>
</script>
