<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\ActivityLog;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::latest()->get();
        return view('admin.expenses.index', compact('expenses'));
    }

    public function create()
    {
        return view('admin.expenses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_name' => 'required',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
        ]);

        Expense::create($request->all());

        return redirect()->route('expenses.index')->with('success', 'Expense added successfully');
    }

    public function edit($id)
    {
        $expense = Expense::findOrFail($id);
        return view('admin.expenses.edit', compact('expense'));
    }

   public function update(Request $request, $id)
{
    $expense = Expense::findOrFail($id);

    $request->validate([
        'expense_name' => 'required',
        'amount' => 'required|numeric|min:0',
        'expense_date' => 'required|date',
    ]);

    $expense->update($request->all());

    // ✅ ACTIVITY LOG
    ActivityLog::create([
        'user_id' => auth()->id(),
        'action' => 'Expense Updated',
        'description' => 'Expense "' . $expense->expense_name . '" was updated'
    ]);

    return redirect()->route('expenses.index')->with('success', 'Expense updated successfully');
}

   public function destroy($id)
{
    $expense = Expense::findOrFail($id);
    $expenseName = $expense->expense_name;

    $expense->delete();

    // ✅ ACTIVITY LOG
    ActivityLog::create([
        'user_id' => auth()->id(),
        'action' => 'Expense Deleted',
        'description' => 'Expense "' . $expenseName . '" was deleted'
    ]);

    return redirect()->route('expenses.index')->with('success', 'Expense deleted');
}
}
