<div class="card">
    <h2><?= Lang::t('report.upload_title') ?></h2>
    <br>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="/reports/upload" enctype="multipart/form-data">
        <div class="form-group">
            <label><?= Lang::t('report.select_file') ?>:</label>
            <input type="file" name="json_file" accept=".json" required>
        </div>

        <button type="submit" class="btn"><?= Lang::t('report.upload_button') ?></button>
    </form>
</div>