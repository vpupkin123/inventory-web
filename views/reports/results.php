<div class="card">
    <h2><?= Lang::t('report.results') ?></h2>
    <br>

    <?php
    $total = count($results['success']) + count($results['skipped']) + count($results['errors']);
    $totalComputers = 0;
    foreach ($results['success'] as $s) {
        $totalComputers += $s['computers'];
    }
    ?>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 25px;">
        <div class="card" style="text-align: center; border-left: 4px solid #28a745; margin: 0;">
            <h3 style="font-size: 2em; margin: 0; color: #28a745;"><?= count($results['success']) ?></h3>
            <p style="margin: 5px 0 0 0; color: #666;"><?= Lang::t('report.success_count') ?></p>
            <small style="color: #999;"><?= $totalComputers ?> computers</small>
        </div>
        
        <div class="card" style="text-align: center; border-left: 4px solid #ffc107; margin: 0;">
            <h3 style="font-size: 2em; margin: 0; color: #ffc107;"><?= count($results['skipped']) ?></h3>
            <p style="margin: 5px 0 0 0; color: #666;"><?= Lang::t('report.skipped_count') ?></p>
        </div>
        
        <div class="card" style="text-align: center; border-left: 4px solid #dc3545; margin: 0;">
            <h3 style="font-size: 2em; margin: 0; color: #dc3545;"><?= count($results['errors']) ?></h3>
            <p style="margin: 5px 0 0 0; color: #666;"><?= Lang::t('report.error_count') ?></p>
        </div>
        
        <div class="card" style="text-align: center; border-left: 4px solid #667eea; margin: 0;">
            <h3 style="font-size: 2em; margin: 0; color: #667eea;"><?= $total ?></h3>
            <p style="margin: 5px 0 0 0; color: #666;"><?= Lang::t('report.total_processed') ?></p>
        </div>
    </div>

    <?php if (!empty($results['success'])): ?>
        <h3 style="color: #28a745;">✅ <?= Lang::t('report.success_count') ?></h3>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="background: #f4f4f4;">
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">File</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Computers</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results['success'] as $s): ?>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($s['file']) ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= $s['computers'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if (!empty($results['skipped'])): ?>
        <h3 style="color: #ffc107;">⚠️ <?= Lang::t('report.skipped_files') ?></h3>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="background: #f4f4f4;">
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">File</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results['skipped'] as $file): ?>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($file) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if (!empty($results['errors'])): ?>
        <h3 style="color: #dc3545;">❌ <?= Lang::t('report.error_files') ?></h3>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="background: #f4f4f4;">
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">File</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Reason</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results['errors'] as $e): ?>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($e['file']) ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd; color: #dc3545;"><?= htmlspecialchars($e['reason']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <br>
    <a href="/reports/upload" class="btn"><?= Lang::t('report.upload_new') ?></a>
    <a href="/reports" class="btn" style="background: #6c757d;"><?= Lang::t('report.back_to_list') ?></a>
</div>