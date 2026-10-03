<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder: StatKebakaranPenyebabSeeder
 *
 * Membaca file CSV kebakaran_penyebab.csv dan mengimport data historis
 * kebakaran berdasarkan dugaan penyebab ke tabel stat_kebakaran_per_penyebab.
 *
 * Format CSV:
 *   bulan, dugaan_penyebab, jumlah
 */
class StatKebakaranPenyebabSeeder extends Seeder
{
    private string $csvPath = 'C:/xampp/htdocs/Pelengkap Projek Damkar/kebakaran_penyebab.csv';
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

            [$bulan, $dugaanPenyebab, $jumlah] = array_map('trim', $kolom);

            if (empty($bulan) || empty($dugaanPenyebab)) {
                continue;
            }

            $rows[] = [
                'tahun'          => $this->tahunData,
                'bulan'          => strtoupper($bulan),
                'dugaan_penyebab'=> $dugaanPenyebab,
                'jumlah'         => (int) $jumlah,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }

        fclose($handle);

        if (!empty($rows)) {
            // Upsert: key unik (tahun+bulan+dugaan_penyebab), update jumlah jika duplikat
            DB::table('stat_kebakaran_per_penyebab')->upsert(
                $rows,
                ['tahun', 'bulan', 'dugaan_penyebab'],
                ['jumlah', 'updated_at']
            );
        }

        $this->command->info('StatKebakaranPenyebab: ' . count($rows) . ' baris CSV berhasil di-import.');
    }
}