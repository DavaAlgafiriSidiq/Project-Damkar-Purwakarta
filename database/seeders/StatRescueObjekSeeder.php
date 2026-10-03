<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder: StatRescueObjekSeeder
 *
 * Membaca file CSV rescue_objek.csv dan mengimport data historis
 * operasi penyelamatan (rescue) ke tabel stat_rescue_per_objek.
 *
 * Format CSV:
 *   jenis_objek, bulan, jumlah
 *   (bulan dalam singkatan 3 huruf: JAN, FEB, dst.)
 *
 * Catatan: CSV rescue menggunakan urutan kolom jenis_objek DULU,
 * berbeda dari CSV kebakaran yang bulan dulu — perhatikan saat parsing.
 */
class StatRescueObjekSeeder extends Seeder
{
    private string $csvPath = 'C:/xampp/htdocs/Pelengkap Projek Damkar/rescue_objek.csv';
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
            // Lewati baris header
            if (!$adaHeader) {
                $adaHeader = true;
                continue;
            }

            if (count($kolom) < 3) {
                continue;
            }

            // Urutan kolom di rescue_objek.csv: jenis_objek, bulan, jumlah
            [$jenisObjek, $bulan, $jumlah] = array_map('trim', $kolom);

            if (empty($jenisObjek) || empty($bulan)) {
                continue;
            }

            $rows[] = [
                'tahun'      => $this->tahunData,
                'bulan'      => strtoupper($bulan),
                'jenis_objek'=> $jenisObjek,
                'jumlah'     => (int) $jumlah,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        fclose($handle);

        if (!empty($rows)) {
            DB::table('stat_rescue_per_objek')->upsert(
                $rows,
                ['tahun', 'bulan', 'jenis_objek'],
                ['jumlah', 'updated_at']
            );
        }

        $this->command->info('StatRescueObjek: ' . count($rows) . ' baris CSV berhasil di-import.');
    }
}