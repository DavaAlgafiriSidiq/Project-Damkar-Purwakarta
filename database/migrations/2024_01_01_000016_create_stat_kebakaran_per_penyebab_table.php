<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Membuat tabel stat_kebakaran_per_penyebab
 *
 * Tabel ini menyimpan data HISTORIS agregasi kebakaran berdasarkan
 * dugaan penyebab, per bulan.
 *
 * Sumber data awal: kebakaran_penyebab.csv
 * Struktur: (bulan, dugaan_penyebab) → jumlah kejadian
 *
 * Kegunaan: men-supply chart "Distribusi Penyebab Kebakaran" di dashboard.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stat_kebakaran_per_penyebab', function (Blueprint $table) {
            $table->id();

            // Tahun data
            $table->unsignedSmallInteger('tahun')->default(2024);

            // Nama bulan dalam huruf kapital, konsisten dengan tabel stat lainnya
            $table->string('bulan', 20);

            // Dugaan penyebab kebakaran, sesuai data CSV
            // Contoh: "Arus Pendek Listrik", "Pembakaran Sampah/Lahan"
            $table->string('dugaan_penyebab');

            // Total jumlah kejadian untuk kombinasi bulan + penyebab ini
            $table->unsignedInteger('jumlah')->default(0);

            $table->timestamps();

            // Unique constraint: satu baris per kombinasi tahun+bulan+penyebab
            $table->unique(['tahun', 'bulan', 'dugaan_penyebab'], 'unique_penyebab_per_bulan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stat_kebakaran_per_penyebab');
    }
};