<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layanan_publik', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nomor')->nullable(); // nomor urut/referensi
            $table->text('keluhan');
            $table->text('solusi');
            $table->string('dinas', 255)->nullable();
            $table->string('link', 2048)->nullable();
            $table->string('instagram', 255)->nullable();
            $table->enum('validation_status', ['pending', 'valid', 'revisi', 'salah_mapping'])->default('pending');
            $table->text('validation_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layanan_publik');
    }
};