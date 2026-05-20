<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Shop;

try {
    // Delete existing testshop if any to allow re-runs
    $existing = Shop::find('testshop');
    if ($existing) {
        $existing->delete();
    }

    $shop = Shop::create([
        'id'                    => 'testshop',
        'name'                  => 'Test Shop',
        'owner_name'            => 'Test Owner',
        'phone'                 => '123456',
        'is_active'             => 1,
        'subscription_starts_at'=> now(),
        'subscription_ends_at'  => now()->addDays(30),
        'admin_email'           => 'testadmin@gmail.com',
        'admin_password'        => 'password',
    ]);
    echo "Shop created successfully!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
