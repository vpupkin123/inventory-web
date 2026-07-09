<?php
// Single entry point for the application

// Load configuration
require_once __DIR__ . '/../config/config.php';

// Load core components
require_once ROOT_PATH . '/core/Database.php';

// Simple test: initialize database and display info
try {
    $pdo = Database::getConnection();
    $warehouseId = Database::getWarehouseId();

    // Count users
    $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

    echo "<!DOCTYPE html>";
    echo "<html><head><meta charset='UTF-8'><title>Inventory Web</title></head><body>";
    echo "<h1>Inventory Web v" . APP_VERSION . "</h1>";
    echo "<p>✅ Database initialized successfully</p>";
    echo "<p>📁 Database location: <code>" . DB_FILE . "</code></p>";
    echo "<p>👥 Users in system: <strong>{$userCount}</strong></p>";
    echo "<p>📦 Virtual warehouse ID: <strong>{$warehouseId}</strong></p>";
    echo "<hr>";
    echo "<h3>Created users:</h3>";
    echo "<ul>";

    $stmt = $pdo->query("SELECT login, last_name, first_name, role FROM users ORDER BY id");
    while ($row = $stmt->fetch()) {
        $display = trim($row['last_name'] . ' ' . $row['first_name']);
        echo "<li><strong>{$row['login']}</strong> ({$display}) — role: {$row['role']}</li>";
    }

    echo "</ul>";
    echo "<p><em>Next step: add login form.</em></p>";
    echo "</body></html>";

} catch (Exception $e) {
    http_response_code(500);
    echo "<h1>Initialization Error</h1>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}