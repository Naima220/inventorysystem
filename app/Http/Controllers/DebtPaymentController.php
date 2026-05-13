<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DebtPayment;

class DebtPaymentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'debt_id' => 'required',
            'paid_amount' => 'required|numeric'
        ]);

        DebtPayment::create($request->all());

        return back()->with('success', 'Payment recorded');
    }
}