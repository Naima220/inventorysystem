<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Shop;
use App\Models\User;

echo "--- CENTRAL SHOPS ---\n";
try {
    $shops = Shop::all();
    foreach ($shops as $shop) {
        echo "Shop ID: " . $shop->id . "\n";
        echo "  Name: " . $shop->name . "\n";
        echo "  Owner: " . $shop->owner_name . "\n";
        echo "  Admin Email: " . $shop->admin_email . "\n";
        echo "  Admin Password (raw/plain): " . $shop->admin_password . "\n";
        echo "  Domains:\n";
        foreach ($shop->domains as $d) {
            echo "    - " . $d->domain . "\n";
        }
        
        echo "  Checking Database Connection...\n";
        try {
            tenancy()->initialize($shop);
            echo "    Initialized successfully!\n";
            $users = User::all();
            echo "    Users in tenant DB:\n";
            foreach ($users as $user) {
                echo "      - Name: " . $user->name . " | Email: " . $user->email . " | Password Hash: " . $user->password . "\n";
                // Let's verify password if we can
                if (Hash::check($shop->admin_password, $user->password)) {
                    echo "        [OK] Password matches admin_password\n";
                } else {
                    echo "        [WARNING] Password DOES NOT match central admin_password!\n";
                }
                
                // Print roles if Spatie is loaded
                try {
                    $roles = $user->roles->pluck('name')->toArray();
                    echo "        Roles: " . implode(', ', $roles) . "\n";
                } catch (\Exception $re) {
                    echo "        Role check error: " . $re->getMessage() . "\n";
                }
            }
            tenancy()->end();
        } catch (\Exception $e) {
            echo "    Connection failed: " . $e->getMessage() . "\n";
            if (tenancy()->initialized) {
                tenancy()->end();
            }
        }
        echo "\n";
    }
} catch (\Exception $e) {
    echo "Error querying central shops: " . $e->getMessage() . "\n";
}
