<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $order->kode_pesanan }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #2563eb; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #2563eb; font-size: 24px; }
        .meta { margin-bottom: 20px; }
        .meta table { width: 100%; }
        .meta td { padding: 4px 0; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .table th { background-color: #f3f4f6; color: #1f2937; }
        .total-box { text-align: right; font-size: 16px; margin-top: 10px; }
        .total-box p { margin: 4px 0; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>

    <div class="header">
        <h1>BOOKSTORE INVOICE</h1>
        <p>Toko Buku Online Terpercaya</p>
    </div>

    <div class="meta">
        <table>
            <tr>
                <td><strong>Kode Pesanan:</strong> {{ $order->kode_pesanan }}</td>
                <td style="text-align: right;"><strong>Tanggal:</strong> {{ $order->created_at->format('d M Y H:i') }}</td>
            </tr>
            <tr>
                <td><strong>Pelanggan:</strong> {{ $order->user->name }} ({{ $order->user->email }})</td>
                <td style="text-align: right;"><strong>Status:</strong> <span style="text-transform: uppercase; color: green;">{{ $order->status }}</span></td>
            </tr>
        </table>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Judul Buku</th>
                <th>Harga Satuan</th>
                <th>Qty</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderDetails as $index => $detail)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $detail->book->nama_buku ?? 'Buku' }}</td>
                <td>Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                <td>{{ $detail->qty }}</td>
                <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-box">
        <p><strong>Total Harga:</strong> Rp {{ number_format($order->total_harga, 0, ',', '.') }}</p>
        @if($order->cash)
        <p><strong>Tunai (Cash):</strong> Rp {{ number_format($order->cash, 0, ',', '.') }}</p>
        <p><strong>Kembalian:</strong> Rp {{ number_format($order->kembalian, 0, ',', '.') }}</p>
        @endif
    </div>

    <div class="footer">
        <p>Terima kasih telah berbelanja di BookStore!</p>
    </div>

</body>
</html>
