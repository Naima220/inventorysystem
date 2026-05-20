<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Shop;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class MigrateShopsToTenants extends Command
{
    protected $signature = 'app:migrate-shops';
    protected $description = 'Migrate existing shops and users to multi-database tenancy';

    public function handle()
    {
        $this->info('Starting migration...');

        // 1. Get existing shops from old table
        $oldShops = DB::table('shops')->get();

        foreach ($oldShops as $oldShop) {
            $this->comment("Processing Shop: {$oldShop->name}");

            // Create a slug for the tenant ID
            $tenantId = Str::slug($oldShop->name);
            
            // Avoid duplicate IDs
            if (Shop::where('id', $tenantId)->exists()) {
                $tenantId = $tenantId . '-' . $oldShop->id;
            }

            // Create the new Tenant (Shop) if not exists
            $shop = Shop::find($tenantId);
            if (!$shop) {
                $shop = Shop::create([
                    'id' => $tenantId,
                    'name' => $oldShop->name,
                    'owner_name' => $oldShop->owner_name,
                    'phone' => $oldShop->phone,
                    'is_active' => $oldShop->is_active,
                    'subscription_starts_at' => $oldShop->subscription_starts_at,
                    'subscription_ends_at' => $oldShop->subscription_ends_at,
                ]);

                // Create Domain dynamically based on APP_URL
                $appUrl = config('app.url');
                $centralDomain = parse_url($appUrl, PHP_URL_HOST) ?? 'localhost';
                $centralDomain = explode(':', $centralDomain)[0];

                $shop->domains()->create([
                    'domain' => strtolower($tenantId) . '.' . $centralDomain
                ]);
                $this->info("Created Tenant and Domain for {$oldShop->name}");
            } else {
                $this->comment("Tenant {$tenantId} already exists, skipping creation.");
            }

            // 2. Get users for this shop from central DB
            $oldUsers = DB::table('users')->where('shop_id', $oldShop->id)->get();

            // 3. Initialize tenancy and move users
            tenancy()->initialize($shop);
            
            // Create Roles in Tenant DB
            $adminRole = Role::firstOrCreate(['name' => 'Admin']);
            $userRole = Role::firstOrCreate(['name' => 'User']);

            foreach ($oldUsers as $oldUser) {
                // Check if user exists in Tenant DB
                $user = User::where('email', $oldUser->email)->first();
                if (!$user) {
                    $user = User::create([
                        'name' => $oldUser->name,
                        'email' => $oldUser->email,
                        'password' => $oldUser->password, // Already hashed
                        'created_at' => $oldUser->created_at,
                        'updated_at' => $oldUser->updated_at,
                    ]);
                    $this->info("Created user {$oldUser->email} in tenant DB");
                }

                if (Str::contains(strtolower($oldUser->name), 'admin')) {
                    $user->syncRoles([$adminRole]);
                } else {
                    $user->syncRoles([$userRole]);
                }
            }

            tenancy()->end();

            $this->info("Migrated " . count($oldUsers) . " users for {$oldShop->name}");
        }

        $this->info('Migration completed successfully!');
    }
}
