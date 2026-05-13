<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\ActivityLog;

use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function create()
    {
        $customers = Customer::all();
        $products = Product::where('stock', '>', 0)->get();
        return view('admin.invoices.new_invoice', compact('customers', 'products'));
    }

    public function store(Request $request)
{
    // Tusaale: InvoiceController@store
$request->validate([
    'customer_id' => 'required|exists:customers,id',
    'products' => 'required|array|min:1',
    'products.*.product_id' => 'required|exists:products,id',
    'products.*.qty' => 'required|integer|min:1',
    'products.*.sales_unit_price' => 'required|numeric|min:0',
    'total_amount' => 'required|numeric|min:0',
    'debt' => 'required|numeric|min:0',
]);


    DB::beginTransaction();
    try {
        $customer = Customer::findOrFail($request->customer_id);

      $grandTotal = 0;

$invoice = Invoice::create([
    'customer_id' => $customer->id,
    'customer_name' => $customer->customer_name,
    'customer_phone' => $customer->phone,
    'debt' => $request->debt,
    'total_amount' => 0, // temporary
]);

        if ($request->order_id) {
    $order = Order::find($request->order_id);
    if ($order) {
        $order->order_status = 1; // Mark as Delivered
        $order->save();
    }
}

        $grandTotal = 0;

foreach ($request->products as $item) {

    $product = Product::findOrFail($item['product_id']);

    if ($product->stock < $item['qty']) {
        DB::rollBack();
        return back()->with('error', 'Insufficient stock');
    }

    $total = $item['qty'] * $item['sales_unit_price'];
    $grandTotal += $total;

    InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'product_id' => $product->id,
        'quantity' => $item['qty'],
        'sale_price' => $item['sales_unit_price'],
        'total_price' => $total,
    ]);

    $product->decrement('stock', $item['qty']);
}

$invoice->update([
    'total_amount' => $grandTotal
]);

        DB::commit();
        return redirect()->route('invoice.index')->with('success', 'Invoice created successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}


    public function index()
    {
       $invoices = Invoice::with(['customer', 'invoiceItems.product'])->latest()->get();

        return view('admin.invoices.all_invoices', compact('invoices'));
        
    }

    public function show($id)
    {
       $invoice = Invoice::with(['invoiceItems.product', 'customer'])->findOrFail($id);

        return view('admin.invoices.show', compact('invoice'));
    }
public function edit($id)
{
    $invoice = Invoice::with('invoiceItems.product')->findOrFail($id);
    $customers = Customer::all();
    $products = Product::all();

    return view('admin.invoices.edit_invoice', compact('invoice', 'customers', 'products'));
}



   public function destroy($id)
{
    $invoice = Invoice::findOrFail($id);
    $invoiceNo = $invoice->id;

    $invoice->delete();

    ActivityLog::create([
        'user_id' => auth()->id(),
        'action' => 'Invoice Deleted',
        'description' => 'Invoice #' . $invoiceNo . ' was deleted'
    ]);

    return redirect()->back()->with('success', 'Invoice Deleted');
}
 public function update(Request $request, $id)
{
    $invoice = Invoice::findOrFail($id);

    // Update invoice basic info (if any)
    $invoice->customer_id = $request->customer_id;

    // Sum total_amount from invoice items
    $totalAmount = 0;

    foreach ($request->items as $itemData) {
        $invoiceItem = InvoiceItem::find($itemData['id']);
        if ($invoiceItem) {
            $invoiceItem->product_id = $itemData['product_id'];
            $invoiceItem->quantity = $itemData['quantity'];
            $invoiceItem->sale_price = $itemData['sale_price'];
            $invoiceItem->total_price = $itemData['quantity'] * $itemData['sale_price'];
            $invoice->debt = $request->debt ?? $invoice->debt;
            $invoiceItem->save();
        }
    }

    // ✅ ACTIVITY LOG (Kaliya kan ayaa lagu daray)
    ActivityLog::create([
        'user_id' => auth()->id(),
        'action' => 'Invoice Updated',
        'description' => 'Invoice #' . $invoice->id . ' was updated'
    ]);

    return redirect()->route('invoice.index')->with('success', 'Invoice updated successfully.');
}


    public function addInvoice($id)
{
    // Soo hel order-ka la rabo
    $order = Order::with('orderItems.product', 'customer')->findOrFail($id);

    // Haddii aad rabto in aad isticmaasho products ama customers kale, halkan ku dar
    $products = Product::all();
    $customers = Customer::all();

    // Soo celinta view-ga form-ka invoice cusub oo laga sameynayo order
    return view('admin.invoices.add_invoice', compact('order', 'products', 'customers'));
}

public function downloadPdf($id)
{
    $invoice = Invoice::with(['invoiceItems.product', 'customer'])->findOrFail($id);

    $pdf = Pdf::loadView('admin.invoices.invoice_pdf', compact('invoice'));

    return $pdf->download('invoice_'.$invoice->id.'.pdf');
}

public function createFromOrder($orderId)
{
    $order = Order::with(['orderItems.product', 'customer'])->findOrFail($orderId);
    $products = Product::all();
    $customers = Customer::all();

    return view('admin.invoices.add_invoice', compact('order', 'products', 'customers'));
}
public function getProductInfo(Request $request)
{
    $product = Product::findOrFail($request->id);

    return response()->json([
        'product' => [
            'category' => $product->category,
            'sales_unit_price' => $product->sales_unit_price
        ]
    ]);
}
}

