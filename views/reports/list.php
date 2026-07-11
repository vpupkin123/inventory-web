<div class="card">
    <h2><?= Lang::t('report.list_title') ?></h2>
    <br>

    <a href="/reports/upload" class="btn"><?= Lang::t('report.upload_new') ?></a>
    <br><br>

    <?php if (empty($reports)): ?>
        <p><?= Lang::t('report.no_reports') ?></p>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f4f4f4;">
                    <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('report.file_name') ?></th>
                    <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('report.uploaded_by') ?></th>
                    <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('report.uploaded_at') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($report['file_name']) ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <?= htmlspecialchars(trim($report['last_name'] . ' ' . $report['first_name'])) ?>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($report['uploaded_at']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>