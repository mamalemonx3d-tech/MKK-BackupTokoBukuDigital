<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 11px;
            color: #1f2937;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            padding: 40px 45px;
        }

        /* Letterhead */
        .letterhead {
            width: 100%;
            border-bottom: 3px solid #1e3a8a;
            padding-bottom: 18px;
            margin-bottom: 24px;
        }
        .letterhead td { vertical-align: top; }
        .brand-name {
            font-size: 22px;
            font-weight: bold;
            color: #1e3a8a;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .brand-sub {
            font-size: 10.5px;
            color: #6b7280;
            margin: 3px 0 0 0;
        }
        .report-label {
            text-align: right;
            font-size: 24px;
            font-weight: bold;
            color: #111827;
            letter-spacing: 1px;
            margin: 0;
        }
        .report-date {
            text-align: right;
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
        }

        /* Summary section */
        .summary-section {
            width: 100%;
            margin-bottom: 26px;
        }
        .summary-box {
            display: inline-block;
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 12px 20px;
            margin-right: 20px;
            margin-bottom: 10px;
        }
        .summary-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #6b7280;
            margin: 0;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 4px 0 0 0;
        }

        /* Items table */
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        table.items thead th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 10px 12px;
            text-align: left;
        }
        table.items thead th.num,
        table.items tbody td.num { text-align: center; }
        table.items thead th.right,
        table.items tbody td.right { text-align: right; }

        table.items tbody td {
            padding: 8px 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        table.items tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .item-title {
            font-weight: bold;
            color: #111827;
        }
        .item-order {
            font-weight: bold;
            color: #1e3a8a;
            font-family: 'Courier New', monospace;
        }

        /* Grand totals */
        .grand-totals {
            width: 100%;
            margin-top: 20px;
            margin-bottom: 40px;
        }
        .grand-totals td { vertical-align: top; }
        .totals-box {
            width: 300px;
            float: right;
        }
        .totals-box table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-box td {
            padding: 8px 0;
            font-size: 11px;
        }
        .totals-box td.label { color: #6b7280; }
        .totals-box td.value { text-align: right; color: #111827; }
        .totals-box tr.total td {
            border-top: 2px solid #1e3a8a;
            padding-top: 12px;
            font-size: 14px;
            font-weight: bold;
            color: #1e3a8a;
        }

        /* Footer */
        .footer {
            clear: both;
            margin-top: 60px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }
        .footer p {
            margin: 2px 0;
            font-size: 10px;
            color: #9ca3af;
        }
        .footer .thanks {
            font-size: 12px;
            color: #1e3a8a;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .page-break {
            page-break-after: always;
            margin-bottom: 40px;
        }
    </style>
</head>
<body>
    <div class="wrapper">

        <!-- Letterhead -->
        <table class="letterhead">
            <tr>
                <td style="width: 55%;">
                    <p class="brand-name">BOOKSTORE</p>
                    <p class="brand-sub">Toko Buku Online Terpercaya</p>
                    <p class="brand-sub">www.bookstore.id &nbsp;•&nbsp; admin@bookstore.id</p>
                </td>
                <td style="width: 45%;">
                    <p class="report-label">LAPORAN PENJUALAN</p>
                    <p class="report-date">
                        Periode: {{ $startDate ?? 'Awal' }} s/d {{ $endDate ?? 'Akhir' }}<br>
                        Tanggal Cetak: {{ now()->format('d M Y, H:i') }} WIB
                    </p>
                </td>
            </tr>
        </table>

        <!-- Summary -->
        <div class="summary-section">
            <div class="summary-box">
                <p class="summary-label">Total Omset</p>
                <p class="summary-value">Rp {{ number_format($totalOmset, 0, ',', '.') }}</p>
            </div>
            <div class="summary-box">
                <p class="summary-label">Total Keuntungan</p>
                <p class="summary-value">Rp {{ number_format($totalKeuntungan, 0, ',', '.') }}</p>
            </div>
            <div class="summary-box">
                <p class="summary-label">Total Transaksi</p>
                <p class="summary-value">{{ count($orders) }}</p>
            </div>
        </div>

        <!-- Items -->
        <table class="items">
            <thead>
                <tr>
                    <th class="num" style="width: 5%;">No</th>
                    <th style="width: 15%;">Kode Pesanan</th>
                    <th style="width: 40%;">Judul Buku</th>
                    <th class="num" style="width: 10%;">Qty</th>
                    <th class="right" style="width: 15%;">Harga Satuan</th>
                    <th class="right" style="width: 15%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php $itemNo = 1; @endphp
                @forelse($orders as $order)
                    @forelse($order->orderDetails as $detail)
                    <tr>
                        <td class="num">{{ $itemNo++ }}</td>
                        <td class="item-order">{{ $order->kode_pesanan }}</td>
                        <td class="item-title">{{ $detail->book->nama_buku ?? 'Buku' }}</td>
                        <td class="num">{{ $detail->qty }}</td>
                        <td class="right">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                        <td class="right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px;">Tidak ada item dalam pesanan ini</td>
                    </tr>
                    @endforelse
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">Tidak ada data transaksi untuk periode ini</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Grand Totals -->
        <table class="grand-totals">
            <tr>
                <td style="width: 60%;"></td>
                <td style="width: 40%;">
                    <div class="totals-box">
                        <table>
                            <tr>
                                <td class="label">Total Omset</td>
                                <td class="value">Rp {{ number_format($totalOmset, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="label">Total Keuntungan</td>
                                <td class="value">Rp {{ number_format($totalKeuntungan, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="total">
                                <td>Grand Total</td>
                                <td class="value">Rp {{ number_format($totalOmset, 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p class="thanks">Laporan Penjualan BookStore</p>
            <p>Laporan ini dibuat secara otomatis oleh sistem.</p>
            <p>Ada pertanyaan? Hubungi kami di admin@bookstore.id</p>
        </div>

    </div>
</body>
</html>