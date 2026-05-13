<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use Carbon\Carbon;

class InvoicesReportController extends Controller
{
    // 🔥 DATE FILTER FUNCTION
    private function applyDateFilter($query, $request)
    {
        if ($request->type == 'daily') {
            $query->whereDate('created_at', now()->toDateString());
        }

        elseif ($request->type == 'weekly') {
            $query->whereBetween('created_at', [
                now()->subDays(7),
                now()
            ]);
        }

        elseif ($request->type == 'monthly') {
            $query->whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth()
            ]);
        }

        // manual date filter
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59',
            ]);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $query = Invoice::with('invoiceItems','customer');

        // ✅ THIS IS IMPORTANT
        $query = $this->applyDateFilter($query, $request);

        $invoices = $query->latest()->get();

        $grandTotal = 0;
        foreach ($invoices as $invoice) {
            $invoiceTotal = $invoice->invoiceItems->sum('total_price');
            $invoice->calculated_total = $invoiceTotal;
            $grandTotal += $invoiceTotal;
        }

        return view('admin.reports.invoices', compact('invoices','grandTotal'));
    }
}