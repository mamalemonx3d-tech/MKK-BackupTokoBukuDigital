<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Get all reviews for a specific book
     */
    public function indexByBook(Book $book): JsonResponse
    {
        $reviews = $book->reviews()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => ReviewResource::collection($reviews),
        ]);
    }

    /**
     * Store a new review
     */
    public function store(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:1000',
        ]);

        // Check if user already reviewed this book
        $existing = Review::where('book_id', $validated['book_id'])
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Anda sudah memberi ulasan untuk buku ini.',
            ], 422);
        }

        // Check if user has bought this book (optional but recommended)
        // For now, we allow anyone to review

        $review = Review::create([
            'book_id' => $validated['book_id'],
            'user_id' => Auth::id(),
            'rating' => $validated['rating'],
            'komentar' => $validated['komentar'] ?? null,
        ]);

        return response()->json([
            'message' => 'Ulasan berhasil ditambahkan',
            'data' => new ReviewResource($review->load('user')),
        ], 201);
    }

    /**
     * Update a review
     */
    public function update(Request $request, Review $review): JsonResponse
    {
        if (!Auth::check() || Auth::id() !== $review->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:1000',
        ]);

        $review->update($validated);

        return response()->json([
            'message' => 'Ulasan berhasil diperbarui',
            'data' => new ReviewResource($review->fresh('user')),
        ]);
    }

    /**
     * Delete a review
     */
    public function destroy(Review $review): JsonResponse
    {
        if (!Auth::check() || Auth::id() !== $review->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $review->delete();

        return response()->json(['message' => 'Ulasan berhasil dihapus']);
    }
}
