<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // List all customers
    public function index()
    {
        $customers = Customer::all();
        return view('admin.customers.all_customers', compact('customers'));
    }

    // Show create customer form
    public function create()
    {
        return view('admin.customers.create_customer');
    }

    // Store new customer
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email', // ✅ Halkan waa la badalay
            'phone' => 'nullable|string|max:20',
            'gender' => 'required|in:Male,Female',
            'address' => 'nullable|string|max:255',
        ]);

        Customer::create($request->only(['customer_name', 'email', 'phone', 'gender', 'address']));

        return redirect()->route('all.customers')->with('success', 'Customer added successfully!');
    }

    // Show edit form for a customer
    public function edit(Customer $customer)
    {
        return view('admin.customers.edit_customer', compact('customer'));
    }

    // Update a customer
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email,' . $customer->id, // ✅ Halkan waa la badalay
            'phone' => 'nullable|string|max:20',
            'gender' => 'required|in:Male,Female',
            'address' => 'nullable|string|max:255',
        ]);

        $customer->update($request->only(['customer_name', 'email', 'phone', 'gender', 'address']));

        return redirect()->route('all.customers')->with('success', 'Customer updated successfully!');
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return redirect()->back()->with('success', 'Customer deleted successfully.');
    }

    public function getCustomerDebt($customerId)
    {
        $debts = \App\Models\Debt::where('customer_id', $customerId)
            ->with('payments')
            ->get();

        $totalDebt = $debts->sum('amount');
        $paid = $debts->flatMap->payments->sum('paid_amount');

        return response()->json([
            'total' => $totalDebt,
            'paid' => $paid,
            'remaining' => $totalDebt - $paid
        ]);
    }
}