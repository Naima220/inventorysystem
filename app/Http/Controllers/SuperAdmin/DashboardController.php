<?php

namespace App\Http\Controllers\SuperAdmin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Employee;
use App\Models\Supplier;
use App\Models\Salary;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Spatie\Permission\PermissionRegistrar;

class DashboardController extends Controller
{
    // =========================================================
    // HELPER: Seed admin user directly into tenant SQLite via PDO
    // (bypasses all tenancy bootstrapper issues — fast & reliable)
    // =========================================================
    private function seedTenantUser(string $shopId, string $name, string $email, string $password): bool
    {
        $prefix     = config('tenancy.database.prefix', 'tenant');
        $sqlitePath = database_path($prefix . $shopId);

        if (!file_exists($sqlitePath)) {
            Log::error("seedTenantUser: SQLite file not found at {$sqlitePath}");
            return false;
        }

        try {
            $pdo = new \PDO('sqlite:' . $sqlitePath);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            // Ensure roles exist
            $pdo->exec("INSERT OR IGNORE INTO roles (name, guard_name, created_at, updated_at)
                        VALUES ('Admin', 'web', datetime('now'), datetime('now'))");
            $pdo->exec("INSERT OR IGNORE INTO roles (name, guard_name, created_at, updated_at)
                        VALUES ('User', 'web', datetime('now'), datetime('now'))");

            // Insert admin user (skip if email already exists)
            $hashedPassword = Hash::make($password);
            $stmt = $pdo->prepare("
                INSERT OR IGNORE INTO users (name, email, password, created_at, updated_at)
                VALUES (:name, :email, :password, datetime('now'), datetime('now'))
            ");
            $stmt->execute([':name' => $name, ':email' => $email, ':password' => $hashedPassword]);

            // Get IDs
            $userId = $pdo->query("SELECT id FROM users WHERE email = " . $pdo->quote($email))->fetchColumn();
            $roleId = $pdo->query("SELECT id FROM roles WHERE name = 'Admin'")->fetchColumn();

            // Assign Admin role
            if ($userId && $roleId) {
                $pdo->exec("
                    INSERT OR IGNORE INTO model_has_roles (role_id, model_type, model_id)
                    VALUES ({$roleId}, 'App\\\\Models\\\\User', {$userId})
                ");
            }

            Log::info("seedTenantUser: User '{$email}' seeded into tenant '{$shopId}'");
            return true;

        } catch (\Exception $e) {
            Log::error("seedTenantUser: Failed for '{$shopId}': " . $e->getMessage());
            return false;
        }
    }

    // =========================================================
    // Super Admin Dashboard
    // =========================================================
    public function index()
    {
        $shops = Shop::all();

        $expiredShops = Shop::whereNotNull('subscription_ends_at')
                            ->where('subscription_ends_at', '<', now())
                            ->get();

        $stats = [];
        foreach ($shops as $shop) {
            try {
                tenancy()->initialize($shop);
                $stats[$shop->id] = [
                    'total_products'  => Product::count(),
                    'total_orders'    => Order::count(),
                    'total_customers' => Customer::count(),
                    'total_employees' => Employee::count(),
                    'total_suppliers' => Supplier::count(),
                    'total_expenses'  => Expense::count(),
                    'total_salaries'  => Salary::count(),
                    'total_invoices'  => Invoice::count(),
                    'total_payments'  => Payment::count(),
                ];
                tenancy()->end();
            } catch (\Exception $e) {
                $stats[$shop->id] = [
                    'total_products'  => 0, 'total_orders'    => 0,
                    'total_customers' => 0, 'total_employees' => 0,
                    'total_suppliers' => 0, 'total_expenses'  => 0,
                    'total_salaries'  => 0, 'total_invoices'  => 0,
                    'total_payments'  => 0, 'error' => 'DB Error: ' . $e->getMessage(),
                ];
                if (tenancy()->initialized) tenancy()->end();
            }
        }

        return view('superadmin.dashboard', compact('shops', 'expiredShops', 'stats'));
    }

    // =========================================================
    // Create Shop — Show Form
    // =========================================================
    public function createShop()
    {
        return view('superadmin.create_shop');
    }

    // =========================================================
    // Store Shop (full flow: create tenant + SQLite + user)
    // =========================================================
    public function storeShop(Request $request)
    {
        $request->validate([
            'id'                => 'required|alpha_dash|unique:tenants,id',
            'name'              => 'required',
            'owner_name'        => 'required',
            'phone'             => 'required',
            'admin_email'       => 'required|email',
            'admin_password'    => 'required|min:6',
            'subscription_days' => 'required|numeric',
        ]);

        $shopId = trim($request->id);

        // STEP 1: Create Shop record in MySQL central DB
        // stancl/tenancy fires TenantCreated → CreateDatabase → MigrateDatabase (synchronous)
        // After this line returns: SQLite file exists + all tenant migrations ran
        $shop = Shop::create([
            'id'                     => $shopId,
            'name'                   => $request->name,
            'owner_name'             => $request->owner_name,
            'phone'                  => $request->phone,
            'is_active'              => 1,
            'subscription_starts_at' => now(),
            'subscription_ends_at'   => now()->addDays((int) $request->subscription_days),
            'admin_email'            => $request->admin_email,
            'admin_password'         => $request->admin_password,
        ]);

        // STEP 2: Register subdomain
        $centralDomain = parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost';
        $centralDomain = explode(':', $centralDomain)[0];
        $shop->domains()->create([
            'domain' => strtolower($shopId) . '.' . $centralDomain
        ]);

        // STEP 3: Insert admin user directly into tenant SQLite database via PDO
        // Direct PDO — no tenancy()->initialize() — avoids all bootstrapper issues
        $this->seedTenantUser(
            $shopId,
            $request->owner_name,
            $request->admin_email,
            $request->admin_password
        );

        return redirect()->back()->with(
            'success',
            "✅ Shop '{$shopId}' created! Login: {$request->admin_email} / {$request->admin_password}"
        );
    }

    // =========================================================
    // Edit Shop — Show Form
    // =========================================================
    public function editShop($id)
    {
        $shop = Shop::findOrFail($id);
        return view('superadmin.edit_shop', compact('shop'));
    }

    // =========================================================
    // Update Shop
    // =========================================================
    public function updateShop(Request $request, $id)
    {
        $shop     = Shop::findOrFail($id);
        $oldEmail = $shop->admin_email;

        $shop->name         = $request->name;
        $shop->owner_name   = $request->owner_name;
        $shop->phone        = $request->phone;
        $shop->admin_email  = $request->admin_email;

        if ($request->filled('admin_password')) {
            $shop->admin_password = $request->admin_password;
        }

        $shop->subscription_ends_at = now()->addDays((int) $request->subscription_days);
        $shop->save();

        // Sync user credentials in tenant SQLite DB directly via PDO
        $prefix     = config('tenancy.database.prefix', 'tenant');
        $sqlitePath = database_path($prefix . $id);

        if (file_exists($sqlitePath)) {
            try {
                $pdo = new \PDO('sqlite:' . $sqlitePath);
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                if ($request->filled('admin_password')) {
                    $hash = Hash::make($request->admin_password);
                    $stmt = $pdo->prepare("UPDATE users SET email = :email, password = :password WHERE email = :old");
                    $stmt->execute([
                        ':email'    => $request->admin_email,
                        ':password' => $hash,
                        ':old'      => $oldEmail,
                    ]);
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET email = :email WHERE email = :old");
                    $stmt->execute([':email' => $request->admin_email, ':old' => $oldEmail]);
                }

            } catch (\Exception $e) {
                Log::error("updateShop: Error syncing user for shop {$id}: " . $e->getMessage());
            }
        }

        return redirect()->route('superadmin.dashboard')->with('success', '✅ Shop updated successfully');
    }

    // =========================================================
    // Delete Shop
    // =========================================================
    public function deleteShop($id)
    {
        $shop = Shop::findOrFail($id);
        $shop->delete();
        return back()->with('success', '✅ Shop deleted successfully');
    }

    // =========================================================
    // Renew Shop
    // =========================================================
    public function renewShop($id)
    {
        $shop = Shop::findOrFail($id);
        $shop->subscription_starts_at = Carbon::now();
        $shop->subscription_ends_at   = Carbon::now()->addMonth();
        $shop->is_active = 1;
        $shop->save();
        return back()->with('success', '✅ Shop Renewed Successfully');
    }

    // =========================================================
    // Close Shop
    // =========================================================
    public function closeShop($id)
    {
        $shop = Shop::findOrFail($id);
        $shop->is_active = 0;
        $shop->save();
        return back()->with('success', '✅ Shop Closed Successfully');
    }

    // =========================================================
    // Impersonate (Super Admin → Login to Shop)
    // =========================================================
    public function impersonate($id)
    {
        $shop = Shop::findOrFail($id);

        // Initialize tenancy (only DatabaseTenancyBootstrapper now — no filesystem issues)
        tenancy()->initialize($shop);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Find admin user in tenant DB
        $adminUser = User::where('email', $shop->admin_email)->first()
                  ?? User::first();

        if (!$adminUser) {
            tenancy()->end();
            return back()->with('error', '❌ No users found in this shop. Please repair the shop database.');
        }

        // Build tenant URL
        $shopDomain = $shop->domains()->first();
        $domain     = $shopDomain ? $shopDomain->domain : request()->getHost();
        $port       = request()->getPort();
        if ($port && !in_array($port, [80, 443])) {
            $domain .= ':' . $port;
        }

        // Create impersonation token
        $token = tenancy()->impersonate($shop, $adminUser->id, '/dashboard');
        tenancy()->end();

        $scheme = (request()->secure() || str_starts_with(config('app.url'), 'https://')) ? 'https://' : 'http://';
         return redirect($scheme . $domain . '/tenancy/impersonate/' . $token->token);
    }
}