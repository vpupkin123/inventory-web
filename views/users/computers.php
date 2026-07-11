<div class="card">
    <h2><?= Lang::t('users.computers_title') ?>: <?= htmlspecialchars($user['login']) ?></h2>
    <br>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <a href="/users" class="btn" style="background: #6c757d;"><?= Lang::t('common.back') ?></a>
    <br><br>

    <h3><?= Lang::t('users.computers') ?> (<?= count($computers) ?>)</h3>
    <?php if (empty($computers)): ?>
        <p><?= Lang::t('users.no_computers') ?></p>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="background: #f4f4f4;">
                    <th style="padding: 10px; border: 1px solid #ddd;">ID</th>
                    <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('computers.name') ?></th>
                    <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('computer.cpu') ?></th>
                    <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('computer.ram') ?></th>
                    <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('computers.actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($computers as $pc): ?>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= $pc['id'] ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <strong><?= htmlspecialchars($pc['computer_name']) ?></strong><br>
                        <small style="color: #666;"><?= htmlspecialchars($pc['serial_number'] ?: 'N/A') ?></small>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($pc['cpu_name']) ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= $pc['ram_total_gb'] ?> GB</td>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <a href="/users/computers/remove?user_id=<?= $user['id'] ?>&id=<?= $pc['id'] ?>" 
                           class="btn" 
                           style="padding: 5px 10px; font-size: 0.9em; background: #dc3545;"
                           onclick="return confirm('<?= Lang::t('users.confirm_delete') ?>');">
                            <?= Lang::t('users.remove_computer') ?>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h3><?= Lang::t('users.add_computer') ?></h3>
    <?php if (empty($warehouseComputers)): ?>
        <p><?= Lang::t('computers.empty') ?></p>
    <?php else: ?>
        <form method="POST" action="/users/computers/add">
            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
            <div class="form-group">
                <label><?= Lang::t('users.select_computer') ?>:</label>
                <select name="computer_id" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                    <option value="">-- <?= Lang::t('common.cancel') ?> --</option>
                    <?php foreach ($warehouseComputers as $pc): ?>
                        <option value="<?= $pc['id'] ?>">
                            #<?= $pc['id'] ?> - <?= htmlspecialchars($pc['computer_name']) ?> 
                            (<?= htmlspecialchars($pc['cpu_name']) ?>, <?= $pc['ram_total_gb'] ?> GB)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn"><?= Lang::t('users.add_computer') ?></button>
        </form>
    <?php endif; ?>
</div>