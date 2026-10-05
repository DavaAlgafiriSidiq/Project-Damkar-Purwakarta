<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Membuat tabel hidrans
 *
 * Mengikuti pola yang sama dengan kejadian_kebakaran: setiap hidran
 * dikaitkan ke kecamatan (lewat kecamatan_id), sehingga otomatis ikut
 * ternaungi oleh zona_layanan lewat relasi kecamatan -> zona_layanan.
 * Ini yang memungkinkan nanti hidran difilter/dilaporkan per kecamatan
 * atau per zona (WMK Pusat, UPTD 1/2/3), selaras dengan dashboard kejadian.
 *
 * Kolom dicatat_oleh & diupdate_oleh berfungsi sebagai jejak audit,
 * sama seperti dilaporkan_oleh/diverifikasi_oleh di kejadian_kebakaran.
 * Hanya user dengan role 'admin' (lihat add_role_to_users_table) yang
 * boleh mengisi/mengubah data hidran — validasi ini dilakukan di
 * controller/policy, bukan di level migration.
 *
 * Dibuat setelah kecamatan dan users karena ada FK ke keduanya.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hidrans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->enum('jenis', ['plat_merah', 'perusahaan'])->default('plat_merah');
            $table->enum('status', ['optimal', 'rusak'])->default('optimal');
            $table->text('catatan')->nullable();
            $table->string('alamat')->nullable();
            $table->string('foto_url')->nullable();

            // FK ke kecamatan — nullable sementara untuk data lama yang belum di-geocode,
            // tapi sebaiknya selalu diisi untuk data baru.
            $table->foreignId('kecamatan_id')
                  ->nullable()
                  ->constrained('kecamatan')
                  ->nullOnDelete();

            // Jejak audit: siapa yang input & update terakhir (role admin saja)
            $table->foreignId('dicatat_oleh')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->foreignId('diupdate_oleh')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();

            $table->index('kecamatan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hidrans');
    }
};