<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Membuat tabel stat_kebakaran_per_objek
 *
 * Tabel ini menyimpan data HISTORIS agregasi kebakaran berdasarkan
 * jenis objek yang terbakar, per bulan.
 *
 * Sumber data awal: kebakaran_objek.csv (hasil unpivot dari Excel Damkar)
 * Struktur: (bulan, jenis_objek) → jumlah kejadian
 *
 * Kegunaan: men-supply chart distribusi objek kebakaran di dashboard
 * sebelum sistem real-time memiliki data yang cukup.
 * Kolom tahun ditambahkan untuk keperluan filter multi-tahun ke depan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stat_kebakaran_per_objek', function (Blueprint $table) {
            $table->id();

            // Tahun data (default 2024 = tahun data CSV yang ada)
            $table->unsignedSmallInteger('tahun')->default(2024);

            // Nama bulan dalam huruf kapital, contoh: JANUARI, FEBRUARI, dst.
            // Menggunakan string bukan integer agar konsisten dengan sumber CSV
            $table->string('bulan', 20);

            // Nama kategori jenis objek yang terbakar, sesuai data CSV
            $table->string('jenis_objek');

            // Total jumlah kejadian kebakaran untuk kombinasi bulan + jenis_objek
            $table->unsignedInteger('jumlah')->default(0);

            $table->timestamps();

            // Composite unique: satu baris per kombinasi tahun+bulan+jenis_objek
            $table->unique(['tahun', 'bulan', 'jenis_objek'], 'unique_objek_per_bulan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stat_kebakaran_per_objek');
    }
};