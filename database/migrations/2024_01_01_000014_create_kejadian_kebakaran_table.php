<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Membuat tabel kejadian_kebakaran
 *
 * Ini adalah tabel INTI sistem — menyimpan setiap laporan kejadian
 * kebakaran maupun penyelamatan yang dicatat petugas Damkar.
 *
 * ATURAN BISNIS KRITIS (jangan dilanggar di query manapun):
 * Kolom status_verifikasi WAJIB selalu difilter dalam setiap query
 * yang menyuplai data ke dashboard publik.
 * Hanya data dengan status "verified" yang boleh tampil di dashboard.
 * Data "draft" adalah data mentah dari petugas lapangan yang
 * belum divalidasi admin/Humas — TIDAK BOLEH masuk ke kalkulasi publik.
 *
 * Urutan pembuatan: TERAKHIR — karena memiliki FK ke users, kecamatan,
 * kategori_objek, dan kategori_penyebab yang harus ada lebih dulu.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kejadian_kebakaran', function (Blueprint $table) {
            $table->id();

            // Jenis layanan: "darurat" = kebakaran, "non_darurat" = penyelamatan/rescue
            $table->enum('jenis_layanan', ['darurat', 'non_darurat']);

            // Waktu kejadian lengkap dengan jam (bukan hanya tanggal)
            $table->dateTime('tanggal_waktu_kejadian');

            // FK ke kecamatan — lokasi kejadian terjadi
            // restrictOnDelete: kecamatan tidak bisa dihapus jika masih ada kejadian
            $table->foreignId('kecamatan_id')
                  ->constrained('kecamatan')
                  ->restrictOnDelete();

            // FK ke kategori_objek — nullable karena mungkin belum terisi saat laporan awal
            $table->foreignId('kategori_objek_id')
                  ->nullable()
                  ->constrained('kategori_objek')
                  ->nullOnDelete();

            // FK ke kategori_penyebab — nullable (penyebab sering "Belum diketahui")
            $table->foreignId('kategori_penyebab_id')
                  ->nullable()
                  ->constrained('kategori_penyebab')
                  ->nullOnDelete();

            // Koordinat GPS opsional (untuk fitur peta sebaran di versi mendatang)
            // Presisi 8 desimal = akurasi ~1 meter
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Jumlah personel yang diterjunkan ke lokasi
            $table->unsignedInteger('jumlah_personel')->nullable();

            // Estimasi nilai kerugian materi akibat kebakaran (dalam rupiah)
            $table->decimal('taksiran_kerugian', 15, 2)->nullable();

            // Estimasi nilai aset yang berhasil diselamatkan (dalam rupiah)
            $table->decimal('taksiran_terselamatkan', 15, 2)->nullable();

            // Narasi deskripsi singkat kejadian dari petugas lapangan
            $table->text('deskripsi')->nullable();

            /**
             * STATUS VERIFIKASI — KOLOM PALING KRITIS DI SELURUH SISTEM
             *
             * "draft"    = data baru di-input petugas lapangan, belum divalidasi
             * "verified" = data sudah diverifikasi admin/Humas, BOLEH tampil di dashboard publik
             *
             * Gunakan scope scopeVerifiedOnly() di Model KejadianKebakaran
             * untuk memastikan filter ini selalu konsisten.
             * JANGAN PERNAH melewatkan filter ini pada query dashboard.
             */
            $table->enum('status_verifikasi', ['draft', 'verified'])->default('draft');

            // FK ke users — petugas lapangan yang menginput laporan kejadian
            $table->foreignId('dilaporkan_oleh')
                  ->constrained('users')
                  ->restrictOnDelete();

            // FK ke users — admin/Humas yang memverifikasi (null selama masih draft)
            $table->foreignId('diverifikasi_oleh')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Timestamp kapan verifikasi dilakukan (null jika masih draft)
            $table->timestamp('diverifikasi_pada')->nullable();

            $table->timestamps();

            // Composite index: query dashboard selalu filter status + urutkan tanggal
            $table->index(['status_verifikasi', 'tanggal_waktu_kejadian'], 'idx_status_tanggal');

            // Index tunggal: query peta sebaran filter per kecamatan
            $table->index('kecamatan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kejadian_kebakaran');
    }
};