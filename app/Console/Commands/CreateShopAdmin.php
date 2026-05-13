<?php

namespace App\Console\Commands;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateShopAdmin extends Command
{
    /**
     * php artisan shop:create-admin
     */
    protected $signature = 'shop:create-admin
                            {shop_id? : ID-ga shop-ka (optional, haddaadan bixin liiska ayaa soo baxaya)}
                            {--name= : Magaca user-ka}
                            {--email= : Email-ka user-ka}
                            {--password= : Password-ka user-ka}
                            {--role=Admin : Role-ka (Admin, User, manager...)}';

    protected $description = 'Shop-ka u samee Admin user si fudud';

    public function handle()
    {
        // 1. List all shops
        $shops = Shop::all(['id', 'name']);

        if ($shops->isEmpty()) {
            $this->error('Wax shop ah ma jiro nidaamka!');
            return 1;
        }

        // 2. If shop_id not given, show a list to pick from
        $shopId = $this->argument('shop_id');

        if (!$shopId) {
            $this->info('📋 Shops-ka jira:');
            $this->table(['#', 'Shop ID', 'Magaca'], $shops->map(fn($s, $i) => [$i + 1, $s->id, $s->name])->toArray());
            $shopId = $this->ask('Shop-ka ID-giisa geli (copy ka liiska sare)');
        }

        $shop = Shop::find($shopId);

        if (!$shop) {
            $this->error("Shop ID '$shopId' lama helin!");
            return 1;
        }

        // 3. Collect user info
        $name     = $this->option('name')     ?? $this->ask("👤 Magaca user-ka (e.g. Happy Yusuf)");
        $email    = $this->option('email')    ?? $this->ask("📧 Email-ka user-ka (e.g. happy@gmail.com)");
        $password = $this->option('password') ?? $this->secret("🔑 Password-ka (waa la qarin doonaa)");
        $role     = $this->option('role');

        // 4. Switch to the tenant (shop) database
        tenancy()->initialize($shop);

        $this->info("🔄 Ku gelayaa database-ka: {$shop->name}...");

        // 5. Check if email already exists
        if (User::where('email', $email)->exists()) {
            $this->warn("⚠️  Email '$email' horey buu ugu jiray shop-kan!");
            tenancy()->end();
            return 1;
        }

        // 6. Create the user
        $user = User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
        ]);

        // 7. Create role if it doesn't exist, then assign
        $roleModel = Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        $user->assignRole($roleModel);

        tenancy()->end();

        $this->newLine();
        $this->info("✅ User si guul ah ayaa loo abuuray!");
        $this->table(
            ['Field', 'Value'],
            [
                ['Shop',     $shop->name],
                ['Magaca',   $name],
                ['Email',    $email],
                ['Password', $password],
                ['Role',     $role],
            ]
        );
        $this->newLine();
        $this->comment("💡 Kaydi password-ka: $password");

        return 0;
    }
}
