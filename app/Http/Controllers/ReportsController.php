<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\SupplierPurchase;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Payment;
use App\Models\Employee;
use App\Models\Salary;
use App\Models\Expense;
use App\Models\Debt;
use Carbon\Carbon;
use DB;

class ReportsController extends Controller
{
    // 🔥 COMMON FILTER
    private function applyDateFilter($query, $request)
    {
        if ($request->type == 'daily') {
            $query->whereDate('created_at', now()->toDateString());
        }

        elseif ($request->type == 'weekly') {
            $query->whereBetween('created_at', [
                now()->subDays(7),
                now()
            ]);
        }

        elseif ($request->type == 'monthly') {
            $query->whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth()
            ]);
        }

        // date range (manual)
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59',
            ]);
        }

        return $query;
    }

    // 🟩 GENERAL
    public function generalReport()
    {
        $data = [];

        $data['total_products'] = Product::count();
        $data['available_stock'] = Product::sum('stock');
        $data['out_of_stock'] = Product::where('stock', 0)->count();

        $data['total_orders'] = Order::count();
        $data['pending_orders'] = Order::where('order_status', 0)->count();
        $data['delivered_orders'] = Order::where('order_status', 1)->count();

        $data['total_invoices'] = Invoice::count();
        $data['total_invoice_amount'] = DB::table('invoice_items')->sum('total_price');

        $data['total_customers'] = Customer::count();
        $data['total_suppliers'] = Supplier::count();

        $data['payments_received'] = Payment::sum('paid');
        $data['total_payments'] = Payment::count();

        $data['total_employees'] = Employee::count();
        $data['salary_count'] = Salary::count();

        $data['total_expenses'] = Expense::sum('amount');
        $data['total_debts'] = Debt::count();
      $data['total_debt_amount'] = Debt::sum('amount');
     $data['total_paid_debts'] = DB::table('debt_payments')->sum('paid_amount');
    $data['remaining_debts'] =
    Debt::sum('amount') - DB::table('debt_payments')->sum('paid_amount');

        return view('admin.reports.general', compact('data'));
    }

    // 🟨 ORDERS
    public function ordersReport(Request $request)
    {
        $query = Order::with(['customer', 'orderItems.product']);

        $query = $this->applyDateFilter($query, $request);

        $orders = $query->latest()->get();

        foreach ($orders as $order) {
            $order->calc_total = $order->orderItems->sum(fn($item) => $item->price * $item->qty);
        }

        $totalOrders = $orders->count();
        $pendingOrders = $orders->where('order_status', 0)->count();
        $deliveredOrders = $orders->where('order_status', 1)->count();

        return view('admin.reports.orders', compact(
            'orders',
            'totalOrders',
            'pendingOrders',
            'deliveredOrders'
        ));
    }

    // 🟦 INVOICES
    public function invoicesReport(Request $request)
    {
        $query = Invoice::with(['customer', 'items']);

        $query = $this->applyDateFilter($query, $request);

        $invoices = $query->latest()->get();

        $totalAmount = 0;
        foreach ($invoices as $inv) {
            $totalAmount += $inv->items->sum(fn($item) => $item->price * $item->qty);
        }

        return view('admin.reports.invoices', compact('invoices', 'totalAmount'));
    }

    // 🟩 PAYMENTS
    public function paymentsReport(Request $request)
    {
        $query = Payment::with('customer');

        $query = $this->applyDateFilter($query, $request);

        $payments = $query->latest()->get();

        $totalPaid = $payments->sum('paid');

        return view('admin.reports.payments', compact('payments', 'totalPaid'));
    }

    // 🟪 SALARIES
    public function salariesReport(Request $request)
    {
        $query = Salary::with('employee');

        $query = $this->applyDateFilter($query, $request);

        $salaries = $query->latest()->get();

        $totalPaid = $salaries->sum('amount');

        return view('admin.reports.salaries', compact('salaries', 'totalPaid'));
    }

    // 🟥 EXPENSES
    public function expensesReport(Request $request)
    {
        $query = Expense::query();

        $query = $this->applyDateFilter($query, $request);

        $expenses = $query->latest()->get();

        $totalExpenses = $expenses->sum('amount');

        return view('admin.reports.expenses', compact('expenses', 'totalExpenses'));
    }

    // 🟧 CUSTOMERS
    public function customersReport(Request $request)
    {
        $query = Customer::query();

        $query = $this->applyDateFilter($query, $request);

        $customers = $query->latest()->get();

        return view('admin.reports.customers', compact('customers'));
    }

    // 🟫 SUPPLIERS
    public function suppliersReport(Request $request)
    {
        $query = Supplier::query();

        $query = $this->applyDateFilter($query, $request);

        $suppliers = $query->latest()->get();

        return view('admin.reports.suppliers', compact('suppliers'));
    }

    // ⬛ PRODUCTS
    public function productsReport(Request $request)
    {
        $query = Product::query();

        $query = $this->applyDateFilter($query, $request);

        $products = $query->latest()->get();

        return view('admin.reports.products', compact('products'));
    }

    // 🟨 EMPLOYEES (SPECIAL)
    public function employeesReport(Request $request)
    {
        $query = Employee::query();

        if ($request->type == 'daily') {
            $query->whereDate('hire_date', now()->toDateString());
        }
        elseif ($request->type == 'weekly') {
            $query->whereBetween('hire_date', [now()->subDays(7), now()]);
        }
        elseif ($request->type == 'monthly') {
            $query->whereBetween('hire_date', [
                now()->startOfMonth(),
                now()->endOfMonth()
            ]);
        }

        $employees = $query->latest()->get();

        return view('admin.reports.employees', compact('employees'));
    }
 public function debtReport(Request $request)
{
    $query = Debt::with(['customer', 'payments']);

    $query = $this->applyDateFilter($query, $request);

    $debts = $query->latest()->get();

    foreach ($debts as $debt) {
        $debt->paid = $debt->payments->sum('paid_amount');
        $debt->remaining = $debt->amount - $debt->paid;
    }

    $totalDebt = $debts->sum('remaining');

    return view('admin.reports.debts', compact(
        'debts',
        'totalDebt'
    ));
}
    
}