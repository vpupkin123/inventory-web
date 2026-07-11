<?php

class ProcessingController
{
    /**
     * Show queue of unprocessed computers
     */
    public function index(): void
    {
        Auth::requireAuth();

        $pdo = Database::getConnection();
        $warehouseId = Database::getWarehouseId();

        // Fetch computers currently at warehouse and not processed
        $stmt = $pdo->prepare("
            SELECT * FROM computers 
            WHERE current_user_id = ? AND is_processed = 0 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$warehouseId]);
        $computers = $stmt->fetchAll();

        View::render('processing/index', [
            'computers' => $computers
        ]);
    }

    /**
     * Process selected computers
     */
    public function process(): void
    {
        Auth::requireAuth();

        $ids = $_POST['computer_ids'] ?? [];
        $comments = $_POST['comments'] ?? [];
        $createUsers = $_POST['create_user'] ?? [];

        if (empty($ids)) {
            $_SESSION['processing_error'] = Lang::t('processing.no_selected');
            header('Location: /processing');
            exit;
        }

        $pdo = Database::getConnection();
        $warehouseId = Database::getWarehouseId();

        try {
            foreach ($ids as $id) {
                $id = (int)$id;
                $comment = trim($comments[$id] ?? '');
                $shouldCreateUser = isset($createUsers[$id]);

                $newUserId = null;

                if ($shouldCreateUser) {
                    // Get configured user data from form
                    $config = $_POST['user_config'][$id] ?? [];
                    $lastName = trim($config['last_name'] ?? '');
                    $firstName = trim($config['first_name'] ?? '');
                    $middleName = trim($config['middle_name'] ?? '');
                    $login = trim($config['login'] ?? '');

                    if (!empty($lastName) && !empty($firstName) && !empty($login)) {
                        $newUserId = $this->createUserFromConfig($pdo, $login, $lastName, $firstName, $middleName);
                    }
                }

                // Update computer: set processed flag, comment, and optionally new user
                $userIdToSet = $newUserId ?? $warehouseId; // If no new user, stays at warehouse
                
                $stmt = $pdo->prepare("
                    UPDATE computers 
                    SET is_processed = 1, 
                        comment = ?, 
                        current_user_id = ?,
                        updated_at = datetime('now','localtime')
                    WHERE id = ?
                ");
                $stmt->execute([$comment, $userIdToSet, $id]);
            }

            $_SESSION['processing_success'] = Lang::t('processing.success');
            header('Location: /processing');
            exit;

        } catch (Exception $e) {
            $_SESSION['processing_error'] = Lang::t('processing.error') . ': ' . $e->getMessage();
            header('Location: /processing');
            exit;
        }
    }

    /**
     * Find existing user by FIO or create a new one
     */
    /**
     * Create user from configured form data
     */
    private function createUserFromConfig(PDO $pdo, string $login, string $lastName, string $firstName, string $middleName): ?int
    {
        // Check if user already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE login = ?");
        $stmt->execute([$login]);
        $existing = $stmt->fetch();

        if ($existing) {
            return (int)$existing['id'];
        }

        // Create new user
        $defaultPassword = password_hash('password', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
            INSERT INTO users (login, password_hash, last_name, first_name, middle_name, role, must_change_pwd)
            VALUES (?, ?, ?, ?, ?, 'viewer', 1)
        ");
        $stmt->execute([$login, $defaultPassword, $lastName, $firstName, $middleName]);

        return (int)$pdo->lastInsertId();
    }
}