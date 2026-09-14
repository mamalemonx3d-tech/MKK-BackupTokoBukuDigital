<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator Toko Buku',
                'email' => 'admin@bookstore.com',
                'no_telp' => '081234567890',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Regular User
        User::firstOrCreate(
            ['username' => 'userdemo'],
            [
                'name' => 'Budi Pembeli',
                'email' => 'user@bookstore.com',
                'no_telp' => '089876543210',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );

        // Initial Categories
        $categories = [
            'Fiksi & Sastra',
            'Sains & Teknologi',
            'Pengembangan Diri',
            'Bisnis & Ekonomi',
            'Komik & Manga',
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['nama_kategori' => $cat]);
        }

        // Sample Books
        $fiksiCat = Category::where('nama_kategori', 'Fiksi & Sastra')->first();
        $techCat = Category::where('nama_kategori', 'Sains & Teknologi')->first();

        if ($fiksiCat) {
            Book::firstOrCreate(
                ['nama_buku' => 'Laskar Pelangi'],
                [
                    'category_id' => $fiksiCat->id,
                    'tanggal_terbit' => '2005-09-25',
                    'stok' => 25,
                    'harga_modal' => 50000,
                    'harga_jual' => 75000,
                    'keuntungan' => 25000,
                    'deskripsi' => 'Novel inspiratif tentang perjuangan sepuluh anak di Belitung.',
                    'gambar' => null,
                ]
            );
        }

        if ($techCat) {
            Book::firstOrCreate(
                ['nama_buku' => 'Mastering Vue 3 & Nuxt 3'],
                [
                    'category_id' => $techCat->id,
                    'tanggal_terbit' => '2024-01-15',
                    'stok' => 15,
                    'harga_modal' => 90000,
                    'harga_jual' => 135000,
                    'keuntungan' => 45000,
                    'deskripsi' => 'Panduan praktis membangun aplikasi modern fullstack.',
                    'gambar' => null,
                ]
            );
        }
    }
}
