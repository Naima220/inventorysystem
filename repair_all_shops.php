<?php
/**
 * REPAIR ALL SHOPS — Direct PDO, no Laravel bootstrap
 * Usage: php repair_all_shops.php
 */

$mysqlHost = '127.0.0.1';
$mysqlPort = '3306';
$mysqlDb   = 'ims';
$mysqlUser = 'root';
$mysqlPass = '';

echo "=== CONNECTING TO MYSQL ===\n";
try {
    $mysql = new PDO("mysql:host={$mysqlHost};port={$mysqlPort};dbname={$mysqlDb};charset=utf8", $mysqlUser, $mysqlPass);
    $mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "  Connected OK\n\n";
} catch (Exception $e) {
    die("[ERROR] MySQL failed: " . $e->getMessage() . "\n");
}

// Load all shops
$shops = $mysql->query("SELECT * FROM tenants ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
echo "Found " . count($shops) . " shops.\n\n";

foreach ($shops as $shop) {
    $shopId  = $shop['id'];
    $data    = json_decode($shop['data'] ?? '{}', true) ?? [];
    $email   = $data['admin_email']    ?? null;
    $rawPass = $data['admin_password'] ?? null;
    $name    = $shop['owner_name']     ?? 'Admin';

    // Determine SQLite path — stancl uses prefix + shop_id
    // Check both naming conventions (with and without tenancy_db_name)
    $dbName  = $data['tenancy_db_name'] ?? ('tenant' . $shopId);
    $dbPath  = __DIR__ . '/database/' . $dbName;

    echo "=== Shop: {$shopId} | {$shop['name']} ===\n";
    echo "  DB path : {$dbPath}\n";
    echo "  Email   : " . ($email ?? '[MISSING]') . "\n";
    echo "  Password: " . ($rawPass ?? '[MISSING]') . "\n";

    if (!$email) {
        echo "  [SKIP] No admin_email found — cannot repair\n\n";
        continue;
    }

    // Create SQLite file if missing
    if (!file_exists($dbPath)) {
        echo "  [CREATE] SQLite file missing — creating...\n";
        if (!is_dir(dirname($dbPath))) {
            mkdir(dirname($dbPath), 0755, true);
        }
        touch($dbPath);
    } else {
        echo "  [OK] SQLite file exists\n";
    }

    // Connect to tenant SQLite
    try {
        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $e) {
        echo "  [ERROR] SQLite connect failed: " . $e->getMessage() . "\n\n";
        continue;
    }

    // Ensure tables exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            email_verified_at DATETIME,
            password TEXT NOT NULL,
            remember_token TEXT,
            created_at DATETIME,
            updated_at DATETIME
        )
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS roles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            guard_name TEXT NOT NULL DEFAULT 'web',
            created_at DATETIME,
            updated_at DATETIME,
            UNIQUE(name, guard_name)
        )
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS model_has_roles (
            role_id INTEGER NOT NULL,
            model_type TEXT NOT NULL,
            model_id INTEGER NOT NULL,
            PRIMARY KEY (role_id, model_type, model_id)
        )
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS permissions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            guard_name TEXT NOT NULL DEFAULT 'web',
            created_at DATETIME,
            updated_at DATETIME
        )
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS model_has_permissions (
            permission_id INTEGER NOT NULL,
            model_type TEXT NOT NULL,
            model_id INTEGER NOT NULL,
            PRIMARY KEY (permission_id, model_type, model_id)
        )
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS role_has_permissions (
            permission_id INTEGER NOT NULL,
            role_id INTEGER NOT NULL,
            PRIMARY KEY (permission_id, role_id)
        )
    ");

    // Seed roles
    $pdo->exec("INSERT OR IGNORE INTO roles (name, guard_name, created_at, updated_at) VALUES ('Admin','web',datetime('now'),datetime('now'))");
    $pdo->exec("INSERT OR IGNORE INTO roles (name, guard_name, created_at, updated_at) VALUES ('User','web',datetime('now'),datetime('now'))");

    // Insert or update admin user
    $hashed = password_hash($rawPass, PASSWORD_BCRYPT);

    // Check if user exists
    $existing = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $existing->execute([$email]);
    $existingUser = $existing->fetchColumn();

    if ($existingUser) {
        // Update password
        $upd = $pdo->prepare("UPDATE users SET password = ?, updated_at = datetime('now') WHERE email = ?");
        $upd->execute([$hashed, $email]);
        $userId = $existingUser;
        echo "  [UPDATE] User updated: {$email}\n";
    } else {
        // Insert
        $ins = $pdo->prepare("INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, datetime('now'), datetime('now'))");
        $ins->execute([$name, $email, $hashed]);
        $userId = $pdo->lastInsertId();
        echo "  [INSERT] User inserted: {$email} (ID: {$userId})\n";
    }

    // Assign Admin role
    $roleId = $pdo->query("SELECT id FROM roles WHERE name = 'Admin'")->fetchColumn();
    if ($userId && $roleId) {
        $pdo->exec("INSERT OR IGNORE INTO model_has_roles (role_id, model_type, model_id) VALUES ({$roleId}, 'App\\\\Models\\\\User', {$userId})");
        echo "  [ROLE] Admin role assigned (user_id={$userId}, role_id={$roleId})\n";
    }

    // Verify
    if (password_verify($rawPass, $hashed)) {
        echo "  [VERIFY] Password hash OK\n";
    } else {
        echo "  [ERROR] Password hash FAILED!\n";
    }

    echo "  => Done: {$email} / {$rawPass}\n\n";
}

echo "=== ALL SHOPS REPAIRED ===\n";
echo "\nLogin credentials summary:\n";
foreach ($shops as $shop) {
    $data    = json_decode($shop['data'] ?? '{}', true) ?? [];
    $email   = $data['admin_email']    ?? 'N/A';
    $rawPass = $data['admin_password'] ?? 'N/A';
    echo "  {$shop['id']}: {$email} / {$rawPass}\n";
}
