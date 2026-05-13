<?php
// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1️⃣ Abuur roles
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $adminRole      = Role::firstOrCreate(['name' => 'Admin']);
        $userRole       = Role::firstOrCreate(['name' => 'User']);

        // 2️⃣ Abuur Super Admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'), // change password later
            ]
        );
        $superAdmin->syncRoles([$superAdminRole]);

        // 3️⃣ Abuur Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );
        $admin->syncRoles([$adminRole]);

        // 4️⃣ Abuur sample User
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Sample User',
                'password' => bcrypt('password'),
            ]
        );
        $user->syncRoles([$userRole]);
    }
}
