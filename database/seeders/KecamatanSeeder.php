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

        // Pemetaan kecamatan ke zona layanan baku (aturan 8.10 di ai-context.md)
        $kecamatans = [
            // ── WMK Pusat (Kantor Dinas) ──────────────────────────────
            'Pasawahan'     => ['kode' => 'PUSAT', 'zona_layanan' => 'WMK Pusat'],
            'Jatiluhur'     => ['kode' => 'PUSAT', 'zona_layanan' => 'WMK Pusat'],
            'Purwakarta'    => ['kode' => 'PUSAT', 'zona_layanan' => 'WMK Pusat'],
            'Babakancikao'  => ['kode' => 'PUSAT', 'zona_layanan' => 'WMK Pusat'],
            'Sukasari'      => ['kode' => 'PUSAT', 'zona_layanan' => 'WMK Pusat'],

            // ── WMK UPTD Wilayah 1 (Pos Plered) ──────────────────────
            'Darangdan'     => ['kode' => 'UPTD1', 'zona_layanan' => 'WMK UPTD 1'],
            'Plered'        => ['kode' => 'UPTD1', 'zona_layanan' => 'WMK UPTD 1'],
            'Sukatani'      => ['kode' => 'UPTD1', 'zona_layanan' => 'WMK UPTD 1'],
            'Tegalwaru'     => ['kode' => 'UPTD1', 'zona_layanan' => 'WMK UPTD 1'],
            'Maniis'        => ['kode' => 'UPTD1', 'zona_layanan' => 'WMK UPTD 1'],

            // ── WMK UPTD Wilayah 2 (Pos Wanayasa) ────────────────────
            'Bojong'        => ['kode' => 'UPTD2', 'zona_layanan' => 'WMK UPTD 2'],
            'Wanayasa'      => ['kode' => 'UPTD2', 'zona_layanan' => 'WMK UPTD 2'],
            'Kiarapedes'    => ['kode' => 'UPTD2', 'zona_layanan' => 'WMK UPTD 2'],
            'Pondoksalam'   => ['kode' => 'UPTD2', 'zona_layanan' => 'WMK UPTD 2'],

            // ── WMK UPTD Wilayah 3 (Pos Cikopo) ──────────────────────
            'Cibatu'        => ['kode' => 'UPTD3', 'zona_layanan' => 'WMK UPTD 3'],
            'Bungursari'    => ['kode' => 'UPTD3', 'zona_layanan' => 'WMK UPTD 3'],
            'Campaka'       => ['kode' => 'UPTD3', 'zona_layanan' => 'WMK UPTD 3'],

            // ── Khusus: Di luar wilayah administratif Purwakarta ──────
            // Digunakan untuk kejadian lintas batas / mutual aid.
            'Luar Kabupaten' => ['kode' => 'PUSAT', 'zona_layanan' => 'Luar Daerah'],
        ];

        foreach ($kecamatans as $nama => $data) {
            $kodeZona = $data['kode'];
            $zonaLayanan = $data['zona_layanan'];

            if (!isset($zonas[$kodeZona])) {
                $this->command->warn("Zona '$kodeZona' tidak ditemukan, skip '$nama'.");
                continue;
            }

            Kecamatan::updateOrCreate(
                ['nama_kecamatan' => $nama],
                [
                    'zona_layanan_id' => $zonas[$kodeZona],
                    'zona_layanan'    => $zonaLayanan,
                ]
            );
        }

        $this->command->info('Kecamatan: ' . count($kecamatans) . ' kecamatan berhasil di-seed dengan zona_layanan baku.');
    }
}