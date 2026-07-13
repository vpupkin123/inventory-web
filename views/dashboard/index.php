<div class="card">
    <h2><?= Lang::t('dashboard.welcome') ?>, <?= htmlspecialchars($user['name']) ?>!</h2>
    <p style="color: #666; margin-top: 10px;">
        <?= Lang::t('dashboard.your_role') ?>: <strong><?= htmlspecialchars($user['role']) ?></strong>
    </p>
</div>

<!-- Statistics Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 20px;">
    <div class="card" style="text-align: center; border-left: 4px solid #007bff;">
        <h3 style="font-size: 2.5em; margin: 0; color: #007bff;"><?= $stats['total_computers'] ?></h3>
        <p style="margin: 10px 0 0 0; color: #666;"><?= Lang::t('dashboard.total_computers') ?></p>
    </div>
    
    <div class="card" style="text-align: center; border-left: 4px solid #28a745;">
        <h3 style="font-size: 2.5em; margin: 0; color: #28a745;"><?= $stats['assigned_computers'] ?></h3>
        <p style="margin: 10px 0 0 0; color: #666;"><?= Lang::t('dashboard.assigned_computers') ?></p>
    </div>
    
    <div class="card" style="text-align: center; border-left: 4px solid #ffc107;">
        <h3 style="font-size: 2.5em; margin: 0; color: #ffc107;"><?= $stats['warehouse_computers'] ?></h3>
        <p style="margin: 10px 0 0 0; color: #666;"><?= Lang::t('dashboard.warehouse_computers') ?></p>
    </div>
    
    <div class="card" style="text-align: center; border-left: 4px solid #dc3545;">
        <h3 style="font-size: 2.5em; margin: 0; color: #dc3545;"><?= $stats['unprocessed'] ?></h3>
        <p style="margin: 10px 0 0 0; color: #666;"><?= Lang::t('dashboard.unprocessed') ?></p>
    </div>
    
    <div class="card" style="text-align: center; border-left: 4px solid #17a2b8;">
        <h3 style="font-size: 2.5em; margin: 0; color: #17a2b8;"><?= $stats['total_users'] ?></h3>
        <p style="margin: 10px 0 0 0; color: #666;"><?= Lang::t('dashboard.total_users') ?></p>
    </div>
</div>

<!-- Quick Actions -->
<div class="card">
    <h3><?= Lang::t('dashboard.quick_actions') ?></h3>
    <br>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <?php if ($stats['unprocessed'] > 0): ?>
            <a href="/processing" class="btn" style="background: #dc3545;">
                📋 <?= Lang::t('dashboard.process_queue') ?> (<?= $stats['unprocessed'] ?>)
            </a>
        <?php endif; ?>
        
        <a href="/reports/upload" class="btn" style="background: #28a745;">
            📤 <?= Lang::t('dashboard.upload_report') ?>
        </a>
        
        <a href="/computers" class="btn">
            💻 <?= Lang::t('dashboard.view_computers') ?>
        </a>
        
        <?php if ($user['role'] === 'admin'): ?>
            <a href="/users" class="btn" style="background: #17a2b8;">
                👥 <?= Lang::t('dashboard.manage_users') ?>
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Recent Activity -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
    <!-- Recent Uploads -->
    <div class="card">
        <h3><?= Lang::t('dashboard.recent_uploads') ?></h3>
        <br>
        <?php if (empty($stats['recent_uploads'])): ?>
            <p style="color: #999;"><?= Lang::t('dashboard.no_recent_uploads') ?></p>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f4f4f4;">
                        <th style="padding: 8px; border: 1px solid #ddd; text-align: left;"><?= Lang::t('dashboard.file') ?></th>
                        <th style="padding: 8px; border: 1px solid #ddd; text-align: left;"><?= Lang::t('dashboard.date') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats['recent_uploads'] as $upload): ?>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd;">
                            <?= htmlspecialchars($upload['file_name']) ?>
                            <br><small style="color: #666;"><?= htmlspecialchars(trim($upload['last_name'] . ' ' . $upload['first_name'])) ?></small>
                        </td>
                        <td style="padding: 8px; border: 1px solid #ddd;"><?= htmlspecialchars($upload['uploaded_at']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <!-- Recent Transfers -->
    <div class="card">
        <h3><?= Lang::t('dashboard.recent_transfers') ?></h3>
        <br>
        <?php if (empty($stats['recent_transfers'])): ?>
            <p style="color: #999;"><?= Lang::t('dashboard.no_recent_transfers') ?></p>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f4f4f4;">
                        <th style="padding: 8px; border: 1px solid #ddd; text-align: left;"><?= Lang::t('dashboard.computer') ?></th>
                        <th style="padding: 8px; border: 1px solid #ddd; text-align: left;"><?= Lang::t('dashboard.from_to') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats['recent_transfers'] as $transfer): ?>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd;">
                            <?= htmlspecialchars($transfer['computer_name']) ?>
                            <br><small style="color: #666;"><?= htmlspecialchars($transfer['transferred_at']) ?></small>
                        </td>
                        <td style="padding: 8px; border: 1px solid #ddd;">
                            <?= htmlspecialchars($transfer['from_login'] ?: Lang::t('history.warehouse')) ?> → 
                            <?= htmlspecialchars($transfer['to_login'] ?: Lang::t('history.warehouse')) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

        <!-- Documentation Links -->
    <div class="card" style="margin-top: 30px;">
        <h3>📚 <?= Lang::t('dashboard.documentation') ?></h3>
        <br>
        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
            <?php if (Lang::is('ru')): ?>
                <a href="/docs/ru_user-guide.html" target="_blank" class="btn" style="background: #17a2b8;">
                     <?= Lang::t('dashboard.user_guide') ?>
                </a>
                <a href="/docs/ru_admin-guide.html" target="_blank" class="btn" style="background: #11998e;">
                    🛠️ <?= Lang::t('dashboard.admin_guide') ?>
                </a>
            <?php else: ?>
                <a href="/docs/en_user-guide.html" target="_blank" class="btn" style="background: #17a2b8;">
                     <?= Lang::t('dashboard.user_guide') ?>
                </a>
                <a href="/docs/en_admin-guide.html" target="_blank" class="btn" style="background: #11998e;">
                    🛠️ <?= Lang::t('dashboard.admin_guide') ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>