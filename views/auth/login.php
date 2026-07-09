<div class="card" style="max-width: 400px; margin: 50px auto;">
    <h2>Login</h2>
    <br>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="/login">
        <div class="form-group">
            <label>Login:</label>
            <input type="text" name="login" value="<?= htmlspecialchars($old_login) ?>" required autofocus>
        </div>

        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit" class="btn">Login</button>
    </form>
</div>