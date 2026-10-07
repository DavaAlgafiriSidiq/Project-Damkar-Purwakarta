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
        Schema::table('kejadian_kebakaran', function (Blueprint $table) {
            $table->enum('status_operasi', ['dalam_penanganan', 'selesai'])
                  ->default('dalam_penanganan')
                  ->after('status_verifikasi');
            $table->dateTime('tanggal_waktu_selesai')
                  ->nullable()
                  ->after('status_operasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kejadian_kebakaran', function (Blueprint $table) {
            $table->dropColumn(['status_operasi', 'tanggal_waktu_selesai']);
        });
    }
};
