<div class="card">
    <h2><?= Lang::t('transfer.title') ?>: <?= htmlspecialchars($computer['computer_name']) ?></h2>
    <br>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="/computer/transfer">
        <input type="hidden" name="computer_id" value="<?= $computer['id'] ?>">

        <!-- УЛУЧШЕННОЕ ПОЛЕ: От кого -->
        <div class="form-group">
            <label><?= Lang::t('transfer.from') ?>:</label>
            <input type="text" 
                   value="<?= !empty($computer['login']) ? htmlspecialchars($computer['login'] . ' (' . trim($computer['last_name'] . ' ' . $computer['first_name']) . ')') : Lang::t('history.warehouse') ?>" 
                   disabled 
                   style="background: #eee; width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
        </div>

        <div class="form-group">
            <label><?= Lang::t('transfer.to_user') ?>:</label>
            <select name="to_user_id" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                <option value="">-- <?= Lang::t('common.cancel') ?> --</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['id'] ?>">
                        <?= htmlspecialchars($user['login']) ?> (<?= htmlspecialchars(trim($user['last_name'] . ' ' . $user['first_name'])) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
            <input type="checkbox" name="to_warehouse" id="to_warehouse" value="1" style="width: auto;">
            <label for="to_warehouse" style="margin: 0;"><?= Lang::t('transfer.to_warehouse') ?></label>
        </div>

        <div class="form-group">
            <label><?= Lang::t('transfer.comment') ?>:</label>
            <input type="text" name="comment" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
        </div>

        <button type="submit" class="btn"><?= Lang::t('transfer.submit') ?></button>
        <a href="/computer?id=<?= $computer['id'] ?>" class="btn" style="background: #6c757d;"><?= Lang::t('common.cancel') ?></a>
    </form>
</div>