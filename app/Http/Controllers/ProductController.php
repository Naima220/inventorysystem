<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
 use App\Models\ActivityLog;

use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function purchaseData($id)
{
    $product = Product::findOrFail($id);

    return view('admin.purchase_products', compact('product'));

}
public function storePurchase(Request $request)
{
    $product = Product::findOrFail($request->product_id);

    // Hubi in number yahay
    $addStock = (int) $request->purchase;

    if ($addStock > 0) {
        $product->stock += $addStock;
        $product->save();
    }

    return redirect()->route('all.product')
        ->with('success', 'Stock updated successfully.');
}
    // Show all products for current shop (or super admin sees all)
    public function allProduct()
    {
        $products = Product::all(); // GlobalScope ayaa xaddidaya shop-ka
        return view('admin.all_product', compact('products'));
    }

    // Show form to create multiple products
    public function createMultiple()
    {
        return view('admin.products.add_multiple_products');
    }

    // Store multiple products
    public function storeMultiple(Request $request)
    {
        // Validation (product_sn excluded, since we auto-generate)
        $request->validate([
            'products.*.name'             => 'required',
            'products.*.category'         => 'required',
            'products.*.stock'            => 'required|integer',
            'products.*.unit_price'       => 'required|numeric',
            'products.*.sales_unit_price' => 'required|numeric',
        ]);

        foreach ($request->products as $productData) {

            // Get last product SN
            $last_product = Product::orderBy('id', 'desc')->first();

            if ($last_product) {
                // Extract numeric part (e.g. S-001 → 001)
                $parts = explode('-', $last_product->product_sn);
                $last_sn_number = (int) end($parts);
                $new_number = str_pad($last_sn_number + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $new_number = '001';
            }

            // Generate new SN with tenant prefix
            $new_sn = 'S' . tenant('id') . '-' . $new_number;

            // Insert product
            Product::create([
                'product_sn'      => $new_sn,
                'name'            => $productData['name'],
                'category'        => $productData['category'],
                'stock'           => $productData['stock'],
                'unit_price'      => $productData['unit_price'],
                'sales_unit_price'=> $productData['sales_unit_price'],
            ]);
        }

        return redirect()->route('products.multiple.create')
                         ->with('success', 'Products inserted successfully!');
    }

    // Edit a product
    public function editProduct($id)
    {
        $product = Product::findOrFail($id);
        return view('admin.edit_product', compact('product'));
    }
// Update product
public function updateProduct(Request $request, $id)
{
    $request->validate([
        'product_sn'       => 'required|unique:products,product_sn,' . $id,
        'name'             => 'required',
        'category'         => 'required',
        'stock'            => 'required|integer',
        'unit_price'       => 'required|numeric',
        'sales_unit_price' => 'required|numeric',
    ]);
     
    $product = Product::findOrFail($id);

    $product->update([
        'product_sn'       => $request->product_sn,
        'name'             => $request->name,
        'category'         => $request->category,
        'stock'            => $request->stock,
        'unit_price'       => $request->unit_price,
        'sales_unit_price' => $request->sales_unit_price,
    ]);

    // ✅ ACTIVITY LOG
    ActivityLog::create([
        'user_id' => auth()->id(),
        'action' => 'Product Updated',
        'description' => 'Product "' . $product->name . '" was updated'
    ]);

    return redirect()->route('all.product')
        ->with('success', 'Product updated successfully.');
}
  
 
public function destroy($id)
{
    $product = Product::findOrFail($id);
    $name = $product->name;
    $product->delete();

    ActivityLog::create([
        'user_id' => auth()->id(),
        'action' => 'Product Deleted',
        'description' => 'Product "' . $name . '" was deleted'
    ]);

    return redirect()->back();
}
public function availableProducts()
{
    $products = \App\Models\Product::where('stock', '>', 0)->get();

    return view('admin.available_products', compact('products'));
}
}