<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Buat tabel audit_logs
 *
 * Tabel ini mencatat setiap perubahan data (created/updated/deleted) pada
 * model KejadianKebakaran secara otomatis melalui Eloquent Observer.
 *
 * Kolom:
 *   - user_id      : ID user yang melakukan aksi (nullable untuk konsistensi)
 *   - action       : Jenis aksi ('created', 'updated', 'deleted')
 *   - model_type   : Nama class model (e.g. 'App\Models\KejadianKebakaran')
 *   - model_id     : ID record yang diubah
 *   - old_values   : Snapshot data sebelum perubahan (JSON), null saat 'created'
 *   - new_values   : Snapshot data sesudah perubahan (JSON), null saat 'deleted'
 *   - created_at   : Timestamp kapan log ini dibuat (tidak perlu updated_at)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index()
                ->comment('User yang melakukan aksi; null jika sistem/seeder');
            $table->string('action', 20)
                ->comment('Jenis aksi: created, updated, deleted');
            $table->string('model_type')
                ->comment('FQCN model, e.g. App\Models\KejadianKebakaran');
            $table->unsignedBigInteger('model_id')
                ->comment('ID record yang diubah');
            $table->json('old_values')->nullable()
                ->comment('Snapshot kolom sebelum perubahan; null untuk created');
            $table->json('new_values')->nullable()
                ->comment('Snapshot kolom sesudah perubahan; null untuk deleted');
            $table->timestamp('created_at')->useCurrent();

            // Indeks gabungan untuk mempercepat query per-record
            $table->index(['model_type', 'model_id']);

            // Foreign key ke users (optional; pakai nullable agar log tidak terhapus
            // jika user dihapus)
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
