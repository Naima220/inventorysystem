<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        // Reuse same dashboard data as DashboardController
        $stockCount = Product::sum('stock');
        $availableProducts = Product::where('stock', '>', 0)->count();
        $orderCount = Order::count();
        $pendingOrders = Order::where('order_status', 0)->count();
        $todayActivities = Order::whereDate('created_at', today())->count();

        $months = [];
        $sales = [];
        $orders = [];

        for ($i = 1; $i <= 12; $i++) {
            $months[] = date('M', mktime(0, 0, 0, $i, 1));
            $sales[] = InvoiceItem::whereMonth('created_at', $i)->sum('total_price');
            $orders[] = Order::whereMonth('created_at', $i)->count();
        }

        $lowStockProducts = Product::where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->get();
        $lowStockCount = $lowStockProducts->count();

        $subscriptionMessage = null;
        $subscriptionType = null;

        if (tenant('subscription_ends_at')) {
            $endsAt = Carbon::parse(tenant('subscription_ends_at'));
            $daysLeft = now()->diffInDays($endsAt, false);

            if ($daysLeft < 0) {
                $subscriptionMessage = "❌ Subscription expired! Renew now.";
                $subscriptionType = "danger";
            } elseif ($daysLeft <= 3) {
                $subscriptionMessage = "⏰ {$daysLeft} days left before expiry.";
                $subscriptionType = "warning";
            }
        }

        $recentInvoices = Invoice::latest()->take(5)->get();

        return view('dashboard', compact(
            'stockCount',
            'availableProducts',
            'orderCount',
            'pendingOrders',
            'todayActivities',
            'months',
            'sales',
            'orders',
            'recentInvoices',
            'lowStockProducts',
            'lowStockCount',
            'subscriptionMessage',
            'subscriptionType'
        ));
    }
}
