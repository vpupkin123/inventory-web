<?php
// Single entry point for the application

// Load configuration
require_once __DIR__ . '/../config/config.php';

// Load core components
require_once ROOT_PATH . '/core/Database.php';
require_once ROOT_PATH . '/core/View.php';
require_once ROOT_PATH . '/core/Auth.php';
require_once ROOT_PATH . '/core/App.php';

// Load routes
require_once ROOT_PATH . '/config/routes.php';

// Run the application
App::run();