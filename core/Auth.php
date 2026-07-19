<?php

class Auth
{
    public static function attempt(string $login, string $password): array
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT * FROM users WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch();

        if (!$user) {
            return ['success' => false, 'error' => 'Invalid login or password'];
        }

        if (!password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'error' => 'Invalid login or password'];
        }

        if ($user['role'] === 'none') {
            return ['success' => false, 'error' => 'Access denied'];
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_login'] = $user['login'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = trim($user['last_name'] . ' ' . $user['first_name']);

        return ['success' => true, 'user' => $user];
    }

    public static function logout(): void
    {
        session_destroy();
    }

    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'login' => $_SESSION['user_login'],
            'role' => $_SESSION['user_role'],
            'name' => $_SESSION['user_name']
        ];
    }

    public static function hasRole(string $role): bool
    {
        return self::check() && $_SESSION['user_role'] === $role;
    }

    public static function mustChangePassword(): bool
    {
        if (!self::check()) {
            return false;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT must_change_pwd FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $row = $stmt->fetch();

        return $row && $row['must_change_pwd'] == 1;
    }

    public static function changePassword(int $userId, string $newPassword): bool
    {
        $pdo = Database::getConnection();
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, must_change_pwd = 0 WHERE id = ?");
        return $stmt->execute([$hash, $userId]);
    }

    public static function requireAuth(): void
    {
        if (!self::check()) {
            header('Location: /login');
            exit;
        }

        // Check if must change password (ИСПРАВЛЕНО ЗДЕСЬ)
        if (self::mustChangePassword() && $_SERVER['REQUEST_URI'] !== '/change-initial-password') {
            header('Location: /change-initial-password');
            exit;
        }
    }

    public static function requireRole(string $role): void
    {
        self::requireAuth();

        if (!self::hasRole($role)) {
            http_response_code(403);
            echo "<h1>403 Forbidden</h1><p>You don't have permission to access this page.</p>";
            exit;
        }
    }
}