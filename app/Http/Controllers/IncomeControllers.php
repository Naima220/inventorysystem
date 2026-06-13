<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Salary;
use App\Models\Expense;

class IncomeControllers extends Controller
{
    public function incomeOutcome(Request $request)
    {
        $year = $request->input('year', date('Y')); // Default to current year

        // Overall yearly totals
        $totalIncome = Payment::whereYear('created_at', $year)->sum('paid');
        $totalSalaries = Salary::whereYear('created_at', $year)->sum('amount');
        $totalExpenses = Expense::whereYear('created_at', $year)->sum('amount');
        $totalPurchases = \App\Models\SupplierPurchase::whereYear('created_at', $year)->sum('paid');
        
        $totalOutcome = $totalSalaries + $totalExpenses + $totalPurchases;
        $profit = $totalIncome - $totalOutcome;

        // Monthly Breakdown
        $monthlyData = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        for ($i = 1; $i <= 12; $i++) {
            $monthIncome = Payment::whereYear('created_at', $year)->whereMonth('created_at', $i)->sum('paid');
            $monthSalaries = Salary::whereYear('created_at', $year)->whereMonth('created_at', $i)->sum('amount');
            $monthExpenses = Expense::whereYear('created_at', $year)->whereMonth('created_at', $i)->sum('amount');
            $monthPurchases = \App\Models\SupplierPurchase::whereYear('created_at', $year)->whereMonth('created_at', $i)->sum('paid');
            
            $monthOutcome = $monthSalaries + $monthExpenses + $monthPurchases;
            
            $monthlyData[] = [
                'month' => $months[$i - 1],
                'income' => $monthIncome,
                'outcome' => $monthOutcome,
                'profit' => $monthIncome - $monthOutcome
            ];
        }

        $availableYears = range(2025, 2030);
        $totalPayments = $totalIncome; // For backward compatibility

        return view('admin.income_outcome', compact(
            'year',
            'availableYears',
            'totalIncome',
            'totalSalaries',
            'totalExpenses',
            'totalPurchases',
            'totalPayments',
            'totalOutcome',
            'profit',
            'monthlyData'
        ));
    }
}
