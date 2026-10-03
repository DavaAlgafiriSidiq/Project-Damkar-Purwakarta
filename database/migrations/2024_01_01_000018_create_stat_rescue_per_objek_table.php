<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Membuat tabel stat_rescue_per_objek
 *
 * Tabel ini menyimpan data HISTORIS agregasi operasi penyelamatan (rescue)
 * berdasarkan jenis objek/kasus penyelamatan, per bulan.
 *
 * Sumber data awal: rescue_objek.csv
 * Jenis rescue mencakup: Sarang Tawon, Ular, Biawak, Kecelakaan, dll.
 *
 * Kegunaan: men-supply chart "Distribusi Jenis Rescue" di dashboard.
 * Rescue diperlakukan terpisah dari kebakaran karena memiliki
 * konteks operasional yang berbeda (non_darurat).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stat_rescue_per_objek', function (Blueprint $table) {
            $table->id();

            // Tahun data
            $table->unsignedSmallInteger('tahun')->default(2024);

            // Nama bulan dalam singkatan 3 huruf (JAN, FEB, dst.) sesuai CSV
            $table->string('bulan', 10);

            // Jenis objek/kasus rescue, contoh: "Sarang Tawon", "Ular", "Kecelakaan/Sakit/cincin"
            $table->string('jenis_objek');

            // Total jumlah operasi rescue untuk kombinasi bulan + jenis ini
            $table->unsignedInteger('jumlah')->default(0);

            $table->timestamps();

            // Unique: satu baris per kombinasi tahun+bulan+jenis_objek
            $table->unique(['tahun', 'bulan', 'jenis_objek'], 'unique_rescue_per_bulan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stat_rescue_per_objek');
    }
};