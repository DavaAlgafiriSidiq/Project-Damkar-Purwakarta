<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('stat_kebakaran_per_objek');
        Schema::dropIfExists('stat_kebakaran_per_penyebab');
        Schema::dropIfExists('stat_kebakaran_per_kecamatan');
        Schema::dropIfExists('stat_rescue_per_objek');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tabel stat_* adalah tabel legacy yang telah digantikan oleh kejadian_kebakaran
        // Tidak perlu direstorasi pada saat rollback
    }
};
