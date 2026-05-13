<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $transaction->invoice_number }}</title>
    <style>
        @media print {
            body {
                margin: 0;
            }

            .no-print {
                display: none;
            }
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            max-width: 300px;
            margin: 0 auto;
            padding: 10px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }

        .header p {
            margin: 2px 0;
            font-size: 10px;
        }

        .invoice-info {
            margin-bottom: 10px;
        }

        .invoice-info div {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .items-table th,
        .items-table td {
            padding: 2px;
            text-align: left;
            font-size: 10px;
        }

        .items-table th {
            border-bottom: 1px solid #000;
            font-weight: bold;
        }

        .items-table .qty {
            text-align: center;
            width: 20px;
        }

        .items-table .price,
        .items-table .total {
            text-align: right;
            width: 60px;
        }

        .summary {
            border-top: 1px solid #000;
            padding-top: 5px;
        }

        .summary div {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }

        .summary .total-line {
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #000;
            padding-top: 3px;
            margin-top: 5px;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #000;
            font-size: 10px;
        }

        .actions {
            text-align: center;
            margin: 20px 0;
        }

        .btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 0 5px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            background: #0056b3;
        }

        .btn-secondary {
            background: #6c757d;
        }

        .btn-secondary:hover {
            background: #545b62;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $storeSettings->name ?? 'TOKO SAYA' }}</h1>
        <p>{{ $storeSettings->address ?? 'Jl. Contoh No. 123, Kota' }}</p>
        <p>Telp: {{ $storeSettings->phone ?? '0812-3456-7890' }}</p>
        @if ($storeSettings->email)
            <p>Email: {{ $storeSettings->email }}</p>
        @endif
    </div>

    <div class="invoice-info">
        <div>
            <span>Invoice:</span>
            <span>{{ $transaction->invoice_number }}</span>
        </div>
        <div>
            <span>Tanggal:</span>
            <span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div>
            <span>Kasir:</span>
            <span>Admin</span>
        </div>
        @if ($transaction->customer_name)
            <div>
                <span>Pelanggan:</span>
                <span>{{ $transaction->customer_name }}</span>
            </div>
        @endif
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Item</th>
                <th class="qty">Qty</th>
                <th class="price">Harga</th>
                <th class="total">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaction->items as $item)
                <tr>
                    <td>
                        {{ $item->product_name }}
                        {{-- <br><small style="color: #666;">{{ ucfirst($item->price_type) }}</small> --}}
                    </td>
                    <td class="qty">{{ $item->quantity }}</td>
                    <td class="price">{{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="total">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <div>
            <span>Subtotal:</span>
            <span>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
        </div>

        @if ($transaction->discount_amount > 0)
            <div>
                <span>Diskon:</span>
                <span>-Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
            </div>
        @endif

        @if ($transaction->tax_amount > 0)
            <div>
                <span>Pajak:</span>
                <span>Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
            </div>
        @endif

        <div class="total-line">
            <span>TOTAL:</span>
            <span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
        </div>

        <div>
            <span>Bayar ({{ ucfirst($transaction->payment_method) }}):</span>
            <span>Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
        </div>

        <div>
            <span>Kembalian:</span>
            <span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="footer">
        <p>*** TERIMA KASIH ***</p>
        <p>{{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="actions no-print">
        <button class="btn" onclick="window.print()">🖨️ Print</button>
        <a href="{{ route('pos.index') }}" class="btn btn-secondary">← Kembali ke POS</a>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            // Wait a bit for page to fully load
            setTimeout(function() {
                // window.print();
            }, 1000);
        }
    </script>
</body>

</html>
