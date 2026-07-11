<div class="card" style="max-width: 400px; margin: 50px auto;">
    <h2><?= Lang::t('login.title') ?></h2>
    <br>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="/login">
        <div class="form-group">
            <label><?= Lang::t('login.login') ?>:</label>
            <input type="text" name="login" value="<?= htmlspecialchars($old_login) ?>" required autofocus>
        </div>

        <div class="form-group">
            <label><?= Lang::t('login.password') ?>:</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit" class="btn"><?= Lang::t('login.submit') ?></button>
    </form>
</div>