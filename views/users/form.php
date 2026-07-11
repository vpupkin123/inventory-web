<div class="card" style="max-width: 600px;">
    <h2><?= $title ?></h2>
    <br>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= $user ? '/users/update' : '/users/store' ?>">
        <?php if ($user): ?>
            <input type="hidden" name="id" value="<?= $user['id'] ?>">
        <?php endif; ?>

        <div class="form-group">
            <label><?= Lang::t('users.last_name') ?>:</label>
            <input type="text" name="last_name" id="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required <?= !$user ? 'oninput="generateLogin()"' : '' ?>>
        </div>

        <div class="form-group">
            <label><?= Lang::t('users.first_name') ?>:</label>
            <input type="text" name="first_name" id="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required <?= !$user ? 'oninput="generateLogin()"' : '' ?>>
        </div>

        <div class="form-group">
            <label><?= Lang::t('users.middle_name') ?>:</label>
            <input type="text" name="middle_name" id="middle_name" value="<?= htmlspecialchars($user['middle_name'] ?? '') ?>