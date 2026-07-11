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
            <label><?= Lang::t('users.login') ?>:</label>
            <input type="text" name="login" value="<?= htmlspecialchars($user['login'] ?? '') ?>" <?= $user ? 'disabled style="background: #eee;"' : 'required' ?>>
        </div>

        <div class="form-group">
            <label><?= Lang::t('users.password') ?>:</label>
            <input type="password" name="password" <?= !$user ? 'required' : '' ?>>
            <?php if ($user): ?>
                <small style="color: #666;"><?= Lang::t('users.password_hint') ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Last Name:</label>
            <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>First Name:</label>
            <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Middle Name:</label>
            <input type="text" name="middle_name" value="<?= htmlspecialchars($user['middle_name'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label><?= Lang::t('users.role') ?>:</label>
            <select name="role" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                <option value="admin" <?= ($user['role'] ?? '') === 'admin' ? 'selected' : '' ?>><?= Lang::t('users.role_admin') ?></option>
                <option value="editor" <?= ($user['role'] ?? '') === 'editor' ? 'selected' : '' ?>><?= Lang::t('users.role_editor') ?></option>
                <option value="viewer" <?= ($user['role'] ?? '') === 'viewer' ? 'selected' : '' ?>><?= Lang::t('users.role_viewer') ?></option>
                <option value="none" <?= ($user['role'] ?? '') === 'none' ? 'selected' : '' ?>><?= Lang::t('users.role_none') ?></option>
            </select>
        </div>

        <button type="submit" class="btn"><?= Lang::t('users.save') ?></button>
        <a href="/users" class="btn" style="background: #6c757d;"><?= Lang::t('common.cancel') ?></a>
    </form>
</div>