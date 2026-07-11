<?php

class ReportController
{
    public function index(): void
    {
        Auth::requireAuth();

        $pdo = Database::getConnection();
        
        $stmt = $pdo->query("
            SELECT pu.*, u.last_name, u.first_name 
            FROM processed_uploads pu 
            LEFT JOIN users u ON pu.uploaded_by = u.id 
            ORDER BY pu.uploaded_at DESC
        ");
        $reports = $stmt->fetchAll();

        View::render('reports/list', [
            'reports' => $reports
        ]);
    }

    public function showUpload(): void
    {
        Auth::requireAuth();

        View::render('reports/upload', [
            'error'   => $_SESSION['report_error'] ?? null,
            'success' => $_SESSION['report_success'] ?? null
        ]);

        unset($_SESSION['report_error'], $_SESSION['report_success']);
    }

    public function upload(): void
    {
        Auth::requireAuth();

        if (!isset($_FILES['json_file']) || $_FILES['json_file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['report_error'] = Lang::t('report.error_no_file');
            header('Location: /reports/upload');
            exit;
        }

        $file = $_FILES['json_file'];
        $fileName = $file['name'];
        $tmpPath = $file['tmp_name'];

        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if ($ext !== 'json') {
            $_SESSION['report_error'] = Lang::t('report.error_invalid_format');
            header('Location: /reports/upload');
            exit;
        }

        $fileHash = hash_file('sha256', $tmpPath);

        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT id FROM processed_uploads WHERE file_hash = ?");
        $stmt->execute([$fileHash]);
        if ($stmt->fetch()) {
            $_SESSION['report_error'] = Lang::t('report.error_duplicate');
            header('Location: /reports/upload');
            exit;
        }

        try {
            $uploadDir = ROOT_PATH . '/data/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }
            $destPath = $uploadDir . uniqid('report_') . '_' . $fileName;
            move_uploaded_file($tmpPath, $destPath);

            $stmt = $pdo->prepare("
                INSERT INTO processed_uploads (file_name, file_hash, uploaded_by) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$fileName, $fileHash, $_SESSION['user_id']]);

            $computers = JsonParser::parse($destPath);
            $warehouseId = Database::getWarehouseId();

            $insertedCount = 0;
            $stmt = $pdo->prepare("
                INSERT INTO computers 
                (serial_number, composite_key, manufacturer, model, motherboard, cpu_name, 
                 cpu_cores, cpu_threads, ram_total_gb, ram_details, storage_info, 
                 os_caption, os_build, ip_address, computer_name, reported_by, current_user_id, is_processed)
                VALUES 
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)
            ");

            foreach ($computers as $pc) {
                $stmt->execute([
                    $pc['serial_number'],
                    $pc['composite_key'],
                    $pc['manufacturer'],
                    $pc['model'],
                    $pc['motherboard'],
                    $pc['cpu_name'],
                    $pc['cpu_cores'],
                    $pc['cpu_threads'],
                    $pc['ram_total_gb'],
                    $pc['ram_details'],
                    $pc['storage_info'],
                    $pc['os_caption'],
                    $pc['os_build'],
                    $pc['ip_address'],
                    $pc['computer_name'],
                    $pc['reported_by'],
                    $warehouseId
                ]);
                $insertedCount++;
            }

            $_SESSION['report_success'] = str_replace(':count', $insertedCount, Lang::t('report.success_uploaded'));
            header('Location: /reports');
            exit;

        } catch (Exception $e) {
            $logFile = ROOT_PATH . '/data/app_error.log';
            file_put_contents($logFile, "Report upload error: " . $e->getMessage() . "\n", FILE_APPEND);
            
            $_SESSION['report_error'] = Lang::t('report.error_processing') . ': ' . $e->getMessage();
            header('Location: /reports/upload');
            exit;
        }
    }
}