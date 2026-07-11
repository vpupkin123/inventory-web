<?php

/**
 * Simple class autoloader
 * Searches for class files in core/ and services/ directories
 */
class Autoloader
{
    private static array $directories = [];

    /**
     * Register a directory to search for classes
     */
    public static function registerDirectory(string $directory): void
    {
        if (is_dir($directory)) {
            self::$directories[] = rtrim($directory, '/');
        }
    }

    /**
     * Autoload a class
     */
    public static function load(string $className): bool
    {
        foreach (self::$directories as $directory) {
            $file = $directory . '/' . $className . '.php';
            if (file_exists($file)) {
                require_once $file;
                return true;
            }
        }
        return false;
    }

    /**
     * Register the autoloader
     */
    public static function register(): void
    {
        spl_autoload_register([self::class, 'load']);
    }
}