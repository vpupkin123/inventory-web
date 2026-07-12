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

        // Collect files from both inputs (single/multiple files and folder)
        $allFiles = [];
        
        if (isset($_FILES['json_files']) && $_FILES['json_files']['error'][0] !== UPLOAD_ERR_NO_FILE) {
            $allFiles = $this->normalizeFiles($_FILES['json_files']);
        }
        
        if (isset($_FILES['json_folder']) && $_FILES['json_folder']['error'][0] !== UPLOAD_ERR_NO_FILE) {
            $folderFiles = $this->normalizeFiles($_FILES['json_folder']);
            $allFiles = array_merge($allFiles, $folderFiles);
        }

        if (empty($allFiles)) {
            $_SESSION['report_error'] = Lang::t('report.no_files_selected');
            header('Location: /reports/upload');
            exit;
        }

        // Limit to prevent overload
        $maxFiles = 100;
        if (count($allFiles) > $maxFiles) {
            $_SESSION['report_error'] = str_replace(':max', $maxFiles, Lang::t('report.max_files_warning'));
            header('Location: /reports/upload');
            exit;
        }

        $pdo = Database::getConnection();
        $uploadDir = ROOT_PATH . '/data/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $results = [
            'success' => [],
            'skipped' => [],
            'errors' => []
        ];

        foreach ($allFiles as $file) {
            $fileName = $file['name'];
            $tmpPath = $file['tmp_name'];
            $error = $file['error'];

            // Skip if upload error
            if ($error !== UPLOAD_ERR_OK) {
                $results['errors'][] = [
                    'file' => $fileName,
                    'reason' => 'Upload error (code: ' . $error . ')'
                ];
                continue;
            }

            // Validate extension
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if ($ext !== 'json') {
                $results['errors'][] = [
                    'file' => $fileName,
                    'reason' => Lang::t('report.error_invalid_format')
                ];
                continue;
            }

            // Check duplicate by hash
            $fileHash = hash_file('sha256', $tmpPath);
            $stmt = $pdo->prepare("SELECT id FROM processed_uploads WHERE file_hash = ?");
            $stmt->execute([$fileHash]);
            if ($stmt->fetch()) {
                $results['skipped'][] = $fileName;
                continue;
            }

            try {
                // Save file
                $destPath = $uploadDir . uniqid('report_') . '_' . $fileName;
                if (!move_uploaded_file($tmpPath, $destPath)) {
                    throw new RuntimeException("Failed to move uploaded file");
                }

                // Register upload
                $stmt = $pdo->prepare("
                    INSERT INTO processed_uploads (file_name, file_hash, uploaded_by) 
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$fileName, $fileHash, $_SESSION['user_id']]);

                // Parse and insert computers
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

                $results['success'][] = [
                    'file' => $fileName,
                    'computers' => $insertedCount
                ];

            } catch (Exception $e) {
                $logFile = ROOT_PATH . '/data/app_error.log';
                file_put_contents($logFile, "Batch upload error [$fileName]: " . $e->getMessage() . "\n", FILE_APPEND);
                
                $results['errors'][] = [
                    'file' => $fileName,
                    'reason' => $e->getMessage()
                ];
            }
        }

        // Save results to session and redirect to results page
        $_SESSION['batch_results'] = $results;
        header('Location: /reports/results');
        exit;
    }

    /**
     * Normalize $_FILES array (handles both single and multiple files)
     */
    private function normalizeFiles(array $files): array
    {
        $normalized = [];
        
        // If it's a single file upload (no array structure)
        if (!is_array($files['name'])) {
            return [$files];
        }
        
        // Multiple files
        $count = count($files['name']);
        for ($i = 0; $i < $count; $i++) {
            if ($files['name'][$i] === '') {
                continue;
            }
            $normalized[] = [
                'name' => $files['name'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i]
            ];
        }
        
        return $normalized;
    }

    /**
     * Show batch upload results
     */
    public function results(): void
    {
        Auth::requireAuth();

        $results = $_SESSION['batch_results'] ?? null;
        unset($_SESSION['batch_results']);

        if (!$results) {
            header('Location: /reports');
            exit;
        }

        View::render('reports/results', [
            'results' => $results
        ]);
    }
}