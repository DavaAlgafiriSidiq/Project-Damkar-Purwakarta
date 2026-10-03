<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Membuat tabel stat_kebakaran_per_kecamatan
 *
 * Tabel ini menyimpan data HISTORIS agregasi kebakaran berdasarkan
 * kecamatan lokasi kejadian, per bulan.
 *
 * Sumber data awal: kebakaran_kecamatan.csv
 * Kolom bulan di CSV menggunakan singkatan (JAN, FEB, MAR, dst.)
 * yang akan dinormalisasi ke nama lengkap saat seeding.
 *
 * Kegunaan: men-supply chart "Sebaran Kebakaran per Kecamatan"
 * dan peta choropleth di dashboard.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stat_kebakaran_per_kecamatan', function (Blueprint $table) {
            $table->id();

            // Tahun data
            $table->unsignedSmallInteger('tahun')->default(2024);

            // Nama bulan, disimpan dalam format singkatan 3 huruf (JAN, FEB, dst.)
            // sesuai format asli di CSV sumber
            $table->string('bulan', 10);

            // Nama kecamatan, termasuk "Luar Kabupaten" untuk kejadian di luar wilayah
            $table->string('kecamatan');

            // Total jumlah kejadian kebakaran di kecamatan tersebut pada bulan itu
            $table->unsignedInteger('jumlah')->default(0);

            $table->timestamps();

            // Unique: satu baris per kombinasi tahun+bulan+kecamatan
            $table->unique(['tahun', 'bulan', 'kecamatan'], 'unique_kecamatan_per_bulan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stat_kebakaran_per_kecamatan');
    }
};