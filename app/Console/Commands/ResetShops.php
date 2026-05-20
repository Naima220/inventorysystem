<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Shop;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class ResetShops extends Command
{
    protected $signature = 'app:reset-shops';
    protected $description = 'Delete all tenants and create clean shop1, shop2, shop3';

    public function handle()
    {
        $this->info('Cleaning up existing tenants...');

        // 1. Delete all tenants (This will delete their databases too)
        $tenants = Shop::all();
        foreach ($tenants as $tenant) {
            $this->comment("Deleting tenant: {$tenant->id}");
            $tenant->delete();
        }

        $this->info('Creating new clean shops...');

        $newShops = [
            [
                'id' => 'shop1',
                'name' => 'Happy Brand',
                'owner_name' => 'Happy Yusuf',
                'phone' => '+252634421157',
                'admin_email' => 'happy@gmail.com',
            ],
            [
                'id' => 'shop2',
                'name' => 'Thoub Abaya',
                'owner_name' => 'Muna',
                'phone' => '+252634157296',
                'admin_email' => 'muna@gmail.com',
            ],
            [
                'id' => 'shop3',
                'name' => 'Samartu Al Janha',
                'owner_name' => 'Niama Hassan',
                'phone' => '+252634749260',
                'admin_email' => 'niama@gmail.com',
            ],
        ];

        foreach ($newShops as $data) {
            $this->comment("Creating {$data['id']}...");

            $shop = Shop::create([
                'id' => $data['id'],
                'name' => $data['name'],
                'owner_name' => $data['owner_name'],
                'phone' => $data['phone'],
                'is_active' => 1,
                'subscription_starts_at' => now(),
                'subscription_ends_at' => now()->addDays(30),
                'admin_email' => $data['admin_email'],
                'admin_password' => 'password',
            ]);

            // Create Domain dynamically based on APP_URL
            $appUrl = config('app.url');
            $centralDomain = parse_url($appUrl, PHP_URL_HOST) ?? 'localhost';
            $centralDomain = explode(':', $centralDomain)[0];

            $shop->domains()->create([
                'domain' => strtolower($data['id']) . '.' . $centralDomain
            ]);

            // Initialize and seed first user
            tenancy()->initialize($shop);
            
            // Create Roles
            $adminRole = Role::firstOrCreate(['name' => 'Admin']);
            Role::firstOrCreate(['name' => 'User']);

            // Create Admin User
            User::create([
                'name' => $data['owner_name'],
                'email' => $data['admin_email'],
                'password' => Hash::make('password'),
            ])->assignRole($adminRole);

            tenancy()->end();

            $this->info("Successfully created {$data['id']} with domain {$data['id']}.{$centralDomain}");
        }

        $this->info('All shops have been reset and recreated as clean databases!');
    }
}
