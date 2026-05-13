<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

use PDF;

class PaymentController extends Controller
{
    // ✅ Liiska dhammaan payments
    public function index()
    {
        $payments = Payment::with('invoice.invoiceItems.product', 'customer')->latest()->get();
        return view('admin.payments.all_payments', compact('payments'));
    }

    // ✅ Foomka add_payment
    public function create($invoiceId)
    {
        $invoice = Invoice::with('invoiceItems.product', 'customer')->findOrFail($invoiceId);
        return view('admin.payments.add_payment', compact('invoice'));
    }

    // ✅ Kaydinta payment cusub
   public function store(Request $request)
{
    $request->validate([
        'invoice_id' => 'required|exists:invoices,id',
        'paid'       => 'required|numeric|min:0',
        'debt'       => 'nullable|numeric|min:0',
        'discount'   => 'nullable|numeric|min:0',
    ]);

    $invoice = Invoice::with('invoiceItems.product')->findOrFail($request->invoice_id);

    // Xisaabi total_payment
    $totalAmount = 0;
    foreach ($invoice->invoiceItems as $item) {
        $totalAmount += $item->quantity * $item->product->sales_unit_price;
    }

    $discount = $request->discount ?? 0;

    if ($discount > $totalAmount) {
        return back()->withErrors(['discount' => 'Discount cannot be greater than total amount']);
    }

    $amountAfterDiscount = $totalAmount - $discount;

    if ($request->paid > $amountAfterDiscount) {
        return back()->withErrors(['paid' => 'Paid amount cannot exceed total after discount']);
    }

// 🔴 CHECK DUPLICATE PAYMENT (IMPORTANT)

// 1. Xisaabi total + items signature
$currentItems = [];

foreach ($invoice->invoiceItems as $item) {
    $currentItems[] = [
        'product_id' => $item->product_id,
        'qty' => $item->quantity,
        'price' => $item->product->sales_unit_price,
    ];
}

// sort si isku mid u noqdaan
sort($currentItems);

// 2. Hel payments hore ee invoice-kan
$oldPayments = Payment::where('invoice_id', $invoice->id)->get();

foreach ($oldPayments as $oldPayment) {

    $oldItems = PaymentItem::where('payment_id', $oldPayment->id)
        ->get()
        ->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'qty' => $item->qty,
                'price' => $item->sale_price,
            ];
        })->toArray();

    sort($oldItems);

    // 3. Compare items + total
    if ($oldItems == $currentItems &&
        $oldPayment->total_payment == $amountAfterDiscount &&
        $oldPayment->paid == $request->paid
    ) {
        return back()->with('error', '❌ Payment already exists for this invoice with same data!');
    }
}


    $debtAmount = $amountAfterDiscount - $request->paid;

    // ✅ Save payment
    $payment = Payment::create([
        'invoice_id'    => $invoice->id,
        'customer_id'   => $invoice->customer_id,
        'paid'          => $request->paid,
        'debt'          => $debtAmount,
        'discount'      => $discount,
        'total_payment' => $amountAfterDiscount,
        'date'          => now(),
    ]);

    // ✅ Save payment items
    foreach ($invoice->invoiceItems as $item) {
        PaymentItem::create([
            'payment_id'  => $payment->id,
            'product_id'  => $item->product_id,
            'sale_price'  => $item->product->sales_unit_price,
            'qty'         => $item->quantity,
            'total_price' => $item->quantity * $item->product->sales_unit_price,
        ]);
    }

    // ================================
    // ✅ DEBT LOGIC (AAD RABTAY)
    // ================================

    if ($debtAmount > 0) {

        // create debt
        $debt = \App\Models\Debt::create([
            'customer_id' => $invoice->customer_id,
            'amount' => $debtAmount,
            'description' => 'Invoice #' . $invoice->id,
            'status' => 'unpaid',
        ]);

        // haddii payment jiro (partial)
        if ($request->paid > 0) {
            \App\Models\DebtPayment::create([
                'debt_id' => $debt->id,
                'paid_amount' => $request->paid,
            ]);

            $totalPaid = $debt->payments()->sum('paid_amount');

            if ($totalPaid >= $debt->amount) {
                $debt->status = 'paid';
            } elseif ($totalPaid > 0) {
                $debt->status = 'partial';
            } else {
                $debt->status = 'unpaid';
            }

            $debt->save();
        }
    }

    return redirect()->route('payments.index')->with('success', '✅ Payment saved successfully');
}

    // ✅ Edit payment
    public function edit($id)
    {
        $payment = Payment::findOrFail($id);
        return view('admin.payments.edit_payment', compact('payment'));
    }

    // ✅ Update payment
   public function update(Request $request, $id)
{
    $payment = Payment::findOrFail($id);

    $request->validate([
        'paid'     => 'required|numeric|min:0',
        'debt'     => 'nullable|numeric|min:0',
        'discount' => 'nullable|numeric|min:0',
    ]);

    $totalPaymentBeforeDiscount = $payment->total_payment + $payment->discount; // or recalculate if needed
    $discount = $request->discount ?? 0;

    if ($discount > $totalPaymentBeforeDiscount) {
        return back()->withErrors(['discount' => 'Discount cannot be greater than total amount']);
    }

    $amountAfterDiscount = $totalPaymentBeforeDiscount - $discount;

    if ($request->paid > $amountAfterDiscount) {
        return back()->withErrors(['paid' => 'Paid cannot exceed total after discount']);
    }

    $debt = $amountAfterDiscount - $request->paid;

    $payment->update([
        'paid'          => $request->paid,
        'debt'          => $debt,
        'discount'      => $discount,
        'total_payment' => $amountAfterDiscount,
    ]);

    // ✅ ACTIVITY LOG
    ActivityLog::create([
        'user_id' => auth()->id(),
        'action' => 'Payment Updated',
        'description' => 'Payment #' . $payment->id . ' was updated'
    ]);

    return redirect()->route('payments.index')->with('success', '✅ Payment updated successfully');
}
    // ✅ Delete
    public function destroy($id)
{
    $payment = Payment::findOrFail($id);
    $paymentId = $payment->id;

    $payment->delete();

    // ✅ ACTIVITY LOG
    ActivityLog::create([
        'user_id' => auth()->id(),
        'action' => 'Payment Deleted',
        'description' => 'Payment #' . $paymentId . ' was deleted'
    ]);

    return redirect()->route('payments.index')->with('success', '✅ Payment deleted');
}

    // ✅ Download PDF
    public function downloadPdf($id)
    {
        $payment = Payment::with('invoice.invoiceItems.product', 'customer')->findOrFail($id);
        $pdf = PDF::loadView('admin.payments.payment_pdf', compact('payment'));
        return $pdf->download('payment-'.$payment->id.'.pdf');
    }

    
public function print($id)
{
    $payment = Payment::with([
                    'invoice.invoiceItems.product',
                    'customer',
                ])
                ->findOrFail($id);

    $pdf = Pdf::loadView('admin.payments.payment_pdf', compact('payment'))
              ->setPaper('a5', 'portrait'); // 🔥 A5 size

    return $pdf->stream('payment-'.$payment->id.'.pdf');
}

}