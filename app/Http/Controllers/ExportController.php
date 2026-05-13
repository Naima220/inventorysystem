<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PaymentsExport;

class ExportController extends Controller
{
    public function exportPaymentsExcel()
    {
        return Excel::download(new PaymentsExport, 'payments_report.xlsx');
    }
}
