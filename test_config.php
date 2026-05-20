<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Shop;
use App\Models\User;

try {
    $shop = Shop::find('testshop');
    if (!$shop) {
        echo "No testshop found in central DB!\n";
        exit;
    }
    echo "Initializing tenancy for shop: " . $shop->id . "\n";
    tenancy()->initialize($shop);
    echo "Tenancy initialized successfully!\n";
    
    $users = User::all();
    echo "Users inside tenant DB:\n";
    foreach ($users as $u) {
        echo " - Name: {$u->name}, Email: {$u->email}, Roles: " . implode(', ', $u->roles->pluck('name')->toArray()) . "\n";
    }
    
    tenancy()->end();
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
