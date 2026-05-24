<?php
// Secret token to prevent unauthorized deployment triggers
$secret_token = 'mine_market_deploy_secret_2026';

if (!isset($_GET['token']) || $_GET['token'] !== $secret_token) {
    header('HTTP/1.0 403 Forbidden');
    echo 'Access Denied: Invalid Token';
    exit;
}

// Set execution time limit to 10 minutes
set_time_limit(600);

header('Content-Type: text/html; charset=utf-8');
echo "<h2>🚀 Starting Deployment...</h2>";

// Change directory to project root (one level up from public/)
chdir(__DIR__ . '/..');

echo "<pre>";
echo "Current working directory: " . getcwd() . "\n\n";

// 1. Execute git pull
echo "🔄 === 1. GIT PULL ===\n";
$gitOutput = shell_exec('git pull origin main 2>&1');
echo htmlspecialchars($gitOutput) . "\n";

// 2. Execute migrations for tenants
echo "🗄️ === 2. RUN TENANT MIGRATIONS ===\n";
$migrationsOutput = shell_exec('php artisan tenants:migrate --force 2>&1');
echo htmlspecialchars($migrationsOutput) . "\n";

// 3. Clear view cache
echo "🧹 === 3. CLEAR VIEW CACHE ===\n";
$viewOutput = shell_exec('php artisan view:clear 2>&1');
echo htmlspecialchars($viewOutput) . "\n";

// 4. Clear config cache
echo "🧹 === 4. CLEAR CONFIG CACHE ===\n";
$configOutput = shell_exec('php artisan config:clear 2>&1');
echo htmlspecialchars($configOutput) . "\n";

echo "\n✅ Deployment Finished Successfully!";
echo "</pre>";
