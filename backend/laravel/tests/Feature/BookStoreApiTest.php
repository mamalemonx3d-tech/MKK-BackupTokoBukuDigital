<?php

namespace Tests\Feature;

use App\Mail\ResetPasswordCodeMail;
use App\Models\Category;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BookStoreApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_login(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'token', 'user']);

        $this->assertDatabaseHas('users', [
            'username' => 'testuser',
            'email' => 'test@example.com',
        ]);
    }

    public function test_user_can_request_6digit_otp_and_reset_password(): void
    {
        Mail::fake();

        $user = User::create([
            'name' => 'Budi Lupa',
            'username' => 'budilupa',
            'email' => 'budi@example.com',
            'password' => Hash::make('password_lama'),
            'role' => 'user',
        ]);

        // Step 1: Send OTP Code
        $response = $this->postJson('/api/forgot-password/send-code', [
            'email' => 'budi@example.com',
        ]);

        $response->assertStatus(200);

        Mail::assertSent(ResetPasswordCodeMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });

        $record = DB::table('password_reset_tokens')->where('email', 'budi@example.com')->first();
        $this->assertNotNull($record);
        $code = $record->token;

        // Step 2: Verify OTP Code
        $verifyResponse = $this->postJson('/api/forgot-password/verify-code', [
            'email' => 'budi@example.com',
            'code' => $code,
        ]);

        $verifyResponse->assertStatus(200);

        // Step 3: Reset Password
        $resetResponse = $this->postJson('/api/forgot-password/reset', [
            'email' => 'budi@example.com',
            'code' => $code,
            'password' => 'password_baru123',
            'password_confirmation' => 'password_baru123',
        ]);

        $resetResponse->assertStatus(200);

        $this->assertTrue(Hash::check('password_baru123', $user->fresh()->password));
    }

    public function test_admin_can_create_category(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'username' => 'admintest',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)
                         ->postJson('/api/admin/categories', [
                             'nama_kategori' => 'Komik Fiksi',
                         ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('categories', ['nama_kategori' => 'Komik Fiksi']);
    }

    public function test_admin_can_create_book_and_calculate_profit(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'username' => 'admintest2',
            'email' => 'admin2@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $category = Category::create(['nama_kategori' => 'Sains']);

        $response = $this->actingAs($admin)
                         ->postJson('/api/admin/books', [
                             'nama_buku' => 'Buku Sains Modern',
                             'category_id' => $category->id,
                             'tanggal_terbit' => '2026-01-01',
                             'stok' => 10,
                             'harga_modal' => 50000,
                             'harga_jual' => 80000,
                             'deskripsi' => 'Deskripsi sains',
                         ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('books', [
            'nama_buku' => 'Buku Sains Modern',
            'keuntungan' => 30000,
        ]);
    }

    public function test_order_creation_and_cashier_flow(): void
    {
        $user = User::create([
            'name' => 'Buyer',
            'username' => 'buyer1',
            'email' => 'buyer1@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        $admin = User::create([
            'name' => 'Admin Kasir',
            'username' => 'adminkasir',
            'email' => 'adminkasir@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $category = Category::create(['nama_kategori' => 'Novel']);
        $book = Book::create([
            'nama_buku' => 'Novel Laskar',
            'category_id' => $category->id,
            'tanggal_terbit' => '2025-05-10',
            'stok' => 5,
            'harga_modal' => 40000,
            'harga_jual' => 60000,
            'keuntungan' => 20000,
        ]);

        // User checkout
        $response = $this->actingAs($user)
                         ->postJson('/api/orders', [
                             'items' => [
                                 ['book_id' => $book->id, 'qty' => 2]
                             ]
                         ]);

        $response->assertStatus(201);
        $orderData = $response->json('data');
        $orderId = $orderData['id'];

        $this->assertEquals('A001', $orderData['kode_pesanan']);
        $this->assertEquals('pending', $orderData['status']);
        $this->assertEquals(3, $book->fresh()->stok);

        // Admin confirms order
        $confirmResponse = $this->actingAs($admin)
                                ->putJson("/api/admin/orders/{$orderId}/confirm");

        $confirmResponse->assertStatus(200)
                        ->assertJsonPath('data.status', 'confirmed');

        // Admin cashier processes cash payment
        $payResponse = $this->actingAs($admin)
                            ->putJson("/api/admin/orders/{$orderId}/pay", [
                                'cash' => 150000,
                            ]);

        $payResponse->assertStatus(200)
                    ->assertJsonPath('data.status', 'completed')
                    ->assertJsonPath('data.cash', 150000)
                    ->assertJsonPath('data.kembalian', 30000);
    }
}
