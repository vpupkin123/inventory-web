<?php

class UserController
{
    public function index(): void
    {
        Auth::requireRole('admin');

        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT * FROM users ORDER BY id ASC");
        $users = $stmt->fetchAll();

        View::render('users/index', ['users' => $users]);
    }

    public function create(): void
    {
        Auth::requireRole('admin');
        
        // Clear old form data
        unset($_SESSION['form_data']);
        
        View::render('users/form', [
            'user' => null,
            'error' => $_SESSION['user_error'] ?? null,
            'title' => Lang::t('users.create_title')
        ]);
        unset($_SESSION['user_error']);
    }

    public function store(): void
    {
        Auth::requireRole('admin');

        $lastName = trim($_POST['last_name'] ?? '');
        $firstName = trim($_POST['first_name'] ?? '');
        $middleName = trim($_POST['middle_name'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'viewer';
        $login = trim($_POST['login'] ?? '');

        // Save form data to session
        $_SESSION['form_data'] = [
            'last_name' => $lastName,
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'login' => $login,
            'role' => $role
        ];

        if (empty($lastName) || empty($firstName) || empty($password)) {
            $_SESSION['user_error'] = Lang::t('change_password.error_empty');
            header('Location: /users/create');
            exit;
        }

        if (!in_array($role, ['admin', 'editor', 'viewer', 'none'])) {
            $role = 'viewer';
        }

        $pdo = Database::getConnection();

        // Auto-generate login if empty
        if (empty($login)) {
            $login = $this->generateUniqueLogin($pdo, $lastName, $firstName, $middleName);
        } else {
            // Check if provided login already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE login = ?");
            $stmt->execute([$login]);
            if ($stmt->fetch()) {
                $_SESSION['user_error'] = Lang::t('users.error_login_exists');
                header('Location: /users/create');
                exit;
            }
        }

        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                INSERT INTO users (login, password_hash, last_name, first_name, middle_name, role, must_change_pwd)
                VALUES (?, ?, ?, ?, ?, ?, 1)
            ");
            $stmt->execute([$login, $hash, $lastName, $firstName, $middleName, $role]);

            // Clear form data on success
            unset($_SESSION['form_data']);
            
            $_SESSION['user_success'] = Lang::t('users.success_created') . ' ' . Lang::t('users.login') . ': ' . $login;
            header('Location: /users');
            exit;
        } catch (Exception $e) {
            $_SESSION['user_error'] = $e->getMessage();
            header('Location: /users/create');
            exit;
        }
    }

    public function edit(): void
    {
        Auth::requireRole('admin');
        $id = (int)($_GET['id'] ?? 0);
        
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        if (!$user) {
            header('Location: /users');
            exit;
        }

        View::render('users/form', [
            'user' => $user,
            'error' => $_SESSION['user_error'] ?? null,
            'title' => Lang::t('users.edit_title')
        ]);
        unset($_SESSION['user_error']);
    }

    public function update(): void
    {
        Auth::requireRole('admin');
        
        $id = (int)($_POST['id'] ?? 0);
        $lastName = trim($_POST['last_name'] ?? '');
        $firstName = trim($_POST['first_name'] ?? '');
        $middleName = trim($_POST['middle_name'] ?? '');
        $role = $_POST['role'] ?? 'viewer';
        $password = $_POST['password'] ?? '';

        if ($id <= 0 || empty($lastName) || empty($firstName)) {
            $_SESSION['user_error'] = Lang::t('change_password.error_empty');
            header('Location: /users/edit?id=' . $id);
            exit;
        }

        if (!in_array($role, ['admin', 'editor', 'viewer', 'none'])) {
            $role = 'viewer';
        }

        $pdo = Database::getConnection();
        
        try {
            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("
                    UPDATE users 
                    SET last_name = ?, first_name = ?, middle_name = ?, role = ?, password_hash = ?, must_change_pwd = 1
                    WHERE id = ?
                ");
                $stmt->execute([$lastName, $firstName, $middleName, $role, $hash, $id]);
            } else {
                $stmt = $pdo->prepare("
                    UPDATE users 
                    SET last_name = ?, first_name = ?, middle_name = ?, role = ?
                    WHERE id = ?
                ");
                $stmt->execute([$lastName, $firstName, $middleName, $role, $id]);
            }

            $_SESSION['user_success'] = Lang::t('users.success_updated');
            header('Location: /users');
            exit;
        } catch (Exception $e) {
            $_SESSION['user_error'] = $e->getMessage();
            header('Location: /users/edit?id=' . $id);
            exit;
        }
    }

    public function toggleBlock(): void
    {
        Auth::requireRole('admin');
        $id = (int)($_GET['id'] ?? 0);

        // Prevent blocking yourself or the warehouse user
        if ($id === (int)$_SESSION['user_id'] || $id === Database::getWarehouseId()) {
            $_SESSION['user_error'] = Lang::t('users.error_self_action');
            header('Location: /users');
            exit;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE users SET role = 'none' WHERE id = ?");
        $stmt->execute([$id]);

        header('Location: /users');
        exit;
    }

    /**
    * Generate unique login from FIO with collision handling
    */
    private function generateUniqueLogin(PDO $pdo, string $lastName, string $firstName, string $middleName): string
    {
        $baseLogin = Transliterator::generateLogin($lastName, $firstName, $middleName);
        
        if (empty($baseLogin)) {
            return 'user' . time();
        }

        // Check if base login is available
        $stmt = $pdo->prepare("SELECT id FROM users WHERE login = ?");
        $stmt->execute([$baseLogin]);
        if (!$stmt->fetch()) {
            return $baseLogin;
        }

        // Collision: try adding more letters from first/middle name
        $firstNameTranslit = Transliterator::transliterate($firstName);
        $middleNameTranslit = Transliterator::transliterate($middleName);
        
        $basePrefix = mb_strtolower(Transliterator::transliterate($lastName), 'UTF-8');
        
        // Try adding 2 letters from first name
        if (mb_strlen($firstNameTranslit) >= 2) {
            $login = $basePrefix . '.' . mb_strtolower(mb_substr($firstNameTranslit, 0, 2), 'UTF-8');
            $stmt->execute([$login]);
            if (!$stmt->fetch()) {
                return $login;
            }
        }
        
        // Try adding 2 letters from first + 1 from middle
        if (mb_strlen($firstNameTranslit) >= 2 && mb_strlen($middleNameTranslit) >= 1) {
            $login = $basePrefix . '.' . 
                     mb_strtolower(mb_substr($firstNameTranslit, 0, 2), 'UTF-8') . 
                     mb_strtolower(mb_substr($middleNameTranslit, 0, 1), 'UTF-8');
            $stmt->execute([$login]);
            if (!$stmt->fetch()) {
                return $login;
            }
        }
        
        // Try adding 3 letters from first name
        if (mb_strlen($firstNameTranslit) >= 3) {
            $login = $basePrefix . '.' . mb_strtolower(mb_substr($firstNameTranslit, 0, 3), 'UTF-8');
            $stmt->execute([$login]);
            if (!$stmt->fetch()) {
                return $login;
            }
        }
        
        // Fallback: add numeric suffix
        $counter = 2;
        while (true) {
            $login = $baseLogin . $counter;
            $stmt->execute([$login]);
            if (!$stmt->fetch()) {
                return $login;
            }
            $counter++;
            if ($counter > 999) {
                return $baseLogin . '_' . time();
            }
        }
    }

    public function delete(): void
    {
        Auth::requireRole('admin');
        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0 || $id === (int)$_SESSION['user_id'] || $id === Database::getWarehouseId()) {
            $_SESSION['user_error'] = Lang::t('users.error_self_action');
            header('Location: /users');
            exit;
        }

        $pdo = Database::getConnection();

        // Check if user has computers
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM computers WHERE current_user_id = ?");
        $stmt->execute([$id]);
        $count = (int)$stmt->fetchColumn();

        if ($count > 0) {
            $_SESSION['user_error'] = Lang::t('users.error_has_computers');
            header('Location: /users');
            exit;
        }

        // Delete user
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);

        $_SESSION['user_success'] = Lang::t('users.success_deleted');
        header('Location: /users');
        exit;
    }
}