<div class="card">
    <h1><?= Lang::t('dashboard.welcome') ?>, <?= htmlspecialchars($user['name']) ?>!</h1>
    <p><?= Lang::t('dashboard.your_role') ?>: <strong><?= htmlspecialchars($user['role']) ?></strong></p>
    <br>
    <p><?= Lang::t('dashboard.next_steps') ?></p>
    <ul style="margin-left: 20px; margin-top: 10px;">
        <li><?= Lang::t('dashboard.upload_reports') ?></li>
        <li><?= Lang::t('dashboard.view_computers') ?></li>
        <li><?= Lang::t('dashboard.manage_users') ?></li>
    </ul>
</div>