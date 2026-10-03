<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ZonaLayanan;

/**
 * Seeder: ZonaLayananSeeder
 *
 * Mengisi tabel zona_layanan dengan 4 zona operasional resmi
 * Dinas Pemadam Kebakaran dan Penyelamatan Kabupaten Purwakarta.
 *
 * Data ini bersifat STATIS (tidak akan berubah kecuali ada restrukturisasi organisasi).
 * Oleh karena itu diisi langsung di seeder, bukan dari CSV.
 */
class ZonaLayananSeeder extends Seeder
{
    public function run(): void
    {
        // Gunakan upsert agar seeder aman dijalankan berulang kali (idempotent)
        // updateOrInsert: jika kode_zona sudah ada, update — jika belum, insert baru
        $zonas = [
            ['kode_zona' => 'PUSAT', 'nama_zona' => 'WMK Pusat'],
            ['kode_zona' => 'UPTD1', 'nama_zona' => 'UPTD 1 — Plered'],
            ['kode_zona' => 'UPTD2', 'nama_zona' => 'UPTD 2 — Wanayasa'],
            ['kode_zona' => 'UPTD3', 'nama_zona' => 'UPTD 3 — Cikopo'],
        ];

        foreach ($zonas as $zona) {
            ZonaLayanan::updateOrCreate(
                ['kode_zona' => $zona['kode_zona']], // kondisi pencarian
                ['nama_zona' => $zona['nama_zona']]   // nilai yang di-insert/update
            );
        }

        $this->command->info('ZonaLayanan: ' . count($zonas) . ' zona berhasil di-seed.');
    }
}