<?php
/**
 * SYNC TENANT MIGRATIONS
 * Automatically marks existing tables as migrated to avoid "table already exists" errors.
 */

function readEnv(string $path): array {
    $vars = [];
    if (!file_exists($path)) return $vars;
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (!str_contains($line, '=')) continue;
        [$key, $val] = explode('=', $line, 2);
        $vars[trim($key)] = trim($val, " \t\"'");
    }
    return $vars;
}

$env = readEnv(__DIR__ . '/.env');

$mysqlHost   = $env['DB_HOST']     ?? '127.0.0.1';
$mysqlPort   = $env['DB_PORT']     ?? '3306';
$mysqlDb     = $env['DB_DATABASE'] ?? 'ims';
$mysqlUser   = $env['DB_USERNAME'] ?? 'root';
$mysqlPass   = $env['DB_PASSWORD'] ?? '';
$dbPrefix    = $env['TENANT_DB_PREFIX'] ?? 'tenant';

try {
    $mysql = new PDO("mysql:host={$mysqlHost};port={$mysqlPort};dbname={$mysqlDb};charset=utf8", $mysqlUser, $mysqlPass);
    $mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("[ERROR] MySQL failed: " . $e->getMessage() . "\n");
}

$shops = $mysql->query("SELECT * FROM tenants ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

// Define migration names and their associated table checks
$migrationChecks = [
    '2014_10_12_000000_create_users_table' => 'users',
    '2021_03_13_091736_create_products_table' => 'products',
    '2025_06_19_232117_create_suppliers_table' => 'suppliers',
    '2025_06_19_232809_create_customers_table' => 'customers',
    '2025_06_26_195329_create_employees_table' => 'employees',
    '2025_06_27_210157_create_salaries_table' => 'salaries',
    '2025_07_03_165019_create_permission_tables' => 'roles',
    '2025_07_12_001031_create_orders_table' => 'orders',
    '2025_07_12_001244_create_order_items_table' => 'order_items',
    '2025_07_17_224132_create_invoices_table' => 'invoices',
    '2025_07_18_194218_create_invoice_items_table' => 'invoice_items',
    '2025_07_20_013920_create_payment_items_table' => 'payment_items',
    '2025_07_20_163500_create_payments_table' => 'payments',
    '2025_07_20_230047_create_supplier_purchases_table' => 'supplier_purchases',
    '2025_07_20_230554_create_supplier_purchase_items_table' => 'supplier_purchase_items',
    '2025_07_21_085604_create_expenses_table' => 'expenses',
    '2026_03_03_104048_create_activity_logs_table' => 'activity_logs',
    '2026_04_02_231356_create_debts_table' => 'debts',
    '2026_04_02_232014_create_debt_payments_table' => 'debt_payments',
];

foreach ($shops as $shop) {
    $shopId = $shop['id'];
    $data = json_decode($shop['data'] ?? '{}', true) ?? [];
    $dbName = $data['tenancy_db_name'] ?? ($dbPrefix . $shopId);
    $dbPath = __DIR__ . '/database/' . $dbName;

    echo "=== Checking Shop: {$shopId} ({$shop['name']}) ===\n";

    if (!file_exists($dbPath)) {
        echo "  Database file not found: {$dbPath}\n";
        continue;
    }

    try {
        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $e) {
        echo "  Failed to connect: " . $e->getMessage() . "\n";
        continue;
    }

    // Ensure migrations table exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS migrations (id INTEGER PRIMARY KEY AUTOINCREMENT, migration TEXT NOT NULL UNIQUE, batch INTEGER NOT NULL)");

    // Run checks
    foreach ($migrationChecks as $migration => $table) {
        // Check if the table exists in SQLite
        $stmt = $pdo->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name=?");
        $stmt->execute([$table]);
        $tableExists = $stmt->fetchColumn();

        if ($tableExists) {
            // Insert into migrations table if it doesn't already exist
            $checkMig = $pdo->prepare("SELECT id FROM migrations WHERE migration = ?");
            $checkMig->execute([$migration]);
            if (!$checkMig->fetchColumn()) {
                $ins = $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, 1)");
                $ins->execute([$migration]);
                echo "  [ADDED] Marked migration as done: {$migration}\n";
            } else {
                echo "  [OK] Already marked: {$migration}\n";
            }
        } else {
            echo "  [SKIP] Table '{$table}' does not exist, leaving migration: {$migration}\n";
        }
    }
    echo "  Done for {$shop['name']}.\n\n";
}
echo "=== ALL SHOPS SYNCED ===\n";
