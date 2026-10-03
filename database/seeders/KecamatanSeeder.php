<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kecamatan;
use App\Models\ZonaLayanan;

/**
 * Seeder: KecamatanSeeder
 *
 * Mengisi tabel kecamatan dengan klasifikasi zona layanan resmi
 * berdasarkan dokumen operasional Damkar Purwakarta.
 *
 * Pemetaan zona (FINAL — sudah dikonfirmasi pihak Damkar):
 *   PUSAT → WMK Pusat (Kantor Dinas):         Pasawahan, Jatiluhur, Purwakarta, Babakancikao, Sukasari
 *   UPTD1 → WMK UPTD Wilayah 1 (Pos Plered):  Darangdan, Plered, Sukatani, Tegalwaru, Maniis
 *   UPTD2 → WMK UPTD Wilayah 2 (Pos Wanayasa):Bojong, Wanayasa, Kiarapedes, Pondoksalam
 *   UPTD3 → WMK UPTD Wilayah 3 (Pos Cikopo):  Cibatu, Bungursari, Campaka
 *
 * Total: 17 kecamatan resmi wilayah Kabupaten Purwakarta.
 */
class KecamatanSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil id zona berdasarkan kode_zona — hindari hardcode ID angka
        // karena ID bisa berbeda antar environment (lokal vs staging vs produksi)
        $zonas = ZonaLayanan::pluck('id', 'kode_zona');

        // Pemetaan kecamatan ke zona layanan (FINAL — dokumen resmi Damkar)
        // Format: 'nama_kecamatan' => 'KODE_ZONA'
        $kecamatans = [
            // ── WMK Pusat (Kantor Dinas) ──────────────────────────────
            'Pasawahan'     => 'PUSAT',
            'Jatiluhur'     => 'PUSAT',
            'Purwakarta'    => 'PUSAT',
            'Babakancikao'  => 'PUSAT',
            'Sukasari'      => 'PUSAT',

            // ── WMK UPTD Wilayah 1 (Pos Plered) ──────────────────────
            'Darangdan'     => 'UPTD1',
            'Plered'        => 'UPTD1',
            'Sukatani'      => 'UPTD1',
            'Tegalwaru'     => 'UPTD1',
            'Maniis'        => 'UPTD1',

            // ── WMK UPTD Wilayah 2 (Pos Wanayasa) ────────────────────
            'Bojong'        => 'UPTD2',
            'Wanayasa'      => 'UPTD2',
            'Kiarapedes'    => 'UPTD2',
            'Pondoksalam'   => 'UPTD2',

            // ── WMK UPTD Wilayah 3 (Pos Cikopo) ──────────────────────
            'Cibatu'        => 'UPTD3',
            'Bungursari'    => 'UPTD3',
            'Campaka'       => 'UPTD3',
        ];

        foreach ($kecamatans as $nama => $kodeZona) {
            if (!isset($zonas[$kodeZona])) {
                $this->command->warn("Zona '$kodeZona' tidak ditemukan, skip '$nama'.");
                continue;
            }

            Kecamatan::updateOrCreate(
                ['nama_kecamatan' => $nama],
                ['zona_layanan_id' => $zonas[$kodeZona]]
            );
        }

        $this->command->info('Kecamatan: ' . count($kecamatans) . ' kecamatan berhasil di-seed (pemetaan zona resmi).');
    }
}