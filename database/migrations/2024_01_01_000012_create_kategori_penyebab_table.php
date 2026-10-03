<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Membuat tabel kategori_penyebab
 * Menyimpan daftar dugaan penyebab kebakaran,
 * misalnya: Arus Pendek Listrik, Kompor Gas, Pembakaran Sampah/Lahan, dsb.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_penyebab', function (Blueprint $table) {
            $table->id();
            // Nama penyebab kebakaran sesuai kategori yang digunakan Damkar
            $table->string('nama_penyebab');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_penyebab');
    }
};
