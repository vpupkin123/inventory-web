<div class="card" style="max-width: 400px; margin: 50px auto;">
    <h2><?= Lang::t('change_password.title') ?></h2>
    <p style="color: #666; margin-bottom: 20px;"><?= Lang::t('change_password.description') ?></p>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="/change-initial-password">
        <div class="form-group">
            <label><?= Lang::t('change_password.new_password') ?>:</label>
            <input type="password" name="new_password" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
        </div>

        <div class="form-group">
            <label><?= Lang::t('change_password.confirm_password') ?>:</label>
            <input type="password" name="confirm_password" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
        </div>

        <button type="submit" class="btn"><?= Lang::t('change_password.submit') ?></button>
    </form>
</div>