<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\SupplierPurchase;
use App\Models\SupplierPurchaseItem;
use Illuminate\Http\Request;

class SupplierPurchaseController extends Controller
{
    // ✅ List of Purchases
    public function index()
    {
        $purchases = SupplierPurchase::with('items.product')->latest()->get();
        return view('admin.supplier_purchases.index', compact('purchases'));
    }

    // ✅ Form: Add Existing Product Purchase
    public function create()
    {
        $suppliers = Supplier::all();
        return view('admin.supplier_purchases.create', compact('suppliers'));
    }

    // ✅ Store Purchase of Existing Products
    public function store(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.cost_price' => 'required|numeric|min:0',
            'items.*.qty' => 'required|integer|min:1',
            'discount' => 'nullable|numeric|min:0',
            'paid' => 'required|numeric|min:0',
        ]);

        $totalCost = collect($request->items)->reduce(function($carry, $item){
            return $carry + ($item['cost_price'] * $item['qty']);
        }, 0);

        $discount = $request->discount ?? 0;
        $supCost = $totalCost - $discount;
        $balance = $supCost - $request->paid;

        $purchase = SupplierPurchase::create([
            'supplier_name' => $request->supplier_name,
            't_cost' => $totalCost,
            'discount' => $discount,
            'sup_cost' => $supCost,
            'paid' => $request->paid,
            'balance' => $balance,
        ]);

        foreach ($request->items as $item) {
            SupplierPurchaseItem::create([
                'supplier_purchase_id' => $purchase->id,
                'product_id' => $item['product_id'],
                'cost_price' => $item['cost_price'],
                'qty' => $item['qty'],
                'total_price' => $item['cost_price'] * $item['qty'],
            ]);

            $product = Product::find($item['product_id']);
            $product->stock += $item['qty'];
            $product->save();
        }

        return redirect()->route('supplier-purchases.index')->with('success', 'Purchase saved successfully!');
    }

    // ✅ Form: Add New Product & Purchase
    public function createNew()
    {
        return view('admin.supplier_purchases.create-new');
    }

    // ✅ Store New Product & Purchase
    public function storeNew(Request $request)
    {
        $request->validate([
            'product_name'  => 'required|string|max:100',
            'sales_price'   => 'required|numeric|min:0',
            'cost_price'    => 'required|numeric|min:0',
            'qty'           => 'required|integer|min:1',
            'supplier_name' => 'required|string|max:100',
            'discount'      => 'nullable|numeric|min:0',
            'paid'          => 'required|numeric|min:0',
        ]);

        $product = Product::create([
            'name'             => $request->product_name,
            'sales_unit_price' => $request->sales_price,
            'stock'            => $request->qty,
            'product_sn'       => uniqid('PRD'),
            'category'         => 'General', // default if not used in form
            'unit_price'       => $request->cost_price,
        ]);

        $totalCost = $request->cost_price * $request->qty;
        $discount  = $request->discount ?? 0;
        $supCost   = $totalCost - $discount;
        $balance   = $supCost - $request->paid;

        if ($request->paid > $supCost) {
            return back()->withErrors(['paid' => 'Paid cannot exceed supplier cost.']);
        }

        $purchase = SupplierPurchase::create([
            'supplier_name' => $request->supplier_name,
            't_cost'        => $totalCost,
            'discount'      => $discount,
            'sup_cost'      => $supCost,
            'paid'          => $request->paid,
            'balance'       => $balance,
        ]);

        SupplierPurchaseItem::create([
            'supplier_purchase_id' => $purchase->id,
            'product_id'           => $product->id,
            'cost_price'           => $request->cost_price,
            'qty'                  => $request->qty,
            'total_price'          => $totalCost,
        ]);

        return redirect()->route('supplier-purchases.index')
            ->with('success', 'New product and purchase saved!');
    }

    public function show($id)
    {
        $purchase = SupplierPurchase::with('items.product')->findOrFail($id);
        return view('admin.supplier_purchases.show', compact('purchase'));
    }

    public function edit($id)
    {
        $purchase = SupplierPurchase::with('items.product')->findOrFail($id);
        return view('admin.supplier_purchases.edit', compact('purchase'));
    }

    public function update(Request $request, $id)
    {
        $purchase = SupplierPurchase::findOrFail($id);

        $request->validate([
            'supplier_name' => 'required|string|max:100',
            'discount'      => 'nullable|numeric|min:0',
            'paid'          => 'required|numeric|min:0',
        ]);

        $discount = $request->discount ?? 0;
        $supCost = $purchase->t_cost - $discount;
        $balance = $supCost - $request->paid;

        $purchase->update([
            'supplier_name' => $request->supplier_name,
            'discount' => $discount,
            'sup_cost' => $supCost,
            'paid' => $request->paid,
            'balance' => $balance,
        ]);

        return redirect()->route('supplier-purchases.index')->with('success', 'Purchase updated!');
    }

    public function destroy($id)
    {
        SupplierPurchase::findOrFail($id)->delete();
        return redirect()->route('supplier-purchases.index')->with('success', 'Purchase deleted!');
    }
}
