<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Tambah kolom nama_pelapor
 *
 * Konteks bisnis (Satu Akun Bersama):
 * Satu akun petugas dipakai bergantian oleh seluruh anggota regu di pos
 * yang sama. Kolom ini memastikan akuntabilitas individual: siapa
 * anggota / Danru yang secara fisik menginput laporan ini.
 *
 * Nullable agar data historis sebelum kolom ini dibuat tidak error.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kejadian_kebakaran', function (Blueprint $table) {
            // Letakkan setelah 'dilaporkan_oleh' agar urutan kolom logis
            $table->string('nama_pelapor')->nullable()->after('dilaporkan_oleh')
                ->comment('Nama anggota / Danru yang secara fisik menginput laporan (akuntabilitas akun bersama)');
        });
    }

    public function down(): void
    {
        Schema::table('kejadian_kebakaran', function (Blueprint $table) {
            $table->dropColumn('nama_pelapor');
        });
    }
};
