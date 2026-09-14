<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Book::with('category');

        // Search by title
        if ($request->filled('search')) {
            $query->where('nama_buku', 'like', '%' . $request->search . '%');
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $books = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'data' => BookResource::collection($books),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_buku' => 'required|string|max:255|unique:books,nama_buku',
            'category_id' => 'required|exists:categories,id',
            'tanggal_terbit' => 'required|date',
            'stok' => 'required|integer|min:0',
            'harga_modal' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'nama_buku.unique' => 'Nama buku harus unik dan sudah pernah terdaftar.',
        ]);

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('books', 'public');
        }

        $keuntungan = (float) $validated['harga_jual'] - (float) $validated['harga_modal'];

        $book = Book::create([
            'nama_buku' => $validated['nama_buku'],
            'category_id' => $validated['category_id'],
            'tanggal_terbit' => $validated['tanggal_terbit'],
            'stok' => $validated['stok'],
            'harga_modal' => $validated['harga_modal'],
            'harga_jual' => $validated['harga_jual'],
            'keuntungan' => $keuntungan,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'gambar' => $gambarPath,
        ]);

        return response()->json([
            'message' => 'Buku berhasil ditambahkan',
            'data' => new BookResource($book->load('category')),
        ], 201);
    }

    public function show(Book $book): JsonResponse
    {
        return response()->json(['data' => new BookResource($book->load('category'))]);
    }

    public function update(Request $request, Book $book): JsonResponse
    {
        $validated = $request->validate([
            'nama_buku' => 'required|string|max:255|unique:books,nama_buku,' . $book->id,
            'category_id' => 'required|exists:categories,id',
            'tanggal_terbit' => 'required|date',
            'stok' => 'required|integer|min:0',
            'harga_modal' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'nama_buku.unique' => 'Nama buku sudah terdaftar.',
        ]);

        if ($request->hasFile('gambar')) {
            if ($book->gambar && Storage::disk('public')->exists($book->gambar)) {
                Storage::disk('public')->delete($book->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('books', 'public');
        }

        $validated['keuntungan'] = (float) $validated['harga_jual'] - (float) $validated['harga_modal'];

        $book->update($validated);

        return response()->json([
            'message' => 'Buku berhasil diperbarui',
            'data' => new BookResource($book->fresh('category')),
        ]);
    }

    public function destroy(Book $book): JsonResponse
    {
        if ($book->gambar && Storage::disk('public')->exists($book->gambar)) {
            Storage::disk('public')->delete($book->gambar);
        }

        $book->delete();

        return response()->json(['message' => 'Buku berhasil dihapus']);
    }
}
