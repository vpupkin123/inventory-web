<div class="card">
    <h1>Welcome, <?= htmlspecialchars($user['name']) ?>!</h1>
    <p>Your role: <strong><?= htmlspecialchars($user['role']) ?></strong></p>
    <br>
    <p>This is the dashboard. Next steps:</p>
    <ul style="margin-left: 20px; margin-top: 10px;">
        <li>Upload JSON reports</li>
        <li>View computers list</li>
        <li>Manage users (admin only)</li>
    </ul>
</div>