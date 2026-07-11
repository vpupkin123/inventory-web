<?php

class LangController
{
    public function switch(): void
    {
        $locale = $_POST['locale'] ?? 'en';

        // Initialize session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        Lang::setLocale($locale);

        // Redirect back to the page user was on
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        header('Location: ' . $referer);
        exit;
    }
}