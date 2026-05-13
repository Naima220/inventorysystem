<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    // Show form for new order (multiple items)
    public function newformData()
    {
        $products = Product::where('stock', '>', 0)->get();
        $customers = Customer::all();
        return view('admin.new_order', compact('products', 'customers'));
    }

    // Store new order with multiple items (STOCK LAMA JARAYO)
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.sale_price' => 'required|numeric|min:0',
            'items.*.product_sn' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $customer = Customer::findOrFail($request->customer_id);

            $order = Order::create([
                'customer_id' => $customer->id,
                'customer_name' => $customer->customer_name,
                'customer_phone' => $customer->phone,
                'order_status' => 0,
            ]);

            foreach ($request->items as $item) {

                $product = Product::findOrFail($item['product_id']);

                // Hubi stock ku filan (LAKIIN HA JARIN)
                if ($product->stock < $item['quantity']) {
                    DB::rollBack();
                    return redirect()->back()->with(
                        'error',
                        'Insufficient stock for product: ' . $product->name
                    );
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_sn' => $item['product_sn'] ?? $product->product_sn,
                    'quantity' => $item['quantity'],
                    'sale_price' => $item['sale_price'],
                    'total_price' => $item['quantity'] * $item['sale_price'],
                ]);

                // ❌ STOCK HALKAN LAGAMA JARAYO
            }

            DB::commit();

            return redirect()->route('all.orders')
                ->with('success', 'Order successfully added.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order Store Error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while adding the order.');
        }
    }

    // Show all orders
    public function allOrders()
    {
        $orders = Order::with('customer', 'orderItems.product')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.all_orders', compact('orders'));
    }

    // Pending orders
    public function pendingOrders()
    {
        $orders = Order::with(['customer', 'orderItems.product'])
            ->where('order_status', 0)
            ->get();

        return view('admin.pending_orders', compact('orders'));
    }

    // Delivered orders
    public function deliveredOrders()
    {
        $orders = Order::with(['customer', 'orderItems.product'])
            ->where('order_status', 1)
            ->latest()
            ->get();

        return view('admin.delivered_orders', compact('orders'));
    }

    // Update order status
    public function updateOrderStatus(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->order_status = $request->order_status;
            $order->save();

            return redirect()->back()
                ->with('success', 'Order status updated.');
        } catch (\Exception $e) {
            Log::error('Update Order Status Error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error updating order status.');
        }
    }

    // Get product serial numbers
    public function getProductSerial(Request $request)
    {
        $serials = Product::where('name', $request->name)
            ->where('stock', '>', 0)
            ->pluck('product_sn');

        return response()->json(['product_serials' => $serials]);
    }

    // Get customer
    public function getCustomer(Request $request)
    {
        $customer = Customer::find($request->id);

        if ($customer) {
            return response()->json(['success' => true, 'customer' => $customer]);
        }

        return response()->json(['success' => false]);
    }

    // Create invoice from order
    public function createInvoiceFromOrder($order_id)
    {
        $order = Order::with('orderItems.product', 'customer')
            ->findOrFail($order_id);

        $products = Product::all();
        $customers = Customer::all();

        return view('admin.new_invoice',
            compact('order', 'products', 'customers'));
    }

    public function ordersData()
    {
        $orders = Order::latest()->get();
        return view('admin.all_orders', compact('orders'));
    }

    public function edit($id)
    {
        $order = Order::with('orderItems')->findOrFail($id);
        $products = Product::all();

        return view('admin.edit_order', compact('order', 'products'));
    }

    public function update(Request $request, $id)
{
    $order = Order::findOrFail($id);
    $order->customer_name = $request->customer_name;
    $order->customer_phone = $request->customer_phone;
    $order->order_status = $request->order_status;
    $order->save();

    $existingItemIds = $order->orderItems->pluck('id')->toArray();
    $submittedItemIds = collect($request->items)
        ->pluck('id')
        ->filter()
        ->toArray();

    $toDelete = array_diff($existingItemIds, $submittedItemIds);
    \App\Models\OrderItem::destroy($toDelete);

    foreach ($request->items as $itemData) {

        if (isset($itemData['id'])) {
            $item = \App\Models\OrderItem::findOrFail($itemData['id']);
        } else {
            $item = new \App\Models\OrderItem();
            $item->order_id = $order->id;
        }

        $item->product_id = $itemData['product_id'];
        $item->quantity = $itemData['quantity'];
        $item->sale_price = $itemData['sale_price'] ?? 0;
        $item->total_price = $item->quantity * $item->sale_price;
        $item->save();
    }

    // ✅ ACTIVITY LOG
    ActivityLog::create([
        'user_id' => auth()->id(),
        'action' => 'Order Updated',
        'description' => 'Order #' . $order->id . ' was updated'
    ]);

    return redirect()->route('all.orders')
        ->with('success', 'Order updated successfully');
}
    public function getProductPrice($id)
    {
        $product = Product::findOrFail($id);
        return response()->json(['sale_price' => $product->sale_price]);
    }

    public function destroy($id)
{
    $order = Order::findOrFail($id);

    foreach ($order->orderItems as $item) {
        $item->delete();
    }

    $order->delete();

    // ✅ ACTIVITY LOG
    ActivityLog::create([
        'user_id' => auth()->id(),
        'action' => 'Order Deleted',
        'description' => 'Order #' . $order->id . ' was deleted'
    ]);

    return redirect()->route('all.orders')
        ->with('success', 'Order deleted successfully.');
}
}