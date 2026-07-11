<div class="card">
    <h2><?= Lang::t('users.title') ?></h2>
    <br>

    <?php if (isset($_SESSION['user_success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['user_success']) ?></div>
        <?php unset($_SESSION['user_success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['user_error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['user_error']) ?></div>
        <?php unset($_SESSION['user_error']); ?>
    <?php endif; ?>

    <a href="/users/create" class="btn"><?= Lang::t('users.add') ?></a>
    <br><br>

    <?php if (empty($users)): ?>
        <p><?= Lang::t('users.empty') ?></p>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f4f4f4;">
                    <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('users.id') ?></th>
                    <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('users.login') ?></th>
                    <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('users.fio') ?></th>
                    <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('users.role') ?></th>
                    <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('users.actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): 
                    $roleKey = 'users.role_' . $u['role'];
                    $roleName = Lang::t($roleKey);
                    $isWarehouse = ($u['login'] === WAREHOUSE_LOGIN);
                    $isSelf = ($u['id'] == $_SESSION['user_id']);
                ?>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= $u['id'] ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <strong><?= htmlspecialchars($u['login']) ?></strong>
                        <?php if ($isWarehouse): ?> <small style="color: #999;">(Virtual)</small> <?php endif; ?>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars(trim($u['last_name'] . ' ' . $u['first_name'] . ' ' . $u['middle_name'])) ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= $roleName ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <?php if (!$isWarehouse && !$isSelf): ?>
                            <a href="/users/computers?id=<?= $u['id'] ?>" class="btn" style="padding: 5px 10px; font-size: 0.9em; background: #17a2b8;"><?= Lang::t('users.computers') ?></a>
                            <a href="/users/edit?id=<?= $u['id'] ?>" class="btn" style="padding: 5px 10px; font-size: 0.9em;"><?= Lang::t('users.edit') ?></a>
                            <a href="/users/delete?id=<?= $u['id'] ?>" class="btn" style="padding: 5px 10px; font-size: 0.9em; background: #dc3545;" onclick="return confirm('<?= Lang::t('users.confirm_delete') ?>');"><?= Lang::t('users.delete') ?></a>
                        <?php else: ?>
                            <span style="color: #999;">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>