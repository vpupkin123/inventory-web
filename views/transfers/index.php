<div class="card">
    <h2><?= Lang::t('history.title') ?></h2>
    <br>

    <?php if (empty($transfers)): ?>
        <p><?= Lang::t('history.empty') ?></p>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f4f4f4;">
                    <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('history.date') ?></th>
                    <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('history.computer') ?></th>
                    <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('history.from') ?></th>
                    <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('history.to') ?></th>
                    <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('history.by') ?></th>
                    <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('history.comment') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transfers as $t): 
                    $from = $t['from_login'] ?: Lang::t('history.warehouse');
                    $to = $t['to_login'] ?: Lang::t('history.warehouse');
                ?>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($t['transferred_at']) ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <strong><?= htmlspecialchars($t['computer_name']) ?></strong><br>
                        <small style="color: #666;"><?= htmlspecialchars($t['serial_number'] ?: 'N/A') ?></small>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($from) ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($to) ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($t['by_login']) ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($t['comment']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>