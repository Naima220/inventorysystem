<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Debt;
use App\Models\Customer;
use App\Models\DebtPayment;

class DebtController extends Controller
{
    // Show all debts
    public function index()
    {
        $debts = Debt::with('customer', 'payments')->latest()->get();
        return view('admin.debts.index', compact('debts'));
    }

    // Create form
    public function create()
    {
        $customers = Customer::all();
        return view('admin.debts.create', compact('customers'));
    }

    // Store new debt
    public function store(Request $request)
{
    $request->validate([
        'debts.*.customer_id' => 'required',
        'debts.*.amount' => 'required|numeric'
    ]);

    foreach ($request->debts as $debt) {
        \App\Models\Debt::create([
            'customer_id' => $debt['customer_id'],
            'amount' => $debt['amount'],
            'description' => $debt['description'] ?? null,
        ]);
    }

    return redirect()->route('debt.index')->with('success', 'Debts added successfully');
}

    // Show payment form
    public function payForm($id)
    {
        $debt = Debt::with('customer')->findOrFail($id);
        return view('admin.debts.pay', compact('debt'));
    }

    // Store payment
    public function payStore(Request $request, $id)
{
    $request->validate([
        'amount' => 'required|numeric|min:1'
    ]);

    $debt = Debt::with('payments')->findOrFail($id);

    // Save payment
    DebtPayment::create([
        'debt_id' => $debt->id,
        'paid_amount' => $request->amount,
    ]);

    // Update status
    $totalPaid = $debt->payments()->sum('paid_amount') + $request->amount;

    if ($totalPaid >= $debt->amount) {
        $debt->status = 'paid';
    } elseif ($totalPaid > 0) {
        $debt->status = 'partial';
    } else {
        $debt->status = 'unpaid';
    }

    $debt->save();

    return back()->with('success', 'Payment added');
}

    // Delete debt
    public function destroy($id)
    {
        $debt = Debt::findOrFail($id);
        $debt->delete();

        return back()->with('success', 'Debt deleted');
    }
    
}