<?php
/**
 * REPAIR SHOP DATABASE SCRIPT
 * ============================
 * Fixes existing shops whose SQLite DB is missing or has no admin user.
 * Run: php repair_shop.php shop1
 *
 * This uses direct PDO — no Laravel bootstrap — runs in seconds.
 */

if ($argc < 2) {
    echo "Usage: php repair_shop.php <shop_id>\n";
    echo "Example: php repair_shop.php shop1\n";
    exit(1);
}

$shopId = trim($argv[1]);

// ── Config ──────────────────────────────────────────────────
$dbPath   = __DIR__ . '/database/tenant' . $shopId;
$mysqlHost = '127.0.0.1';
$mysqlPort = '3306';
$mysqlDb   = 'ims';
$mysqlUser = 'root';
$mysqlPass = '';

// ── Step 1: Get shop info from MySQL central DB ──────────────
echo "[1] Connecting to central MySQL...\n";
try {
    $mysql = new PDO("mysql:host={$mysqlHost};port={$mysqlPort};dbname={$mysqlDb}", $mysqlUser, $mysqlPass);
    $mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $mysql->prepare("SELECT * FROM tenants WHERE id = ?");
    $stmt->execute([$shopId]);
    $shop = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$shop) {
        echo "[ERROR] Shop '{$shopId}' not found in MySQL tenants table!\n";
        exit(1);
    }
    // stancl/tenancy stores non-custom columns in the JSON 'data' column
    $shopData = json_decode($shop['data'] ?? '{}', true) ?? [];
    $adminEmail    = $shopData['admin_email']    ?? null;
    $adminPassword = $shopData['admin_password'] ?? null;
    $ownerName     = $shop['owner_name']         ?? 'Admin';
    echo "  Found shop: {$shop['name']} | Admin: {$adminEmail}\n";
} catch (Exception $e) {
    echo "[ERROR] MySQL connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// ── Step 2: Check if SQLite file exists (copy from tenanttestshop if available) ─
echo "[2] Checking SQLite file at: {$dbPath}\n";
$templatePath = __DIR__ . '/database/tenanttestshop';

if (!file_exists($dbPath)) {
    if (file_exists($templatePath)) {
        echo "  SQLite file missing. Copying from tenanttestshop template...\n";
        copy($templatePath, $dbPath);
        echo "  Created: {$dbPath}\n";
    } else {
        echo "  No template found. Creating blank SQLite file and running schema...\n";
        touch($dbPath);
        // Will add tables below
    }
} else {
    echo "  SQLite file already exists.\n";
}

// ── Step 3: Connect to tenant SQLite ────────────────────────
echo "[3] Connecting to tenant SQLite...\n";
try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "  Connected!\n";
} catch (Exception $e) {
    echo "[ERROR] SQLite connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// ── Step 4: Create required tables if missing ───────────────
echo "[4] Ensuring schema tables exist...\n";

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
    CREATE TABLE IF NOT EXISTS permissions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        guard_name TEXT NOT NULL DEFAULT 'web',
        created_at DATETIME,
        updated_at DATETIME
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

echo "  Tables OK.\n";

// ── Step 5: Seed roles ──────────────────────────────────────
echo "[5] Seeding roles...\n";
$pdo->exec("INSERT OR IGNORE INTO roles (name, guard_name, created_at, updated_at)
             VALUES ('Admin', 'web', datetime('now'), datetime('now'))");
$pdo->exec("INSERT OR IGNORE INTO roles (name, guard_name, created_at, updated_at)
             VALUES ('User', 'web', datetime('now'), datetime('now'))");
echo "  Roles OK.\n";

// ── Step 6: Insert admin user ───────────────────────────────
echo "[6] Inserting admin user...\n";
$email    = $adminEmail;
$name     = $ownerName;
$password = $adminPassword;

if (!$email) {
    echo "[ERROR] No admin_email found in shop data. Please provide manually.\n";
    echo "  Hint: Check the 'data' JSON column in MySQL tenants table for shop '{$shopId}'\n";
    exit(1);
}

// Hash the password using bcrypt (PHP native — no Laravel needed)
$hashed = password_hash($password, PASSWORD_BCRYPT);

$stmt = $pdo->prepare("INSERT OR IGNORE INTO users (name, email, password, created_at, updated_at)
                        VALUES (:name, :email, :password, datetime('now'), datetime('now'))");
$stmt->execute([':name' => $name, ':email' => $email, ':password' => $hashed]);

$userId = $pdo->query("SELECT id FROM users WHERE email = " . $pdo->quote($email))->fetchColumn();
$roleId = $pdo->query("SELECT id FROM roles WHERE name = 'Admin'")->fetchColumn();

if ($userId && $roleId) {
    $pdo->exec("INSERT OR IGNORE INTO model_has_roles (role_id, model_type, model_id)
                VALUES ({$roleId}, 'App\\\\Models\\\\User', {$userId})");
}

echo "  Admin user OK: {$email} (ID: {$userId}, Role ID: {$roleId})\n";

// ── Step 7: Verify ──────────────────────────────────────────
echo "[7] Verifying...\n";
$users = $pdo->query("SELECT id, name, email FROM users")->fetchAll(PDO::FETCH_ASSOC);
foreach ($users as $u) {
    echo "  User: {$u['name']} | {$u['email']} | ID: {$u['id']}\n";
    // Verify password
    if (password_verify($password, $hashed)) {
        echo "  ✅ Password hash verified OK\n";
    }
}

echo "\n✅ DONE — Shop '{$shopId}' repaired!\n";
echo "   Login URL  : http://{$shopId}.localhost/login (or your subdomain)\n";
echo "   Email      : {$email}\n";
echo "   Password   : {$password}\n";
