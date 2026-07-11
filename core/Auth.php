<?php

class Auth
{
    /**
     * Attempt to login user
     */
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

        // Check if user role is 'none' (no access)
        if ($user['role'] === 'none') {
            return ['success' => false, 'error' => 'Access denied'];
        }

        // Login successful - store user in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_login'] = $user['login'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = trim($user['last_name'] . ' ' . $user['first_name']);

        return ['success' => true, 'user' => $user];
    }

    /**
     * Logout user
     */
    public static function logout(): void
    {
        session_destroy();
    }

    /**
     * Check if user is logged in
     */
    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Get current user data
     */
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

    /**
     * Check if user has specific role
     */
    public static function hasRole(string $role): bool
    {
        return self::check() && $_SESSION['user_role'] === $role;
    }

    /**
     * Check if user must change password
     */
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

    /**
     * Change user password
     */
    public static function changePassword(int $userId, string $newPassword): bool
    {
        $pdo = Database::getConnection();
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, must_change_pwd = 0 WHERE id = ?");
        return $stmt->execute([$hash, $userId]);
    }

    /**
     * Require authentication (redirect to login if not authenticated)
     */
    public static function requireAuth(): void
    {
        if (!self::check()) {
            header('Location: /login');
            exit;
        }

        // Check if must change password
        if (self::mustChangePassword() && $_SERVER['REQUEST_URI'] !== '/change-password') {
            header('Location: /change-password');
            exit;
        }
    }

    /**
     * Require specific role
     */
    public static function requireRole(string $role): void
    {
        self::requireAuth();

        if (!self::hasRole($role)) {
            http_response_code(403);
            echo "<h1>403 Forbidden</h1><p>You don't have permission to access this page.</p>";
            exit;
        }
    }

    /**
     * Require specific role, redirect to dashboard if not matched
     */
    public static function requireRole(string $role): void
    {
        if (!self::check()) {
            header('Location: /login');
            exit;
        }

        if ($_SESSION['user_role'] !== $role) {
            header('Location: /dashboard');
            exit;
        }
    }
}