<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Stancl\Tenancy\Events\DatabaseMigrated;
use Illuminate\Support\Facades\Log;

class SetupTenantDatabase
{
    /**
     * Handle the event.
     *
     * @param DatabaseMigrated $event
     * @return void
     */
    public function handle(DatabaseMigrated $event)
    {
        $tenant = $event->tenant;

        try {
            // 1. Initialize tenancy to perform database operations
            tenancy()->initialize($tenant);

            // 2. Clear Spatie Permission Cache
            // This is CRITICAL for multi-tenancy to avoid "Role not found" errors
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            // 3. Create Basic Roles (Admin and User)
            // We specify the guard explicitly to avoid ambiguity
            $adminRole = Role::firstOrCreate(
                ['name' => 'Admin', 'guard_name' => 'web']
            );
            
            Role::firstOrCreate(
                ['name' => 'User', 'guard_name' => 'web']
            );

            // 4. Create the Admin User using data stored in the tenant/shop model
            if ($tenant->admin_email) {
                $user = User::where('email', $tenant->admin_email)->first();
                if (!$user) {
                    $user = User::create([
                        'name'     => $tenant->owner_name ?? 'Admin User',
                        'email'    => $tenant->admin_email,
                        'password' => Hash::make($tenant->admin_password ?? 'password'),
                    ]);

                    // Assign the Admin Role
                    $user->assignRole($adminRole);
                    Log::info("Successfully created admin user {$tenant->admin_email} for tenant: {$tenant->id}");
                } else {
                    Log::info("Admin user {$tenant->admin_email} already exists for tenant: {$tenant->id}");
                }
            }

            // 5. End tenancy to return to central context
            tenancy()->end();

            Log::info("Successfully set up database roles/admin user for tenant: {$tenant->id}");

        } catch (\Exception $e) {
            Log::error("Error setting up tenant database ({$tenant->id}): " . $e->getMessage());
            // Important: always end tenancy even on error
            if (tenancy()->initialized) {
                tenancy()->end();
            }
        }
    }
}
