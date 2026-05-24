<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboard;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ShopController;

/*
|--------------------------------------------------------------------------
| Web Routes (Central Domain Only)
|--------------------------------------------------------------------------
|
| These routes are for the CENTRAL domain (mine-market.test) only.
| Tenant routes (shop1.mine-market.test, etc.) are in routes/tenant.php
|
*/

// ✅ Home → Redirect to login
Route::get('/', function () {
    return redirect('/login');
});

// ✅ Subscription expired page
Route::get('/subscription-expired', function () {
    return view('subscription.expired');
})->name('central.subscription.expired');

// ✅ Super Admin Dashboard
Route::middleware(['auth', 'role:super_admin'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
        Route::get('/dashboard', [SuperAdminDashboard::class, 'index'])->name('dashboard');
        Route::get('/create-shop', [SuperAdminDashboard::class, 'createShop'])->name('createShop');
        Route::post('/store-shop', [SuperAdminDashboard::class, 'storeShop'])->name('storeShop');
        Route::get('/shop/{id}/edit', [SuperAdminDashboard::class, 'editShop'])->name('editShop');
        Route::put('/shop/{id}/update', [SuperAdminDashboard::class, 'updateShop'])->name('updateShop');
        Route::post('/delete-shop/{id}', [SuperAdminDashboard::class, 'deleteShop'])->name('deleteShop');
        Route::post('/renew-shop/{id}', [SuperAdminDashboard::class, 'renewShop'])->name('renewShop');
        Route::post('/close-shop/{id}', [SuperAdminDashboard::class, 'closeShop'])->name('closeShop');

        // Backup Management
        Route::get('/backups', [\App\Http\Controllers\SuperAdmin\BackupController::class, 'index'])->name('backups.index');
        Route::get('/backups/download/{id}', [\App\Http\Controllers\SuperAdmin\BackupController::class, 'download'])->name('backups.download');
        Route::post('/backups/restore/{id}', [\App\Http\Controllers\SuperAdmin\BackupController::class, 'restore'])->name('backups.restore');
        Route::post('/backups/global', [\App\Http\Controllers\SuperAdmin\BackupController::class, 'runGlobalBackup'])->name('backups.global');

        Route::get('/shop/{id}/impersonate', [SuperAdminDashboard::class, 'impersonate'])->name('impersonate');

        // Profile & Password Management
        Route::get('/profile', [\App\Http\Controllers\SuperAdmin\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [\App\Http\Controllers\SuperAdmin\ProfileController::class, 'update'])->name('profile.update');
        Route::get('/change-password', [\App\Http\Controllers\SuperAdmin\ProfileController::class, 'showChangePassword'])->name('profile.password');
        Route::post('/change-password', [\App\Http\Controllers\SuperAdmin\ProfileController::class, 'changePassword'])->name('profile.password.update');

        // Shop Users Management
        Route::get('/shop-users', [\App\Http\Controllers\SuperAdmin\UserController::class, 'index'])->name('users.index');
        Route::post('/shop-users/change-password', [\App\Http\Controllers\SuperAdmin\UserController::class, 'changePassword'])->name('users.changePassword');
        Route::post('/shop-users/delete', [\App\Http\Controllers\SuperAdmin\UserController::class, 'deleteUser'])->name('users.delete');
    });

// ✅ Shop resource (Super Admin)
Route::middleware(['auth', 'super_admin'])->group(function () {
    Route::resource('shops', ShopController::class);
});

// ✅ Activity Logs (Central)
Route::get('/activity-logs', [ActivityLogController::class, 'index'])
    ->middleware(['auth'])
    ->name('activity.logs');

// ✅ Chatbot
Route::post('/chatbot', [ChatbotController::class, 'reply']);

// ✅ Auth routes (login, register, logout, etc.)
require __DIR__.'/auth.php';

// Temporary Deployment Helper Route (Automates .env edit and cache clear)
Route::get('/deploy-helper', function (\Illuminate\Http\Request $request) {
    if ($request->query('secret') !== 'deploy143') {
        abort(403);
    }

    $envPath = base_path('.env');
    if (!file_exists($envPath)) {
        return 'No .env file found';
    }

    $envContent = file_get_contents($envPath);
    
    // Comment out SESSION_DOMAIN
    if (str_contains($envContent, "SESSION_DOMAIN=.minemarket.tech") && !str_contains($envContent, "# SESSION_DOMAIN=.minemarket.tech")) {
        $envContent = str_replace("SESSION_DOMAIN=.minemarket.tech", "# SESSION_DOMAIN=.minemarket.tech", $envContent);
        file_put_contents($envPath, $envContent);
        $message = "SESSION_DOMAIN has been successfully commented out in .env!\n";
    } else {
        $message = "SESSION_DOMAIN was already commented out or not active.\n";
    }

    // Clear caches
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    
    $message .= "Configuration and Cache cleared successfully!";
    
    return response($message)->header('Content-Type', 'text/plain');
});

