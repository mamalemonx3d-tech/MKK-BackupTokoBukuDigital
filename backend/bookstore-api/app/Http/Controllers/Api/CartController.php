<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * GET /api/cart — Ambil semua item keranjang user yang login
     */
    public function index(Request $request): JsonResponse
    {
        $items = Cart::with('book')
            ->where('user_id', $request->user()->id)
            ->get()
            ->map(function ($cart) {
                return [
                    'id'         => $cart->id,
                    'book_id'    => $cart->book_id,
                    'nama_buku'  => $cart->book?->nama_buku,
                    'harga_jual' => $cart->book?->harga_jual,
                    'gambar'     => $cart->book?->gambar,
                    'stok'       => $cart->book?->stok,
                    'qty'        => $cart->qty,
                ];
            });

        return response()->json(['data' => $items]);
    }

    /**
     * POST /api/cart — Tambah atau update item keranjang
     * Body: { book_id, qty }
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'qty'     => 'required|integer|min:1',
        ]);

        $book = Book::findOrFail($request->book_id);

        // Cek stok
        $existing = Cart::where('user_id', $request->user()->id)
            ->where('book_id', $request->book_id)
            ->first();

        $totalQty = $request->qty + ($existing ? $existing->qty : 0);

        if ($totalQty > $book->stok) {
            return response()->json([
                'message' => "Stok buku tidak mencukupi. Sisa stok: {$book->stok}"
            ], 422);
        }

        if ($existing) {
            $existing->update(['qty' => $existing->qty + $request->qty]);
            $cart = $existing->fresh();
        } else {
            $cart = Cart::create([
                'user_id' => $request->user()->id,
                'book_id' => $request->book_id,
                'qty'     => $request->qty,
            ]);
        }

        return response()->json([
            'message' => 'Berhasil ditambahkan ke keranjang',
            'data'    => [
                'id'         => $cart->id,
                'book_id'    => $cart->book_id,
                'nama_buku'  => $book->nama_buku,
                'harga_jual' => $book->harga_jual,
                'gambar'     => $book->gambar,
                'stok'       => $book->stok,
                'qty'        => $cart->qty,
            ]
        ], 201);
    }

    /**
     * PUT /api/cart/{cart} — Update qty item keranjang
     * Body: { qty }
     */
    public function update(Request $request, Cart $cart): JsonResponse
    {
        // Pastikan hanya pemilik keranjang yang bisa update
        if ($cart->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'qty' => 'required|integer|min:1',
        ]);

        $book = $cart->book;
        if ($request->qty > $book->stok) {
            return response()->json([
                'message' => "Stok buku tidak mencukupi. Maksimal: {$book->stok}"
            ], 422);
        }

        $cart->update(['qty' => $request->qty]);

        return response()->json(['message' => 'Qty berhasil diupdate', 'data' => $cart]);
    }

    /**
     * DELETE /api/cart/{cart} — Hapus satu item keranjang
     */
    public function destroy(Request $request, Cart $cart): JsonResponse
    {
        if ($cart->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $cart->delete();

        return response()->json(['message' => 'Item dihapus dari keranjang']);
    }

    /**
     * DELETE /api/cart — Kosongkan semua keranjang user
     */
    public function clear(Request $request): JsonResponse
    {
        Cart::where('user_id', $request->user()->id)->delete();

        return response()->json(['message' => 'Keranjang dikosongkan']);
    }
}
