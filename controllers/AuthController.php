<?php

class AuthController
{
    public function showLogin(): void
    {
        // If already logged in, redirect to dashboard
        if (Auth::check()) {
            header('Location: /dashboard');
            exit;
        }

        View::render('auth/login', [
            'error' => $_SESSION['login_error'] ?? null,
            'old_login' => $_SESSION['old_login'] ?? ''
        ]);

        // Clear session data
        unset($_SESSION['login_error'], $_SESSION['old_login']);
    }

    public function login(): void
    {
        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($login) || empty($password)) {
            $_SESSION['login_error'] = 'Please enter login and password';
            $_SESSION['old_login'] = $login;
            header('Location: /login');
            exit;
        }

        $result = Auth::attempt($login, $password);

        if ($result['success']) {
            // Check if must change password
            if ($result['user']['must_change_pwd'] == 1) {
                header('Location: /change-password');
            } else {
                header('Location: /dashboard');
            }
            exit;
        } else {
            $_SESSION['login_error'] = $result['error'];
            $_SESSION['old_login'] = $login;
            header('Location: /login');
            exit;
        }
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: /login');
        exit;
    }

    public function showChangePassword(): void
    {
        Auth::requireAuth();

        View::render('auth/change_password', [
            'error' => $_SESSION['password_error'] ?? null,
            'success' => $_SESSION['password_success'] ?? null
        ]);

        unset($_SESSION['password_error'], $_SESSION['password_success']);
    }

    public function changePassword(): void
    {
        Auth::requireAuth();

        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($newPassword) || empty($confirmPassword)) {
            $_SESSION['password_error'] = 'Please fill in all fields';
            header('Location: /change-password');
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['password_error'] = 'Passwords do not match';
            header('Location: /change-password');
            exit;
        }

        if (strlen($newPassword) < 6) {
            $_SESSION['password_error'] = 'Password must be at least 6 characters';
            header('Location: /change-password');
            exit;
        }

        if (Auth::changePassword($_SESSION['user_id'], $newPassword)) {
            $_SESSION['password_success'] = 'Password changed successfully';
            header('Location: /change-password');
            exit;
        } else {
            $_SESSION['password_error'] = 'Failed to change password';
            header('Location: /change-password');
            exit;
        }
    }
}