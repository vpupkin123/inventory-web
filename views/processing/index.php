<div class="card">
    <h2><?= Lang::t('processing.title') ?></h2>
    <br>

    <?php if (isset($_SESSION['processing_error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['processing_error']) ?></div>
        <?php unset($_SESSION['processing_error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['processing_success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['processing_success']) ?></div>
        <?php unset($_SESSION['processing_success']); ?>
    <?php endif; ?>

    <?php if (empty($computers)): ?>
        <p><?= Lang::t('processing.empty_queue') ?></p>
    <?php else: ?>
        <form method="POST" action="/processing/process">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f4f4f4;">
                        <!-- Главный чекбокс "Выделить все" -->
                        <th style="padding: 10px; border: 1px solid #ddd; width: 30px; text-align: center;">
                            <input type="checkbox" id="select_all">
                        </th>
                        <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('processing.computer_name') ?></th>
                        <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('processing.hardware') ?></th>
                        <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('processing.reported_by') ?></th>
                        <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('processing.comment') ?></th>
                        <th style="padding: 10px; border: 1px solid #ddd;"><?= Lang::t('processing.create_user') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($computers as $pc): ?>
                    <tr>
                        <!-- Обычный чекбокс (без checked по умолчанию) -->
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
                            <input type="checkbox" name="computer_ids[]" value="<?= $pc['id'] ?>" class="row_checkbox">
                        </td>
                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <strong><?= htmlspecialchars($pc['computer_name']) ?></strong><br>
                            <small style="color: #666;"><?= htmlspecialchars($pc['ip_address']) ?></small>
                        </td>
                        <td style="padding: 10px; border: 1px solid #ddd; font-size: 0.9em;">
                            <strong>CPU:</strong> <?= htmlspecialchars($pc['cpu_name']) ?><br>
                            <strong>RAM:</strong> <?= htmlspecialchars($pc['ram_total_gb']) ?> GB<br>
                            <strong>MB:</strong> <?= htmlspecialchars($pc['motherboard']) ?>
                        </td>
                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <?= htmlspecialchars($pc['reported_by']) ?>
                        </td>
                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <input type="text" name="comments[<?= $pc['id'] ?>]" value="<?= htmlspecialchars($pc['comment']) ?>" style="width: 100%; padding: 5px;">
                        </td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
                            <input type="checkbox" name="create_user[<?= $pc['id'] ?>]" value="1">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <br>
            <button type="submit" class="btn"><?= Lang::t('processing.process_selected') ?></button>
        </form>
    <?php endif; ?>
</div>

<!-- Скрипт для работы чекбокса "Выделить все" -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select_all');
    const rowCheckboxes = document.querySelectorAll('.row_checkbox');

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            rowCheckboxes.forEach(function(checkbox) {
                checkbox.checked = selectAll.checked;
            });
        });
    }
});
</script>