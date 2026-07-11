<div class="card">
    <h2><?= Lang::t('computer.title') ?> #<?= $computer['id'] ?></h2>
    <br>

    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px; font-weight: bold; width: 200px;"><?= Lang::t('computer.serial') ?>:</td>
            <td style="padding: 8px;"><?= htmlspecialchars($computer['serial_number'] ?: 'N/A') ?></td>
        </tr>
        <tr style="background: #f9f9f9;">
            <td style="padding: 8px; font-weight: bold;"><?= Lang::t('computer.mb') ?>:</td>
            <td style="padding: 8px;"><?= htmlspecialchars($computer['motherboard']) ?></td>
        </tr>
        <tr>
            <td style="padding: 8px; font-weight: bold;"><?= Lang::t('computer.cpu') ?>:</td>
            <td style="padding: 8px;"><?= htmlspecialchars($computer['cpu_name']) ?> (<?= $computer['cpu_cores'] ?>C / <?= $computer['cpu_threads'] ?>T)</td>
        </tr>
        <tr style="background: #f9f9f9;">
            <td style="padding: 8px; font-weight: bold;"><?= Lang::t('computer.ram') ?>:</td>
            <td style="padding: 8px;"><?= $computer['ram_total_gb'] ?> GB <br><small style="color: #666;"><?= htmlspecialchars($computer['ram_details']) ?></small></td>
        </tr>
        <tr>
            <td style="padding: 8px; font-weight: bold;"><?= Lang::t('computer.storage') ?>:</td>
            <td style="padding: 8px;"><?= htmlspecialchars($computer['storage_info']) ?></td>
        </tr>
        <tr style="background: #f9f9f9;">
            <td style="padding: 8px; font-weight: bold;"><?= Lang::t('computer.os') ?>:</td>
            <td style="padding: 8px;"><?= htmlspecialchars($computer['os_caption']) ?> (Build <?= htmlspecialchars($computer['os_build']) ?>)</td>
        </tr>
        <tr>
            <td style="padding: 8px; font-weight: bold;"><?= Lang::t('computer.ip') ?>:</td>
            <td style="padding: 8px;"><?= htmlspecialchars($computer['ip_address']) ?></td>
        </tr>
        <tr style="background: #f9f9f9;">
            <td style="padding: 8px; font-weight: bold;"><?= Lang::t('computer.reported_by') ?>:</td>
            <td style="padding: 8px;"><?= htmlspecialchars($computer['reported_by']) ?></td>
        </tr>
        <tr>
            <td style="padding: 8px; font-weight: bold;"><?= Lang::t('computer.comment') ?>:</td>
            <td style="padding: 8px;"><?= htmlspecialchars($computer['comment']) ?></td>
        </tr>
        <tr style="background: #f9f9f9;">
            <td style="padding: 8px; font-weight: bold;"><?= Lang::t('computer.created_at') ?>:</td>
            <td style="padding: 8px;"><?= htmlspecialchars($computer['created_at']) ?></td>
        </tr>
    </table>

    <br>
    <a href="/computer/transfer?id=<?= $computer['id'] ?>" class="btn" style="background: #28a745;"><?= Lang::t('computers.transfer') ?></a>
    <a href="/computers" class="btn" style="background: #6c757d;"><?= Lang::t('computer.back_to_list') ?></a>
</div>