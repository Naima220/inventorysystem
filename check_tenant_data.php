<?php
/**
 * Quick MySQL query — no Laravel bootstrap needed.
 * Shows raw shop data from MySQL tenants table.
 */

$host = '127.0.0.1';
$port = '3306';
$db   = 'ims';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db}", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $rows = $pdo->query("SELECT id, name, owner_name, data, is_active FROM tenants")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
        echo "=== Shop: {$row['id']} ===\n";
        echo "  Name      : {$row['name']}\n";
        echo "  Owner     : {$row['owner_name']}\n";
        echo "  is_active : {$row['is_active']}\n";
        $data = json_decode($row['data'] ?? '{}', true);
        echo "  data JSON : " . print_r($data, true) . "\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
