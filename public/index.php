<?php
// Single entry point for the application

// Uncomment to catch the errors:
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

// Load configuration
require_once __DIR__ . '/../config/config.php';

// Load core components
require_once ROOT_PATH . '/core/Database.php';
require_once ROOT_PATH . '/core/Lang.php';
require_once ROOT_PATH . '/core/View.php';
require_once ROOT_PATH . '/core/Auth.php';
require_once ROOT_PATH . '/core/Autoloader.php';
require_once ROOT_PATH . '/core/App.php';

// Register autoloader for services and other classes
Autoloader::registerDirectory(ROOT_PATH . '/services');
Autoloader::registerDirectory(ROOT_PATH . '/controllers');
Autoloader::register();

// Load routes
require_once ROOT_PATH . '/config/routes.php';

// Run the application
App::run();