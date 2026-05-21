<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierPurchase;
use Illuminate\Http\Request;


class SupplierController extends Controller
{
    // List all suppliers
    public function index()
    {
        $suppliers = Supplier::latest()->get();
        return view('admin.suppliers.index', compact('suppliers'));
    }

    // Show form to create new supplier
    public function create()
    {
        return view('admin.suppliers.create_supplier');
    }

    // Store new supplier
    public function store(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:suppliers,email',
            'gender' => 'required|in:Male,Female',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        Supplier::create($request->all());

        return redirect()->route('all.suppliers')->with('success', 'Supplier created successfully.');
    }

    // Show edit form
    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('admin.suppliers.edit', compact('supplier'));
    }

    // Update supplier
    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $request->validate([
            'supplier_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:suppliers,email,' . $supplier->id,
            'gender' => 'required|in:Male,Female',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $supplier->update($request->all());

        return redirect()->route('all.suppliers')->with('success', 'Supplier updated successfully.');
    }

    // Delete supplier
    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return redirect()->route('all.suppliers')->with('success', 'Supplier deleted successfully.');
    }

    // Get all suppliers for use in dropdowns or related purchases
    public function getSuppliersForPurchase()
    {
        $suppliers = Supplier::select('id', 'supplier_name')->get();
        return response()->json($suppliers);
    }

}


