<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Membuat tabel kategori_objek
 * Menyimpan daftar kategori objek yang terbakar,
 * misalnya: Rumah Tinggal, Kendaraan, Perkebunan/Hutan/Lahan, dsb.
 * Kolom is_karhutla digunakan untuk menandai kategori yang
 * termasuk klasifikasi Kebakaran Hutan dan Lahan (Karhutla)
 * sehingga bisa difilter terpisah di laporan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_objek', function (Blueprint $table) {
            $table->id();
            // Nama kategori objek yang terbakar atau target operasi penyelamatan
            $table->string('nama_kategori');
            // Jenis layanan terkait: 'kebakaran' (darurat) atau 'rescue' (non-darurat)
            $table->enum('jenis_layanan', ['kebakaran', 'rescue'])->default('kebakaran');
            // Penanda apakah kategori ini termasuk Karhutla (kebakaran hutan & lahan)
            $table->boolean('is_karhutla')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_objek');
    }
};
