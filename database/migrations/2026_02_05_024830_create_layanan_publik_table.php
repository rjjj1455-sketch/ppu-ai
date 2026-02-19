<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('layanan_publik', function (Blueprint $table) {
            $table->id('nomor'); // Primary key
            $table->text('keluhan'); // Pertanyaan user
            $table->text('solusi'); // Jawaban AI
            $table->string('dinas')->nullable(); // Nama dinas terkait
            $table->text('link'); // URL/referensi link
            $table->string('instagram')->nullable(); // Instagram dinas
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layanan_publik');
    }
};
