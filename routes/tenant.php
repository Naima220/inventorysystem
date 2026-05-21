<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeControllers;
use App\Http\Controllers\OrdersReportController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\SupplierPurchaseController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    'check.subscription',
])->group(function () {

    // Home
    Route::get('/', function () {
        return redirect()->route('login');
    });

    // Auth
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');
    Route::get('/user/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('user.dashboard');
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->middleware(['auth', 'role:super_admin|admin|Admin'])->name('admin.dashboard');

    // Products
    Route::middleware(['auth'])->group(function () {
        Route::get('/products', [ProductController::class, 'allProduct'])->name('all.product');
        Route::get('/products/available', [ProductController::class, 'availableProducts'])->name('products.available');
        Route::get('/products/multiple/create', [ProductController::class, 'createMultiple'])->name('products.multiple.create');
        Route::post('/products/multiple/store', [ProductController::class, 'storeMultiple'])->name('products.multiple.store');
        Route::get('/edit-product/{id}', [ProductController::class, 'editProduct'])->name('product.edit');
        Route::get('/delete-product/{id}', [ProductController::class, 'destroy'])->name('product.delete');
        Route::post('/update-product/{id}', [ProductController::class, 'updateProduct'])->name('update.product');
        Route::post('/insert-purchase-products', [ProductController::class, 'storePurchase'])->name('purchase.store');
        Route::get('/product/purchase/{id}', [ProductController::class, 'purchaseData'])->name('product.purchase');
        Route::post('/api/get-product-by-id', [ProductController::class, 'getProductById']);
    });

    // Invoices
    Route::middleware(['auth'])->group(function () {
        Route::get('/new-invoice', [InvoiceController::class, 'create'])->name('invoice.create');
        Route::post('/store-invoice', [InvoiceController::class, 'store'])->name('invoice.store');
        Route::get('/all-invoices', [InvoiceController::class, 'index'])->name('invoice.index');
        Route::get('/invoice/show/{id}', [InvoiceController::class, 'show'])->name('invoice.show');
        Route::get('/edit-invoice/{id}', [InvoiceController::class, 'edit'])->name('invoice.edit');
        Route::put('/update-invoice/{id}', [InvoiceController::class, 'update'])->name('invoice.update');
        Route::delete('/invoice/delete/{id}', [InvoiceController::class, 'destroy'])->name('invoice.destroy');
        Route::get('/invoices/{id}/download-pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.downloadPdf');
        Route::get('/get-product-info', [InvoiceController::class, 'getProductInfo'])->name('get.product.info');

        // ✅ Add Invoice from Order (route alias used in all_orders.blade.php)
        Route::get('/add-invoice/{id}', [InvoiceController::class, 'addInvoice'])->name('add.invoice');

        // ✅ Route aliases for invoices.* (used in all_invoices.blade.php)
        Route::get('/invoices/{id}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::delete('/invoices/{id}/delete', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
        Route::put('/invoices/{id}/update', [InvoiceController::class, 'update'])->name('invoices.update');
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/invoices/store', [InvoiceController::class, 'store'])->name('invoices.store');
    });

    // Orders
    Route::middleware(['auth'])->group(function () {
        Route::get('/new-order', [OrderController::class, 'newformData'])->name('new.order');
        Route::get('/new-order-form', [OrderController::class, 'newformData'])->name('new.order.form'); // Alias for compatibility
        Route::post('/insert-order', [OrderController::class, 'store'])->name('insert.order');
        Route::get('/all-orders', [OrderController::class, 'ordersData'])->name('all.orders');
        Route::get('/pending-orders', [OrderController::class, 'pendingOrders'])->name('pending.orders');
        Route::get('/delivered-orders', [OrderController::class, 'deliveredOrders'])->name('delivered.orders');
        Route::post('/update-order-status/{id}', [OrderController::class, 'updateOrderStatus'])->name('update.order.status');
        Route::get('orders/{id}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::delete('orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::put('/orders/{id}', [OrderController::class, 'update'])->name('orders.update');
        Route::post('/api/get-product-serial', [OrderController::class, 'getProductSerial']);
        Route::post('/api/get-customer', [OrderController::class, 'getCustomer']);
    });

    // Customers
    Route::middleware(['auth'])->group(function () {
        Route::get('/all-customers', [CustomerController::class, 'index'])->name('all.customers');
        Route::get('/add-customer', [CustomerController::class, 'create'])->name('add.customer');
        Route::post('/insert-customer', [CustomerController::class, 'store'])->name('store.customer');
        Route::get('/edit-customer/{customer}', [CustomerController::class, 'edit'])->name('edit.customer');
        Route::put('/update-customer/{customer}', [CustomerController::class, 'update'])->name('customers.update');
        Route::delete('/delete-customer/{id}', [CustomerController::class, 'destroy'])->name('delete.customer');
        Route::get('/customer-debt/{id}', [DebtController::class, 'getCustomerDebt']);
    });

    // ADMIN ONLY MODULES
    Route::middleware(['auth', 'role:super_admin|admin|Admin'])->group(function () {
        // Suppliers
        Route::get('/add-supplier', [SupplierController::class, 'create'])->name('add.supplier');
        Route::post('/all-suppliers', [SupplierController::class, 'store'])->name('store.supplier');
        Route::get('/all-suppliers', [SupplierController::class, 'index'])->name('all.suppliers');
        Route::get('/suppliers/{id}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::put('/suppliers/{id}/update', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

        // Payments
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/create/{invoice}', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments/store', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
        Route::put('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
        Route::get('/payments/{id}/pdf', [PaymentController::class, 'downloadPdf'])->name('payments.downloadPdf');
        Route::get('/payments/{id}/print', [PaymentController::class, 'print'])->name('payments.print');

        // Employees & Salaries
        Route::get('/add-employee', fn() => view('admin.employee.add_employee'))->name('add.employee');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/all-employees', [EmployeeController::class, 'index'])->name('all.employees');
        Route::get('/employees/{id}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
        Route::get('/add-salary', [SalaryController::class, 'create'])->name('add.salary');
        Route::post('/salaries', [SalaryController::class, 'store'])->name('salaries.store');
        Route::get('/all-salaries', [SalaryController::class, 'index'])->name('all.salaries');
        Route::get('/edit-salary/{salary}', [SalaryController::class, 'edit'])->name('salaries.edit');
        Route::post('/update-salary/{salary}', [SalaryController::class, 'update'])->name('salaries.update');
        Route::delete('/delete-salary/{salary}', [SalaryController::class, 'destroy'])->name('salaries.destroy');
        Route::post('/api/get-employee-salary', [SalaryController::class, 'getEmployeeSalary']);

        // Expenses
        Route::resource('expenses', ExpenseController::class);

        // Debts
        Route::get('debts', [DebtController::class, 'index'])->name('debt.index');
        Route::get('/debts/create', [DebtController::class, 'create'])->name('debt.create');
        Route::post('/debts/store', [DebtController::class, 'store'])->name('debt.store');
        Route::get('/debts/pay/{id}', [DebtController::class, 'payForm'])->name('debt.pay.form');
        Route::post('/debts/pay/{id}', [DebtController::class, 'payStore'])->name('debt.pay.store');
        Route::delete('/debts/delete/{id}', [DebtController::class, 'destroy'])->name('debt.delete');

        // Supplier Purchases
        Route::resource('supplier-purchases', SupplierPurchaseController::class);
        Route::get('supplier-purchases/create-new', [SupplierPurchaseController::class, 'createNew'])->name('supplier-purchases.create-new');
        Route::post('supplier-purchases/storeNew', [SupplierPurchaseController::class, 'storeNew'])->name('supplier-purchases.storeNew');

        // Reports
        Route::get('/reports/invoices', [\App\Http\Controllers\InvoicesReportController::class, 'index'])->name('reports.invoices');
        Route::get('reports/payments', [ReportsController::class, 'paymentsReport'])->name('reports.payments');
        Route::get('/reports/general', [ReportsController::class, 'generalReport'])->name('reports.general');
        Route::get('reports/salaries', [ReportsController::class, 'salariesReport'])->name('reports.salaries');
        Route::get('reports/expenses', [ReportsController::class, 'expensesReport'])->name('reports.expenses');
        Route::get('reports/customers', [ReportsController::class, 'customersReport'])->name('reports.customers');
        Route::get('reports/employees', [ReportsController::class, 'employeesReport'])->name('reports.employees');
        Route::get('reports/products', [ReportsController::class, 'productsReport'])->name('reports.products');
        Route::get('reports/suppliers', [ReportsController::class, 'suppliersReport'])->name('reports.suppliers');
        Route::get('/reports/debts', [ReportsController::class, 'debtReport'])->name('reports.debts');
        Route::get('/reports/orders', [OrdersReportController::class, 'index'])->name('reports.orders');
        Route::get('/report', [IncomeControllers::class, 'incomeOutcome'])->name('report.incomeOutcome');
    });

    // Users (Admin only)
    Route::middleware(['auth', 'role:super_admin|admin|Admin'])->prefix('admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/users/store', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    });

    // Activity Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->middleware(['auth'])->name('activity.logs');
});

// Subscription expired (Outside main middleware to avoid loop)
Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/subscription-expired', fn() => view('subscription.expired'))->name('subscription.expired');
    
    // Manual Impersonation Route to fix 404
    Route::get('/tenancy/impersonate/{token}', function ($token) {
        return \Stancl\Tenancy\Features\UserImpersonation::makeResponse($token);
    });
});
