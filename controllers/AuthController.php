<?php

class AuthController
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            header('Location: /dashboard');
            exit;
        }

        View::render('auth/login', [
            'error' => $_SESSION['login_error'] ?? null,
            'old_login' => $_SESSION['old_login'] ?? ''
        ]);

        unset($_SESSION['login_error'], $_SESSION['old_login']);
    }

    public function login(): void
    {
        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($login) || empty($password)) {
            $_SESSION['login_error'] = Lang::t('login.error_empty');
            $_SESSION['old_login'] = $login;
            header('Location: /login');
            exit;
        }

        $result = Auth::attempt($login, $password);

        if ($result['success']) {
            // <-- ВАЖНО: Редирект на правильный маршрут
            if ($result['user']['must_change_pwd'] == 1) {
                header('Location: /change-initial-password');
            } else {
                header('Location: /dashboard');
            }
            exit;
        } else {
            $errorKey = $result['error'] === 'Access denied' 
                ? 'login.error_access_denied' 
                : 'login.error_invalid';
            $_SESSION['login_error'] = Lang::t($errorKey);
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

    public function showChangeInitialPassword(): void
    {
        Auth::requireAuth();

        View::render('auth/change-initial-password', [
            'error' => $_SESSION['password_error'] ?? null,
            'success' => $_SESSION['password_success'] ?? null
        ]);

        unset($_SESSION['password_error'], $_SESSION['password_success']);
    }

    public function changeInitialPassword(): void
    {
        Auth::requireAuth();

        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($newPassword) || empty($confirmPassword)) {
            $_SESSION['password_error'] = Lang::t('change_password.error_empty');
            header('Location: /change-initial-password');
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['password_error'] = Lang::t('change_password.error_mismatch');
            header('Location: /change-initial-password');
            exit;
        }

        if (strlen($newPassword) < 6) {
            $_SESSION['password_error'] = Lang::t('change_password.error_short');
            header('Location: /change-initial-password');
            exit;
        }

        if (Auth::changePassword($_SESSION['user_id'], $newPassword)) {
            $_SESSION['password_success'] = Lang::t('change_password.success');
            // <-- ВАЖНО: Редирект на главную после успешной смены (Решение Пункта 2)
            header('Location: /dashboard');
            exit;
        } else {
            $_SESSION['password_error'] = Lang::t('change_password.error_failed');
            header('Location: /change-initial-password');
            exit;
        }
    }
}