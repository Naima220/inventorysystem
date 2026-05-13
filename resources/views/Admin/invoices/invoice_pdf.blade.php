<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice PDF</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h2>Invoice #{{ $invoice->id }}</h2>
    <p><strong>Customer:</strong> {{ $invoice->customer->customer_name }}</p>
    <p><strong>Date:</strong> {{ $invoice->created_at->format('Y-m-d') }}</p>

    <h4>Items</h4>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Sale Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
        @foreach($invoice->invoiceItems as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format($item->sale_price, 2) }}</td>
                <td>${{ number_format($item->sale_price * $item->quantity, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
