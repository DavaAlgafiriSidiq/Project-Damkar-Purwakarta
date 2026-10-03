<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Menambahkan kolom role pada tabel users
 *
 * Kolom 'role' membedakan dua jenis pengguna sistem Damkar:
 *   - 'admin'   : Administrator/Humas Damkar — berwenang memverifikasi laporan,
 *                 mengelola data, dan mengakses dashboard manajemen.
 *   - 'petugas' : Petugas lapangan — hanya dapat menginput laporan kejadian baru
 *                 dan melihat riwayat laporan sendiri.
 *
 * Default 'petugas' dipilih karena lebih aman: jika role tidak diisi saat
 * pembuatan akun, pengguna tidak mendapat akses admin secara tidak sengaja.
 *
 * Catatan: Migration ini memeriksa keberadaan kolom sebelum membuat ulang
 * untuk menghindari error duplikasi kolom jika dijalankan berkali-kali.
 */
return new class extends Migration
{
    /**
     * Jalankan migration: tambahkan kolom role ke tabel users.
     */
    public function up(): void
    {
        // Hapus kolom lama terlebih dulu jika sudah ada (idempoten)
        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }

        // Buat kolom role dengan nilai yang diizinkan dan default 'petugas'
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'petugas'])->default('petugas')->after('email');
        });
    }

    /**
     * Batalkan migration: hapus kolom role dari tabel users.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
