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
            $table->integer('korban_meninggal')->default(0)->nullable()->after('taksiran_terselamatkan');
            $table->integer('korban_luka_berat')->default(0)->nullable()->after('korban_meninggal');
            $table->integer('korban_luka_ringan')->default(0)->nullable()->after('korban_luka_berat');
            $table->integer('kk_terdampak')->default(0)->nullable()->after('korban_luka_ringan');
            $table->integer('jiwa_terdampak')->default(0)->nullable()->after('kk_terdampak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kejadian_kebakaran', function (Blueprint $table) {
            $table->dropColumn([
                'korban_meninggal',
                'korban_luka_berat',
                'korban_luka_ringan',
                'kk_terdampak',
                'jiwa_terdampak',
            ]);
        });
    }
};
