<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * chat_deletions — tracks "hapus untuk saya" (per-user soft hide).
     * When a user deletes a message for themselves, we insert a row here.
     * The message still exists in the DB for the other party.
     */
    public function up(): void
    {
        Schema::create('chat_deletions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained('chats')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Each user can hide each message at most once
            $table->unique(['chat_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_deletions');
    }
};
