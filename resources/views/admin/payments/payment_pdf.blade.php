<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment #{{ $payment->id }} PDF</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            margin: 40px;
        }

        h1, h2, h3, p {
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 28px;
            color: #222;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 14px;
            color: #555;
        }

        .signature {
            margin-top: 60px;
            text-align: right;
            margin-right: 50px;
        }

        .footer {
            position: fixed;
            bottom: 30px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 12px;
            color: #555;
        }
    </style>
</head>

<body>

@php
    // Use tenant helper to get dynamic shop information
    $shopName = tenant('name') ?? 'Mine Market';
    $shopAddress = 'Hargeisa, Somaliland';
    $shopPhone = tenant('phone') ?? 'N/A';
    // Get owner email: first try tenant data, then get from first admin user
    $ownerUser = \App\Models\User::orderBy('id')->first();
    $shopEmail = tenant('email') ?? ($ownerUser ? $ownerUser->email : 'N/A');
@endphp

{{-- ✅ HEADER (Dynamic Shop Info) --}}
<div class="header">
    <h1>{{ $shopName }}</h1>
    <p>
        {{ $shopAddress }} –
        Tel: {{ $shopPhone }} –
        Email: {{ $shopEmail }}
    </p>
    <hr>
</div>

<h2>Payment #{{ $payment->id }}</h2>

<p><strong>Customer:</strong> {{ $payment->customer->customer_name }}</p>
<p><strong>Date:</strong> {{ $payment->date }}</p>

<h3 style="margin-top:20px;">Items Purchased</h3>

<table>
    <thead>
        <tr>
            <th>Product</th>
            <th>Sale Price</th>
            <th>Quantity</th>
            <th>Total Price</th>
        </tr>
    </thead>
    <tbody>
        @foreach($payment->invoice->invoiceItems as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>${{ number_format($item->product->sales_unit_price, 2) }}</td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format($item->total_price, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div style="text-align: right; margin-top: 20px;">
    <p><strong>Total (After Discount):</strong> ${{ number_format($payment->total_payment,2) }}</p>
    <p><strong>Paid:</strong> ${{ number_format($payment->paid,2) }}</p>
    <p><strong>Debt:</strong> ${{ number_format($payment->debt,2) }}</p>
</div>

{{-- ✅ Signature --}}
<div class="signature">
    <p>__________________________</p>
    <p>Authorized Signature</p>
</div>

{{-- ✅ Footer Dynamic --}}
<div class="footer">
    {{ tenant('name') ?? 'Mine Market' }} &nbsp;|&nbsp; © {{ date('Y') }} All Rights Reserved
</div>

</body>
</html>