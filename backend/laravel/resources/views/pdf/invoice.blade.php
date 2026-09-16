<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $order->kode_pesanan }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #000000;
            background-color: #ffffff;
            padding: 30px;
        }
        .header-container {
            border-bottom: 2px solid #000000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .brand-title {
            font-size: 20px;
            font-weight: 900;
            letter-spacing: -0.5px;
            text-transform: uppercase;
            color: #000000;
        }
        .brand-badge {
            display: inline-block;
            background-color: #000000;
            color: #ffffff;
            font-size: 10px;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 4px;
            margin-left: 6px;
            vertical-align: middle;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 10px;
            color: #52525b;
            margin-top: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .invoice-title {
            text-align: right;
            font-size: 16px;
            font-weight: 900;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .meta-table td {
            vertical-align: top;
            padding: 4px 0;
            font-size: 11px;
        }
        .order-code-badge {
            display: inline-block;
            border: 1.5px solid #000000;
            padding: 2px 8px;
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            font-size: 12px;
        }
        .status-badge {
            display: inline-block;
            background-color: #000000;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            padding: 2px 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #000000;
            color: #ffffff;
            font-weight: 900;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            border: 1px solid #000000;
        }
        .items-table td {
            padding: 8px 10px;
            border: 1px solid #000000;
            font-size: 11px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }
        .font-bold { font-weight: bold; }
        
        .summary-wrapper {
            width: 100%;
            margin-top: 10px;
            margin-bottom: 30px;
        }
        .summary-table {
            width: 280px;
            margin-left: auto;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 5px 8px;
            font-size: 11px;
        }
        .summary-table .total-row td {
            border-top: 2px solid #000000;
            border-bottom: 2px solid #000000;
            font-weight: 900;
            font-size: 13px;
            padding: 8px;
        }
        
        .footer {
            border-top: 1px dashed #000000;
            padding-top: 15px;
            text-align: center;
            font-size: 10px;
            color: #3f3f46;
        }
        .footer p {
            margin-bottom: 3px;
        }
        .footer-brand {
            font-weight: bold;
            color: #000000;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>

    <div class="header-container">
        <table style="width: 100%;">
            <tr>
                <td>
                    <span class="brand-title">TOKOBukuDigital</span>
                    <span class="brand-badge">INVOICE</span>
                    <p class="subtitle">Platform Pembelian & Katalog Buku Digital Terpercaya</p>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <div class="invoice-title">STRUK TRANSAKSI</div>
                    <div class="subtitle">No: <span class="order-code-badge">{{ $order->kode_pesanan }}</span></div>
                </td>
            </tr>
        </table>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 50%;">
                <div style="font-size: 10px; font-weight: bold; text-transform: uppercase; color: #71717a; margin-bottom: 2px;">DITAGIHKAN KEPADA:</div>
                <div style="font-weight: bold; font-size: 13px;">{{ $order->user->name ?? 'Pelanggan' }}</div>
                <div>Email: {{ $order->user->email ?? '-' }}</div>
                @if($order->user->username)
                <div>Username: {{ '@' . $order->user->username }}</div>
                @endif
            </td>
            <td style="width: 50%; text-align: right;">
                <div style="font-size: 10px; font-weight: bold; text-transform: uppercase; color: #71717a; margin-bottom: 2px;">DETAIL PESANAN:</div>
                <div>Tanggal: <span class="font-bold">{{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : '-' }} WIB</span></div>
                <div>Metode: <span class="font-bold font-mono">{{ strtoupper($order->payment_method ?? 'CASH / KASIR') }}</span></div>
                <div style="margin-top: 3px;">Status: <span class="status-badge">{{ strtoupper($order->status) }}</span></div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 35px;" class="text-center">No</th>
                <th>Judul Buku</th>
                <th style="width: 100px;" class="text-right">Harga Satuan</th>
                <th style="width: 50px;" class="text-center">Qty</th>
                <th style="width: 110px;" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderDetails as $index => $detail)
            <tr>
                <td class="text-center font-mono">{{ $index + 1 }}</td>
                <td>
                    <strong style="font-size: 12px;">{{ $detail->book->nama_buku ?? 'Buku' }}</strong>
                    @if($detail->book && $detail->book->category)
                    <div style="font-size: 9px; color: #52525b; text-transform: uppercase;">Kategori: {{ $detail->book->category->nama_kategori }}</div>
                    @endif
                </td>
                <td class="text-right font-mono">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                <td class="text-center font-mono font-bold">{{ $detail->qty }}</td>
                <td class="text-right font-mono font-bold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-wrapper">
        <table class="summary-table">
            <tr class="total-row">
                <td>TOTAL TAGIHAN:</td>
                <td class="text-right font-mono">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
            </tr>
            @if($order->cash)
            <tr>
                <td style="border-bottom: 1px solid #e4e4e7;">Tunai Diterima (Cash):</td>
                <td class="text-right font-mono" style="border-bottom: 1px solid #e4e4e7;">Rp {{ number_format($order->cash, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Uang Kembalian:</td>
                <td class="text-right font-mono font-bold">Rp {{ number_format($order->kembalian ?? 0, 0, ',', '.') }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="footer">
        <p class="footer-brand">TOKOBukuDigital &bull; Terima kasih atas pesanan Anda!</p>
        <p>Struk / Invoice ini sah dan dihasilkan secara otomatis oleh sistem TokoBukuDigital.</p>
        <p style="font-family: 'Courier New', Courier, monospace; font-size: 9px; color: #71717a; margin-top: 6px;">Kode Verifikasi: [ {{ $order->kode_pesanan }} ]</p>
    </div>

</body>
</html>
