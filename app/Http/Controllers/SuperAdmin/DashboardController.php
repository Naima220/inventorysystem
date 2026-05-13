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
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $shops = Shop::all();

        // Expired shops
        $expiredShops = Shop::whereNotNull('subscription_ends_at')
                            ->where('subscription_ends_at', '<', now())
                            ->get();

        $stats = [];
        foreach ($shops as $shop) {
            try {
                tenancy()->initialize($shop);
                $stats[$shop->id] = [
                    'total_products'   => Product::count(),
                    'total_orders'     => Order::count(),
                    'total_customers'  => Customer::count(),
                    'total_employees'  => Employee::count(),
                    'total_suppliers'  => Supplier::count(),
                    'total_expenses'   => Expense::count(),
                    'total_salaries'   => Salary::count(),
                    'total_invoices'   => Invoice::count(),
                    'total_payments'   => Payment::count(),
                ];
                tenancy()->end();
            } catch (\Exception $e) {
                $stats[$shop->id] = [
                    'total_products'   => 0,
                    'total_orders'     => 0,
                    'total_customers'  => 0,
                    'total_employees'  => 0,
                    'total_suppliers'  => 0,
                    'total_expenses'   => 0,
                    'total_salaries'   => 0,
                    'total_invoices'   => 0,
                    'total_payments'   => 0,
                    'error'            => 'DB Error'
                ];
            }
        }

        return view('superadmin.dashboard', compact('shops','expiredShops','stats'));
    }

    // 🔵 Renew Shop
    public function renewShop($id)
    {
        $shop = Shop::findOrFail($id);

        $now = Carbon::now();

        $shop->subscription_starts_at = $now;
        $shop->subscription_ends_at = $now->copy()->addMonth();
        $shop->is_active = 1;

        $shop->save();

        return back()->with('success', 'Shop Renewed Successfully ✅');
    }

    // 🔴 Close Shop
    public function closeShop($id)
    {
        $shop = Shop::findOrFail($id);

        $shop->is_active = 0;
        $shop->save();

        return back()->with('success', 'Shop Closed Successfully ❌');
    }
    // Show form
public function createShop()
{
    return view('superadmin.create_shop');
}
// 🔴 Delete Shop
public function deleteShop($id)
{
    $shop = \App\Models\Shop::findOrFail($id);

    $shop->delete();

    return back()->with('success', 'Shop deleted successfully ❌');
}

public function updateShop(Request $request, $id)
{
    $shop = \App\Models\Shop::findOrFail($id);
    $oldEmail = $shop->admin_email;

    // 1. Update central Shop record
    $shop->name = $request->name;
    $shop->owner_name = $request->owner_name;
    $shop->phone = $request->phone;
    $shop->admin_email = $request->admin_email;
    
    if ($request->filled('admin_password')) {
        $shop->admin_password = $request->admin_password;
    }

    // update subscription
    $shop->subscription_ends_at = now()->addDays((int)$request->subscription_days);
    $shop->save();

    // 2. Update User in Tenant DB to stay in sync
    try {
        tenancy()->initialize($shop);
        
        // Try to find by old email first
        $user = User::where('email', $oldEmail)->first();
        
        // If not found by email, find any user with Admin role
        if (!$user) {
            $user = User::role(['Admin', 'admin'])->first();
        }

        // Final fallback: first user
        if (!$user) {
            $user = User::first();
        }

        if ($user) {
            $user->email = $request->admin_email;
            if ($request->filled('admin_password')) {
                $user->password = Hash::make($request->admin_password);
            }
            $user->save();
        }
        
        tenancy()->end();
    } catch (\Exception $e) {
        if (tenancy()->initialized) tenancy()->end();
        Log::error("Error syncing user for shop {$id}: " . $e->getMessage());
    }

    return redirect()->route('superadmin.dashboard')->with('success', 'Shop updated and synced successfully');
}




public function editShop($id)
{
    $shop = \App\Models\Shop::findOrFail($id);

    return view('superadmin.edit_shop', compact('shop'));
}
// Store shop
public function storeShop(Request $request)
{
    $request->validate([
        'id' => 'required|alpha_dash|unique:tenants,id',
        'name' => 'required',
        'owner_name' => 'required',
        'phone' => 'required',
        'admin_email' => 'required|email',
        'admin_password' => 'required|min:6',
        'subscription_days' => 'required|numeric'
    ]);

    $shopId = trim($request->id);

    $shop = Shop::create([
        'id' => $shopId,
        'name' => $request->name,
        'owner_name' => $request->owner_name,
        'phone' => $request->phone,
        'is_active' => 1,
        'subscription_starts_at' => now(),
        'subscription_ends_at' => now()->addDays((int)$request->subscription_days),
        'admin_email' => $request->admin_email,
        'admin_password' => $request->admin_password, // Saved in central DB for Super Admin
    ]);

    // Create Domain (e.g. shop1.mine-market.test)
    $centralDomains = config('tenancy.central_domains');
    $centralDomain = end($centralDomains); // mine-market.test
    $shop->domains()->create([
        'domain' => strtolower($shopId) . '.' . $centralDomain
    ]);

    return redirect()->back()->with('success', "Shop '$shopId' created successfully! Database, Roles, and Admin User are being set up automatically in the background.");
}

    public function impersonate($id)
    {
        $shop = Shop::findOrFail($id);

        // 1. Initialize tenancy
        tenancy()->initialize($shop);

        // ✅ IMPORTANT: Clear Spatie Permission cache for this tenant
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Find the admin user in the tenant database
        // Try exact email match first
        $adminUser = User::where('email', $shop->admin_email)->first();

        // Fallback 1: Try any user with 'Admin' or 'admin' role
        if (!$adminUser) {
            try {
                $adminUser = User::role(['Admin', 'admin'])->first();
            } catch (\Exception $e) {
                // If role doesn't exist, ignore and move to next fallback
                Log::warning("Impersonation: Role 'Admin' not found in tenant {$shop->id}");
            }
        }

        // Fallback 2: Just take the first user in the database (last resort)
        if (!$adminUser) {
            $adminUser = User::first();
        }

        if (!$adminUser) {
            tenancy()->end();
            return back()->with('error', 'No users found in this shop database to log in as.');
        }

        // 3. Build the tenant base URL using the shop's domain
        $shopDomain = $shop->domains()->first();
        $domain = $shopDomain ? $shopDomain->domain : request()->getHost();
        
        // Handle port (e.g. localhost:8000)
        $port = request()->getPort();
        if ($port && !in_array($port, [80, 443])) {
            $domain .= ':' . $port;
        }
        
        // 4. Create impersonation token
        $token = tenancy()->impersonate($shop, $adminUser->id, '/dashboard');

        // 5. End tenancy
        tenancy()->end();

        // 6. Redirect to the impersonation URL
        return redirect('http://' . $domain . '/tenancy/impersonate/' . $token->token);
    }
}