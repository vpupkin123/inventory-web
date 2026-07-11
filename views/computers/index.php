<div class="card">
    <h2><?= Lang::t('computers.title') ?></h2>
    <div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2><?= Lang::t('computers.title') ?></h2>
        <a href="/computers/export" class="btn" style="background: #28a745;"><?= Lang::t('common.export') ?></a>
    </div>
    <br>
    
    <?php if (empty($computers)): ?>
        <p><?= Lang::t('computers.empty') ?></p>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f4f4f4;">
                    <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('computers.id') ?></th>
                    <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('computers.name') ?></th>
                    <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('computers.owner') ?></th>
                    <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('computers.status') ?></th>
                    <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('computers.actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($computers as $pc): 
                    $ownerName = $pc['login'] ? htmlspecialchars($pc['login']) : Lang::t('history.warehouse');
                    $status = $pc['is_processed'] ? Lang::t('computers.status_processed') : Lang::t('computers.status_new');
                ?>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= $pc['id'] ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <strong><?= htmlspecialchars($pc['computer_name']) ?></strong><br>
                        <small style="color: #666;"><?= htmlspecialchars($pc['cpu_name']) ?></small>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= $ownerName ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= $status ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <a href="/computer?id=<?= $pc['id'] ?>" class="btn" style="padding: 5px 10px; font-size: 0.9em;"><?= Lang::t('computers.view') ?></a>
                        <a href="/computer/transfer?id=<?= $pc['id'] ?>" class="btn" style="padding: 5px 10px; font-size: 0.9em; background: #28a745;"><?= Lang::t('computers.transfer') ?></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>