<?php
// Application configuration
// All paths are relative to make the project easily portable between servers

// Project root (one level above public/)
define('ROOT_PATH', dirname(__DIR__));

// Database directory
define('DATA_PATH', ROOT_PATH . '/data');

// Full path to database file
define('DB_FILE', DATA_PATH . '/inventory.db');

// Virtual "Warehouse" user login (created during DB initialization)
define('WAREHOUSE_LOGIN', 'warehouse');

// Session settings
define('SESSION_LIFETIME', 8 * 60 * 60); // 8 hours

// Application version
define('APP_VERSION', '0.1.0');

// Default timezone
date_default_timezone_set('Europe/Moscow');