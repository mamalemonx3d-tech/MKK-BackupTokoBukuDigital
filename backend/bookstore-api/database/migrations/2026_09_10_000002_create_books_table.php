<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('nama_buku')->unique();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->date('tanggal_terbit');
            $table->integer('stok')->default(0);
            $table->decimal('harga_modal', 12, 2);
            $table->decimal('harga_jual', 12, 2);
            $table->decimal('keuntungan', 12, 2);
            $table->text('deskripsi')->nullable();
            $table->string('gambar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
