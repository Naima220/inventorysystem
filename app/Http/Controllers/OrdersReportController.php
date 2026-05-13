<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;

class OrdersReportController extends Controller
{
    public function index(Request $request)
{
    $from = $request->from_date;
    $to   = $request->to_date;

    // 🔥 Query
    $query = Order::with('orderItems', 'customer');

    // ✅ Daily / Weekly / Monthly
    $query = $this->applyDateFilter($query, $request);

    // ✅ Date range (manual filter)
    if ($from && $to) {
        $query->whereDate('created_at', '>=', $from)
              ->whereDate('created_at', '<=', $to);
    }

    $orders = $query->latest()->get();

    // 🔢 Total calculation
    $grandTotal = 0;
    foreach ($orders as $order) {
        $orderTotal = $order->orderItems->sum('total_price');
        $order->calculated_total = $orderTotal;
        $grandTotal += $orderTotal;
    }

    return view('admin.reports.orders', compact('orders', 'grandTotal', 'from', 'to'));
}


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

    return $query;
}
}