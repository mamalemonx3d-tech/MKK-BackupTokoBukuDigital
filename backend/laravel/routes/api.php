<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - BookStore
|--------------------------------------------------------------------------
*/

// Public Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout']);

// Lupa Password 6-Digit OTP Email
Route::post('/forgot-password/send-code', [AuthController::class, 'sendResetCode']);
Route::post('/forgot-password/verify-code', [AuthController::class, 'verifyResetCode']);
Route::post('/forgot-password/reset', [AuthController::class, 'resetPassword']);

// Public Catalog & Categories (Read-only)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{book}', [BookController::class, 'show']);

// Public Reviews (Read-only)
Route::get('/books/{book}/reviews', [ReviewController::class, 'indexByBook']);

// Invoice & Report PDF Download Routes (Accessible via Direct Link)
Route::get('/orders/{order}/invoice-pdf', [OrderController::class, 'invoicePdf']);
Route::get('/admin/reports/export-pdf', [ReportController::class, 'exportPdf']);
Route::get('/admin/reports/export-excel', [ReportController::class, 'exportExcel']);

// Authenticated Routes (Sanctum SPA & Bearer Token)
Route::middleware(['auth:sanctum'])->group(function () {

    // Current logged in user profile
    Route::get('/me', [AuthController::class, 'me']);

    // User & Admin Order Routes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/orders/scan', [OrderController::class, 'scan']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);

    // Cart Routes (Keranjang Belanja — persisted ke DB)
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{cart}', [CartController::class, 'update']);
    Route::delete('/cart/clear', [CartController::class, 'clear']);
    Route::delete('/cart/{cart}', [CartController::class, 'destroy']);

    // Live Chat (HTTP Polling)
    Route::get('/admins', [ChatController::class, 'getAdmins']);
    Route::get('/chats', [ChatController::class, 'index']);
    Route::get('/chats/unread-count', [ChatController::class, 'getUnreadCount']);
    Route::post('/chats/mark-as-read', [ChatController::class, 'markAsRead']);
    Route::get('/chats/user/{userId}', [ChatController::class, 'getConversation']);
    Route::post('/chats', [ChatController::class, 'store']);
    Route::post('/chats/delete-for-me', [ChatController::class, 'deleteForMe']);
    Route::post('/chats/delete-for-all', [ChatController::class, 'deleteForAll']);
    Route::post('/chats/delete-batch', [ChatController::class, 'destroyBatch']);
    Route::delete('/chats/{id}', [ChatController::class, 'destroy']);

    // Review Routes (Authenticated users)
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{review}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);

    // Admin Only Routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        // Admin CRUD Categories
        Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);

        // Admin CRUD Books
        Route::apiResource('books', BookController::class)->except(['index', 'show']);

        // Admin User Management
        Route::apiResource('users', UserController::class);

        // Admin Order Confirm & Pay (Kasir)
        Route::put('/orders/{order}/confirm', [OrderController::class, 'confirm']);
        Route::put('/orders/{order}/pay', [OrderController::class, 'pay']);

        // Admin Reports
        Route::get('/reports', [ReportController::class, 'index']);
    });
});
