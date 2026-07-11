<?php

class Lang
{
    private static array $translations = [];
    private static string $currentLocale = 'en';
    private static bool $loaded = false;

    /**
     * Initialize language system
     */
    public static function init(): void
    {
        // Check session for saved language preference
        if (isset($_SESSION['locale'])) {
            self::$currentLocale = $_SESSION['locale'];
        } else {
            // Check cookie
            if (isset($_COOKIE['locale'])) {
                self::$currentLocale = $_COOKIE['locale'];
            } else {
                // Check browser language
                $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en', 0, 2);
                self::$currentLocale = in_array($browserLang, ['en', 'ru']) ? $browserLang : 'en';
            }
        }

        self::load(self::$currentLocale);
    }

    /**
     * Load translations for given locale
     */
    private static function load(string $locale): void
    {
        $file = ROOT_PATH . "/lang/{$locale}.php";

        if (!file_exists($file)) {
            $file = ROOT_PATH . '/lang/en.php'; // Fallback to English
        }

        self::$translations = require $file;
        self::$currentLocale = $locale;
        self::$loaded = true;

        // Save to session and cookie
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['locale'] = $locale;
        }
        setcookie('locale', $locale, time() + (86400 * 30), '/'); // 30 days
    }

    /**
     * Get translated string
     */
    public static function get(string $key, array $params = []): string
    {
        if (!self::$loaded) {
            self::init();
        }

        $text = self::$translations[$key] ?? $key;

        // Replace placeholders like :name with values
        if (!empty($params)) {
            foreach ($params as $placeholder => $value) {
                $text = str_replace(':' . $placeholder, $value, $text);
            }
        }

        return $text;
    }

    /**
     * Shorthand alias
     */
    public static function t(string $key, array $params = []): string
    {
        return self::get($key, $params);
    }

    /**
     * Change current language
     */
    public static function setLocale(string $locale): void
    {
        $allowed = ['en', 'ru'];
        if (!in_array($locale, $allowed)) {
            $locale = 'en';
        }

        self::load($locale);
    }

    /**
     * Get current locale
     */
    public static function getLocale(): string
    {
        return self::$currentLocale;
    }

    /**
     * Check if current locale is given one
     */
    public static function is(string $locale): bool
    {
        return self::$currentLocale === $locale;
    }
}