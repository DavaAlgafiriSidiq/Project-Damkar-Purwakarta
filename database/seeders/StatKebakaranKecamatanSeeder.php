<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder: StatKebakaranKecamatanSeeder
 *
 * Membaca file CSV kebakaran_kecamatan.csv dan mengimport data historis
 * kebakaran berdasarkan kecamatan ke tabel stat_kebakaran_per_kecamatan.
 *
 * Format CSV:
 *   kecamatan, bulan, jumlah
 *   (bulan dalam singkatan 3 huruf: JAN, FEB, MAR, APR, MEI, JUN,
 *    JUL, AGS, SEP, OKT, NOV, DES)
 *
 * Catatan: "Luar Kabupaten" adalah entri valid di CSV untuk kejadian
 * yang terjadi di luar wilayah Kabupaten Purwakarta (mutual aid).
 */
class StatKebakaranKecamatanSeeder extends Seeder
{
    private string $csvPath = 'C:/xampp/htdocs/Pelengkap Projek Damkar/kebakaran_kecamatan.csv';
    private int $tahunData  = 2024;

    public function run(): void
    {
        if (!file_exists($this->csvPath)) {
            $this->command->error("File CSV tidak ditemukan: {$this->csvPath}");
            return;
        }

        $handle = fopen($this->csvPath, 'r');

        if ($handle === false) {
            $this->command->error("Gagal membuka file CSV: {$this->csvPath}");
            return;
        }

        $rows      = [];
        $adaHeader = false;

        while (($kolom = fgetcsv($handle, 0, ',')) !== false) {
            // Lewati baris header (urutan kolom di CSV: kecamatan, bulan, jumlah)
            if (!$adaHeader) {
                $adaHeader = true;
                continue;
            }

            if (count($kolom) < 3) {
                continue;
            }

            // Perhatikan urutan kolom di CSV ini: kecamatan DULU, baru bulan
            // (berbeda dengan CSV lain yang bulan dulu)
            [$kecamatan, $bulan, $jumlah] = array_map('trim', $kolom);

            if (empty($kecamatan) || empty($bulan)) {
                continue;
            }

            $rows[] = [
                'tahun'      => $this->tahunData,
                'bulan'      => strtoupper($bulan),   // normalisasi ke uppercase: JAN, FEB, dst.
                'kecamatan'  => $kecamatan,
                'jumlah'     => (int) $jumlah,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        fclose($handle);

        if (!empty($rows)) {
            DB::table('stat_kebakaran_per_kecamatan')->upsert(
                $rows,
                ['tahun', 'bulan', 'kecamatan'],
                ['jumlah', 'updated_at']
            );
        }

        $this->command->info('StatKebakaranKecamatan: ' . count($rows) . ' baris CSV berhasil di-import.');
    }
}