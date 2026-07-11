<?php

class Database
{
    private static ?PDO $pdo = null;

    /**
     * Get PDO connection (singleton)
     */
    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            // Ensure data directory exists
            if (!is_dir(DATA_PATH)) {
                mkdir(DATA_PATH, 0755, true);
            }

            self::$pdo = new PDO('sqlite:' . DB_FILE);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // Initialize structure and data
            self::initialize();
        }

        return self::$pdo;
    }

    /**
     * Create tables and initial data (idempotent - safe to call multiple times)
     */
    private static function initialize(): void
    {
        $pdo = self::$pdo;

        // Create tables (IF NOT EXISTS - safe to run multiple times)
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id              INTEGER PRIMARY KEY AUTOINCREMENT,
                login           TEXT    UNIQUE NOT NULL,
                password_hash   TEXT,
                last_name       TEXT    NOT NULL,
                first_name      TEXT    NOT NULL,
                middle_name     TEXT    DEFAULT '',
                role            TEXT    NOT NULL CHECK(role IN ('admin','editor','viewer','none')),
                must_change_pwd INTEGER NOT NULL DEFAULT 0,
                created_at      TEXT    NOT NULL DEFAULT (datetime('now','localtime'))
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS computers (
                id              INTEGER PRIMARY KEY AUTOINCREMENT,
                serial_number   TEXT,
                composite_key   TEXT    UNIQUE,
                manufacturer    TEXT,
                model           TEXT,
                motherboard     TEXT,
                cpu_name        TEXT,
                cpu_cores       INTEGER,
                cpu_threads     INTEGER,
                ram_total_gb    REAL,
                ram_details     TEXT,
                storage_info    TEXT,
                os_caption      TEXT,
                os_build        TEXT,
                ip_address      TEXT,
                computer_name   TEXT,
                comment         TEXT    DEFAULT '',
                reported_by     TEXT    DEFAULT '',
                current_user_id INTEGER REFERENCES users(id),
                created_at      TEXT    NOT NULL DEFAULT (datetime('now','localtime')),
                updated_at      TEXT    NOT NULL DEFAULT (datetime('now','localtime'))
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS transfers (
                id              INTEGER PRIMARY KEY AUTOINCREMENT,
                computer_id     INTEGER NOT NULL REFERENCES computers(id),
                from_user_id    INTEGER REFERENCES users(id),
                to_user_id      INTEGER REFERENCES users(id),
                transferred_by  INTEGER NOT NULL REFERENCES users(id),
                comment         TEXT    DEFAULT '',
                transferred_at  TEXT    NOT NULL DEFAULT (datetime('now','localtime'))
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS processed_uploads (
                id              INTEGER PRIMARY KEY AUTOINCREMENT,
                file_name       TEXT    NOT NULL,
                file_hash       TEXT    NOT NULL,
                uploaded_by     INTEGER NOT NULL REFERENCES users(id),
                uploaded_at     TEXT    NOT NULL DEFAULT (datetime('now','localtime'))
            )
        ");

        // Create indexes
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_computers_serial ON computers(serial_number)");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_computers_user ON computers(current_user_id)");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_processed_uploads_hash ON processed_uploads(file_hash)");

        // Migrations for existing databases
        self::migrate();

        // Check if warehouse user exists, create if not
        $stmt = $pdo->prepare("SELECT id FROM users WHERE login = ?");
        $stmt->execute([WAREHOUSE_LOGIN]);
        if (!$stmt->fetch()) {
            $stmt = $pdo->prepare("
                INSERT INTO users (login, password_hash, last_name, first_name, role, must_change_pwd)
                VALUES (?, NULL, 'Warehouse', '', 'none', 0)
            ");
            $stmt->execute([WAREHOUSE_LOGIN]);
        }

        // Check if admin user exists, create if not
        $stmt = $pdo->prepare("SELECT id FROM users WHERE login = ?");
        $stmt->execute(['admin']);
        if (!$stmt->fetch()) {
            $adminHash = password_hash('password', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                INSERT INTO users (login, password_hash, last_name, first_name, role, must_change_pwd)
                VALUES (?, ?, 'Admin', 'Admin', 'admin', 1)
            ");
            $stmt->execute(['admin', $adminHash]);
        }
    }

    /**
     * Run migrations for existing databases (add missing columns)
     */
    private static function migrate(): void
    {
        $pdo = self::$pdo;

        // Add reported_by column to computers table if not exists
        $columns = $pdo->query("PRAGMA table_info(computers)")->fetchAll(PDO::FETCH_ASSOC);
        $columnNames = array_column($columns, 'name');
        
        if (!in_array('reported_by', $columnNames)) {
            $pdo->exec("ALTER TABLE computers ADD COLUMN reported_by TEXT DEFAULT ''");
        }
    }

    /**
     * Get virtual "Warehouse" user ID
     */
    public static function getWarehouseId(): int
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE login = ?");
        $stmt->execute([WAREHOUSE_LOGIN]);
        $row = $stmt->fetch();

        if (!$row) {
            throw new RuntimeException('Warehouse user not found in database');
        }

        return (int)$row['id'];
    }
}