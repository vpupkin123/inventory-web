<div class="card" style="max-width: 400px; margin: 50px auto;">
    <h2><?= Lang::t('auth.change_password') ?></h2>
    <br>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="/auth/change-password">
        <div class="form-group">
            <label><?= Lang::t('auth.current_password') ?>:</label>
            <input type="password" name="current_password" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
        </div>
        <div class="form-group">
            <label><?= Lang::t('auth.new_password') ?>:</label>
            <input type="password" name="new_password" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
        </div>
        <div class="form-group">
            <label><?= Lang::t('auth.confirm_new_password') ?>:</label>
            <input type="password" name="confirm_new_password" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
        </div>
        <br>
        <button type="submit" class="btn"><?= Lang::t('auth.change_password') ?></button>
        <a href="/dashboard" class="btn" style="background: #6c757d;"><?= Lang::t('common.cancel') ?></a>
    </form>
</div>