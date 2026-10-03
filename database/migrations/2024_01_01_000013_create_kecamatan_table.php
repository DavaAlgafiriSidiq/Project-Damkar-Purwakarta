<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Membuat tabel kecamatan
 * Menyimpan 17 kecamatan di Kabupaten Purwakarta beserta
 * asosiasi ke zona layanan (FK ke zona_layanan).
 * Dibuat SETELAH zona_layanan karena FK dependency.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kecamatan', function (Blueprint $table) {
            $table->id();
            // FK ke zona_layanan — menentukan kecamatan ini dilayani zona mana
            // onDelete restrict: zona tidak boleh dihapus jika masih ada kecamatan
            $table->foreignId('zona_layanan_id')
                  ->constrained('zona_layanan')
                  ->restrictOnDelete();
            // Nama kecamatan, contoh: Purwakarta, Plered, Wanayasa, Cibatu
            $table->string('nama_kecamatan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Hapus kecamatan dulu sebelum zona_layanan (urutan kebalikan dari up)
        Schema::dropIfExists('kecamatan');
    }
};
