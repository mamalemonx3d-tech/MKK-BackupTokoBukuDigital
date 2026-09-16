<?php

namespace App\Http\Controllers\Api;

use App\Helpers\OrderCodeGenerator;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Book;
use App\Models\Order;
use App\Models\OrderDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // List orders for admin or user
    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['user:id,name,email', 'orderDetails.book:id,nama_buku,harga_jual,gambar']);

        if ($request->user()->role !== 'admin') {
            $query->where('user_id', $request->user()->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'data' => OrderResource::collection($orders),
        ]);
    }

    // Submit Cart & Create Order (Status: pending)
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.book_id' => 'required|exists:books,id',
            'items.*.qty' => 'required|integer|min:1',
            'payment_method' => 'nullable|string|in:cash,bank_transfer,qris', // nullable karena belum wajib dari frontend
        ]);

        return DB::transaction(function () use ($request) {
            $totalHarga = 0;
            $itemsToProcess = [];

            foreach ($request->items as $item) {
                $book = Book::lockForUpdate()->find($item['book_id']);

                if ($book->stok < $item['qty']) {
                    return response()->json([
                        'message' => "Stok buku '{$book->nama_buku}' tidak mencukupi (Sisa: {$book->stok}).",
                    ], 422);
                }

                $subtotal = (float) $book->harga_jual * (int) $item['qty'];
                $totalHarga += $subtotal;

                $itemsToProcess[] = [
                    'book' => $book,
                    'qty' => (int) $item['qty'],
                    'harga_satuan' => (float) $book->harga_jual,
                    'subtotal' => $subtotal,
                ];
            }

            // Generate Kode Pesanan (e.g. A001, A021)
            $kodePesanan = OrderCodeGenerator::generate();

            $order = Order::create([
                'kode_pesanan' => $kodePesanan,
                'user_id' => $request->user()->id,
                'status' => 'pending',
                'payment_method' => $request->payment_method ?? 'cash',
                'total_harga' => $totalHarga,
            ]);

            foreach ($itemsToProcess as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'book_id' => $item['book']->id,
                    'qty' => $item['qty'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Reduce stock
                $item['book']->decrement('stok', $item['qty']);
            }

            return response()->json([
                'message' => 'Pesanan berhasil dibuat dengan kode ' . $kodePesanan,
                'data' => new OrderResource($order->load(['user:id,name,email', 'orderDetails.book:id,nama_buku,harga_jual,gambar'])),
            ], 201);
        });
    }

    public function show(Order $order): JsonResponse
    {
        return response()->json([
            'data' => new OrderResource($order->loadMissing(['user:id,name,email', 'orderDetails.book:id,nama_buku,harga_jual,gambar'])),
        ]);
    }

    // Admin: Konfirmasi Pesanan (pending -> confirmed)
    public function confirm(Order $order): JsonResponse
    {
        if ($order->status !== 'pending') {
            return response()->json(['message' => 'Hanya pesanan berstatus pending yang bisa dikonfirmasi.'], 400);
        }

        $order->update(['status' => 'confirmed']);
        $order->status = 'confirmed';
        $order->loadMissing(['user:id,name,email', 'orderDetails.book:id,nama_buku,harga_jual,gambar']);

        return response()->json([
            'message' => 'Pesanan ' . $order->kode_pesanan . ' berhasil dikonfirmasi.',
            'data' => new OrderResource($order),
        ]);
    }

    // Admin Kasir: Process Cash & Change (confirmed -> completed)
    public function pay(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'cash' => 'required|numeric|min:' . $order->total_harga,
        ], [
            'cash.min' => 'Jumlah uang tunai kurang dari total harga pesanan (Rp ' . number_format($order->total_harga, 0, ',', '.') . ').',
        ]);

        $cash = (float) $request->cash;
        $kembalian = $cash - (float) $order->total_harga;

        $order->update([
            'cash' => $cash,
            'kembalian' => $kembalian,
            'status' => 'completed',
        ]);
        $order->cash = $cash;
        $order->kembalian = $kembalian;
        $order->status = 'completed';
        $order->loadMissing(['user:id,name,email', 'orderDetails.book:id,nama_buku,harga_jual,gambar']);

        return response()->json([
            'message' => 'Pembayaran berhasil diproses. Kembalian: Rp ' . number_format($kembalian, 0, ',', '.'),
            'data' => new OrderResource($order),
        ]);
    }

    // Order Scan Endpoint: Automatically confirm status to 'confirmed' in DB
    public function scan(Request $request): JsonResponse
    {
        $request->validate([
            'kode_pesanan' => 'required|string',
        ]);

        $kode = strtoupper(trim($request->kode_pesanan));

        $query = Order::where(function ($q) use ($kode) {
            $q->where('kode_pesanan', $kode)
              ->orWhere('kode_pesanan', 'LIKE', '%' . $kode);
        });

        // If logged-in user is not admin, limit search to their own orders
        if ($request->user()->role !== 'admin') {
            $query->where('user_id', $request->user()->id);
        }

        $order = $query->with(['user:id,name,email', 'orderDetails.book:id,nama_buku,harga_jual,gambar'])->first();

        if (!$order) {
            return response()->json([
                'message' => "Pesanan dengan kode '{$kode}' tidak ditemukan.",
            ], 404);
        }

        // Auto update status to 'confirmed' when scanned if status was 'pending'!
        if ($order->status === 'pending') {
            $order->update(['status' => 'confirmed']);
            $order->status = 'confirmed';
        }

        return response()->json([
            'message' => "Scan Berhasil! Status pesanan {$order->kode_pesanan} berhasil diperbarui di Database ke 'CONFIRMED'.",
            'data' => new OrderResource($order),
        ]);
    }

    // Print / Download Invoice PDF
    public function invoicePdf(Order $order): Response
    {
        $order->load(['user', 'orderDetails.book']);

        $pdf = Pdf::loadView('pdf.invoice', compact('order'));
        return $pdf->download('Invoice-' . $order->kode_pesanan . '.pdf');
    }
}
