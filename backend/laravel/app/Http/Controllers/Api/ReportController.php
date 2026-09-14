<?php

namespace App\Http\Controllers\Api;

use App\Exports\ReportExport;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $orderQuery = Order::with(['user', 'orderDetails.book'])->where('status', 'completed');

        if ($startDate) {
            $orderQuery->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $orderQuery->whereDate('created_at', '<=', $endDate);
        }

        $completedOrders = $orderQuery->orderBy('created_at', 'desc')->get();

        $totalOmset = $completedOrders->sum('total_harga');

        // Calculate total profit from books sold
        $totalKeuntungan = 0;
        $totalBukuTerjual = 0;

        foreach ($completedOrders as $order) {
            foreach ($order->orderDetails as $detail) {
                $totalBukuTerjual += $detail->qty;
                if ($detail->book) {
                    $totalKeuntungan += ((float) $detail->book->keuntungan * $detail->qty);
                }
            }
        }

        return response()->json([
            'summary' => [
                'total_omset' => (float) $totalOmset,
                'total_keuntungan' => (float) $totalKeuntungan,
                'total_buku_terjual' => (int) $totalBukuTerjual,
                'total_transaksi' => $completedOrders->count(),
            ],
            'orders' => $completedOrders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'kode_pesanan' => $order->kode_pesanan,
                    'pelanggan' => $order->user?->name,
                    'total_harga' => (float) $order->total_harga,
                    'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                    'orderDetails' => $order->orderDetails->map(function ($detail) {
                        return [
                            'qty' => $detail->qty,
                            'harga_satuan' => (float) $detail->harga_satuan,
                            'subtotal' => (float) $detail->subtotal,
                            'book' => [
                                'nama_buku' => $detail->book?->nama_buku ?? 'Buku',
                            ],
                        ];
                    }),
                ];
            }),
        ]);
    }

    public function exportPdf(Request $request): Response
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $orderQuery = Order::with(['user', 'orderDetails.book'])->where('status', 'completed');

        if ($startDate) {
            $orderQuery->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $orderQuery->whereDate('created_at', '<=', $endDate);
        }

        $orders = $orderQuery->orderBy('created_at', 'desc')->get();
        $totalOmset = $orders->sum('total_harga');
        $totalKeuntungan = 0;

        foreach ($orders as $order) {
            foreach ($order->orderDetails as $detail) {
                if ($detail->book) {
                    $totalKeuntungan += ((float) $detail->book->keuntungan * $detail->qty);
                }
            }
        }

        $pdf = Pdf::loadView('pdf.report', compact('orders', 'totalOmset', 'totalKeuntungan', 'startDate', 'endDate'));
        return $pdf->download('Laporan-Penjualan.pdf');
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $orderQuery = Order::with(['user', 'orderDetails.book'])->where('status', 'completed');

        if ($startDate) {
            $orderQuery->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $orderQuery->whereDate('created_at', '<=', $endDate);
        }

        $orders = $orderQuery->orderBy('created_at', 'desc')->get();
        $totalOmset = $orders->sum('total_harga');
        $totalKeuntungan = 0;

        foreach ($orders as $order) {
            foreach ($order->orderDetails as $detail) {
                if ($detail->book) {
                    $totalKeuntungan += ((float) $detail->book->keuntungan * $detail->qty);
                }
            }
        }

        $fileName = 'Laporan-Penjualan-' . now()->format('Y-m-d-H-i-s') . '.xlsx';
        return Excel::download(new ReportExport($orders, $startDate, $endDate, $totalOmset, $totalKeuntungan), $fileName);
    }
}
