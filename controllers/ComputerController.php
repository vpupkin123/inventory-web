<?php

class ComputerController
{
    public function index(): void
    {
        Auth::requireAuth();
        $pdo = Database::getConnection();

        $stmt = $pdo->query("
            SELECT c.*, u.login, u.last_name, u.first_name 
            FROM computers c 
            LEFT JOIN users u ON c.current_user_id = u.id 
            ORDER BY c.id DESC
        ");
        $computers = $stmt->fetchAll();

        View::render('computers/index', ['computers' => $computers]);
    }

    public function show(): void
    {
        Auth::requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /computers');
            exit;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT c.*, u.login, u.last_name, u.first_name 
            FROM computers c 
            LEFT JOIN users u ON c.current_user_id = u.id 
            WHERE c.id = ?
        ");
        $stmt->execute([$id]);
        $computer = $stmt->fetch();

        if (!$computer) {
            header('Location: /computers');
            exit;
        }

        View::render('computers/show', ['computer' => $computer]);
    }

    public function showTransfer(): void
    {
        Auth::requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /computers');
            exit;
        }

        $pdo = Database::getConnection();
        
        // Get computer info
        $stmt = $pdo->prepare("SELECT * FROM computers WHERE id = ?");
        $stmt->execute([$id]);
        $computer = $stmt->fetch();

        if (!$computer) {
            header('Location: /computers');
            exit;
        }

        // Get all users for dropdown (exclude warehouse 'none' role if desired, but let's keep it simple)
        $usersStmt = $pdo->query("SELECT id, login, last_name, first_name FROM users WHERE role != 'none' ORDER BY last_name");
        $users = $usersStmt->fetchAll();

        View::render('computers/transfer', [
            'computer' => $computer,
            'users' => $users,
            'error' => $_SESSION['transfer_error'] ?? null,
            'success' => $_SESSION['transfer_success'] ?? null
        ]);
        unset($_SESSION['transfer_error'], $_SESSION['transfer_success']);
    }

    public function transfer(): void
    {
        Auth::requireAuth();
        
        $id = (int)($_POST['computer_id'] ?? 0);
        $toUserId = $_POST['to_user_id'] ?? null;
        $toWarehouse = isset($_POST['to_warehouse']);
        $comment = trim($_POST['comment'] ?? '');

        if ($id <= 0) {
            header('Location: /computers');
            exit;
        }

        if (!$toUserId && !$toWarehouse) {
            $_SESSION['transfer_error'] = Lang::t('transfer.error_no_target');
            header('Location: /computer/transfer?id=' . $id);
            exit;
        }

        $pdo = Database::getConnection();
        $warehouseId = Database::getWarehouseId();

        try {
            // Get current owner
            $stmt = $pdo->prepare("SELECT current_user_id FROM computers WHERE id = ?");
            $stmt->execute([$id]);
            $fromUserId = (int)$stmt->fetchColumn();

            // Determine target user ID
            $targetUserId = $toWarehouse ? $warehouseId : (int)$toUserId;

            // Insert transfer record
            $stmt = $pdo->prepare("
                INSERT INTO transfers (computer_id, from_user_id, to_user_id, transferred_by, comment, transferred_at)
                VALUES (?, ?, ?, ?, ?, datetime('now','localtime'))
            ");
            $stmt->execute([$id, $fromUserId, $targetUserId, $_SESSION['user_id'], $comment]);

            // Update computer
            $stmt = $pdo->prepare("
                UPDATE computers 
                SET current_user_id = ?, updated_at = datetime('now','localtime') 
                WHERE id = ?
            ");
            $stmt->execute([$targetUserId, $id]);

            $_SESSION['transfer_success'] = Lang::t('transfer.success');
            header('Location: /computer/transfer?id=' . $id);
            exit;

        } catch (Exception $e) {
            $_SESSION['transfer_error'] = Lang::t('transfer.error') . ': ' . $e->getMessage();
            header('Location: /computer/transfer?id=' . $id);
            exit;
        }
    }

        public function export(): void
    {
        Auth::requireAuth();
        $pdo = Database::getConnection();

        $stmt = $pdo->query("
            SELECT c.*, u.login 
            FROM computers c 
            LEFT JOIN users u ON c.current_user_id = u.id 
            ORDER BY c.id DESC
        ");
        $computers = $stmt->fetchAll();

        $headers = [
            Lang::t('computers.id'),
            Lang::t('computers.name'),
            Lang::t('computer.serial'),
            Lang::t('computer.mb'),
            Lang::t('computer.cpu'),
            Lang::t('computer.ram'),
            Lang::t('computer.storage'),
            Lang::t('computer.os'),
            Lang::t('computer.ip'),
            Lang::t('computers.owner'),
            Lang::t('computer.comment'),
            Lang::t('computer.created_at')
        ];

        $rows = [];
        foreach ($computers as $c) {
            $rows[] = [
                $c['id'],
                $c['computer_name'],
                $c['serial_number'] ?: 'N/A',
                $c['motherboard'],
                $c['cpu_name'] . " ({$c['cpu_cores']}C/{$c['cpu_threads']}T)",
                $c['ram_total_gb'] . " GB\n" . $c['ram_details'],
                $c['storage_info'],
                $c['os_caption'] . " ({$c['os_build']})",
                $c['ip_address'],
                $c['login'] ?: Lang::t('history.warehouse'),
                $c['comment'],
                $c['created_at']
            ];
        }

        Export::toExcel('computers_' . date('Y-m-d') . '.xls', $headers, $rows);
    }
}