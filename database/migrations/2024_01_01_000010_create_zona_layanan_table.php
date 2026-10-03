<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Membuat tabel zona_layanan
 * Tabel induk yang menyimpan 4 zona operasional Damkar Purwakarta:
 * WMK Pusat, UPTD 1-Plered, UPTD 2-Wanayasa, UPTD 3-Cikopo.
 * Harus dibuat PERTAMA karena tabel kecamatan memiliki FK ke sini.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zona_layanan', function (Blueprint $table) {
            $table->id();
            // Kode singkat zona, contoh: PUSAT, UPTD1, UPTD2, UPTD3
            $table->string('kode_zona', 10)->unique();
            // Nama lengkap zona, contoh: "WMK Pusat", "UPTD 1 - Plered"
            $table->string('nama_zona');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zona_layanan');
    }
};
