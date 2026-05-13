<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Stancl\Tenancy\Events\DatabaseCreated;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SetupTenantDatabase
{
    /**
     * Handle the event.
     *
     * @param DatabaseCreated $event
     * @return void
     */
    public function handle(DatabaseCreated $event)
    {
        $tenant = $event->tenant;

        try {
            // 1. Run migrations for the new tenant
            // We use --force to ensure it runs in all environments
            Artisan::call('tenants:migrate', [
                '--tenants' => $tenant->id,
                '--force' => true,
            ]);

            // 2. Initialize tenancy to perform database operations
            tenancy()->initialize($tenant);

            // 3. Clear Spatie Permission Cache
            // This is CRITICAL for multi-tenancy to avoid "Role not found" errors
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            // 4. Create Basic Roles (Admin and User)
            // We specify the guard explicitly to avoid ambiguity
            $adminRole = Role::firstOrCreate(
                ['name' => 'Admin', 'guard_name' => 'web']
            );
            
            Role::firstOrCreate(
                ['name' => 'User', 'guard_name' => 'web']
            );

            // 5. Create the Admin User
            // Using data stored in the tenant/shop model
            $user = User::create([
                'name'     => $tenant->owner_name,
                'email'    => $tenant->admin_email,
                'password' => Hash::make($tenant->admin_password),
            ]);

            // 6. Assign the Admin Role
            $user->assignRole($adminRole);

            // 7. End tenancy to return to central context
            tenancy()->end();

            Log::info("Successfully set up database and admin for tenant: {$tenant->id}");

        } catch (\Exception $e) {
            Log::error("Error setting up tenant database ({$tenant->id}): " . $e->getMessage());
            // Important: always end tenancy even on error
            if (tenancy()->initialized) {
                tenancy()->end();
            }
        }
    }
}
