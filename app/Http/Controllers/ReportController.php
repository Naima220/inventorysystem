<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use PDF;  
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function exportPaymentsPDF()
    {
        $payments = Payment::with(['customer', 'product'])->get();

        $pdf = PDF::loadView('reports.payments_pdf', compact('payments'));

        return $pdf->download('payments_report.pdf');
     
{
    return Excel::download(new PaymentsExport, 'payments.xlsx');
}
    }
}
