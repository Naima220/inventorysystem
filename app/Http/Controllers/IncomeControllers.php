<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Salary;
use App\Models\Expense;

class IncomeControllers extends Controller
{
    public function incomeOutcome()
    {
        $totalIncome = Payment::sum('paid'); // Lacagaha la helay
        $totalPayments = $totalIncome;       // Payment is the income in this context

        $totalSalaries = Salary::sum('amount');     // Mushaharka
        $totalExpenses = Expense::sum('amount');    // Kharashyada kale

        $totalOutcome = $totalSalaries + $totalExpenses; // Wadarta kharashaadka

        $profit = $totalIncome - $totalOutcome; // Faa’iidada

        return view('admin.income_outcome', compact(
            'totalIncome',
            'totalSalaries',
            'totalExpenses',
            'totalPayments',
            'totalOutcome',
            'profit'
        ));
    }
}
