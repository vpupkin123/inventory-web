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

        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';
        $lastName = trim($_POST['last_name'] ?? '');
        $firstName = trim($_POST['first_name'] ?? '');
        $middleName = trim($_POST['middle_name'] ?? '');
        $role = $_POST['role'] ?? 'viewer';

        if (empty($login) || empty($password) || empty($lastName) || empty($firstName)) {
            $_SESSION['user_error'] = Lang::t('change_password.error_empty');
            header('Location: /users/create');
            exit;
        }

        if (!in_array($role, ['admin', 'editor', 'viewer', 'none'])) {
            $role = 'viewer';
        }

        $pdo = Database::getConnection();

        // Check duplicate login
        $stmt = $pdo->prepare("SELECT id FROM users WHERE login = ?");
        $stmt->execute([$login]);
        if ($stmt->fetch()) {
            $_SESSION['user_error'] = Lang::t('users.error_login_exists');
            header('Location: /users/create');
            exit;
        }

        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                INSERT INTO users (login, password_hash, last_name, first_name, middle_name, role, must_change_pwd)
                VALUES (?, ?, ?, ?, ?, ?, 1)
            ");
            $stmt->execute([$login, $hash, $lastName, $firstName, $middleName, $role]);

            $_SESSION['user_success'] = Lang::t('users.success_created');
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
}