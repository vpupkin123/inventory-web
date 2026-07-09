<div class="card" style="max-width: 400px; margin: 50px auto;">
    <h2>Change Password</h2>
    <p style="color: #666; margin-bottom: 20px;">You must change your password before continuing.</p>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="/change-password">
        <div class="form-group">
            <label>New Password:</label>
            <input type="password" name="new_password" required>
        </div>

        <div class="form-group">
            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" required>
        </div>

        <button type="submit" class="btn">Change Password</button>
    </form>
</div>