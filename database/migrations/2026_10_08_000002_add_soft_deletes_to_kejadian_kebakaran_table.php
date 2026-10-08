<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Tambah soft deletes (deleted_at) ke tabel kejadian_kebakaran
 *
 * Alasan: Data laporan kebakaran adalah dokumen dinas resmi yang tidak boleh
 * hilang permanen. Soft delete memastikan data tetap ada di database (bisa
 * di-restore jika salah hapus) namun tidak terlihat di query normal.
 *
 * Perilaku dengan SoftDeletes:
 *   - $model->delete()  → mengisi deleted_at, bukan hapus fisik
 *   - ::withTrashed()   → include data yang sudah di-soft-delete
 *   - $model->restore() → set deleted_at = null
 *   - ::onlyTrashed()   → ambil hanya yang di-soft-delete
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kejadian_kebakaran', function (Blueprint $table) {
            $table->softDeletes(); // Menambahkan kolom deleted_at (nullable timestamp)
        });
    }

    public function down(): void
    {
        Schema::table('kejadian_kebakaran', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
