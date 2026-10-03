<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\StatKebakaranPerObjek;

/**
 * Seeder: StatKebakaranObjekSeeder
 *
 * Membaca file CSV kebakaran_objek.csv dan mengimport seluruh data
 * historis kebakaran berdasarkan jenis objek ke tabel stat_kebakaran_per_objek.
 *
 * Format CSV:
 *   bulan, jenis_objek, jumlah
 *   (bulan dalam huruf kapital, contoh: JANUARI, FEBRUARI, dst.)
 *
 * Strategi import: upsert berdasarkan (tahun, bulan, jenis_objek)
 * agar seeder aman dijalankan berkali-kali tanpa duplikasi data.
 */
class StatKebakaranObjekSeeder extends Seeder
{
    /**
     * Path absolut ke file CSV sumber data.
     * Sesuaikan path ini jika lokasi file CSV berbeda.
     */
    private string $csvPath = 'C:/xampp/htdocs/Pelengkap Projek Damkar/kebakaran_objek.csv';

    /**
     * Tahun data yang direpresentasikan oleh CSV ini.
     * Karena CSV tidak menyertakan kolom tahun, kita set default.
     */
    private int $tahunData = 2024;

    public function run(): void
    {
        // Validasi: pastikan file CSV ada sebelum proses
        if (!file_exists($this->csvPath)) {
            $this->command->error("File CSV tidak ditemukan: {$this->csvPath}");
            return;
        }

        // Buka file CSV untuk dibaca
        $handle = fopen($this->csvPath, 'r');

        if ($handle === false) {
            $this->command->error("Gagal membuka file CSV: {$this->csvPath}");
            return;
        }

        $jumlahBaris    = 0;
        $jumlahBerhasil = 0;
        $adaHeader      = false; // flag untuk melompati baris pertama (header)

        // Kumpulkan data dalam array untuk bulk upsert (lebih efisien dari insert satu-satu)
        $rows = [];

        // Baca CSV baris per baris
        while (($kolom = fgetcsv($handle, 0, ',')) !== false) {
            // Lewati baris header (baris pertama dengan nama kolom)
            if (!$adaHeader) {
                $adaHeader = true;
                continue;
            }

            // Lewati baris kosong atau baris dengan kolom tidak lengkap
            if (count($kolom) < 3) {
                continue;
            }

            // Bersihkan whitespace dari setiap nilai kolom
            [$bulan, $jenisObjek, $jumlah] = array_map('trim', $kolom);

            // Lewati baris dengan nilai kosong
            if (empty($bulan) || empty($jenisObjek)) {
                continue;
            }

            // Kumpulkan ke array untuk bulk insert nanti
            $rows[] = [
                'tahun'      => $this->tahunData,
                'bulan'      => strtoupper($bulan),  // normalisasi ke uppercase
                'jenis_objek'=> $jenisObjek,
                'jumlah'     => (int) $jumlah,       // cast ke integer
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $jumlahBaris++;
        }

        fclose($handle);

        // Bulk upsert: insert semua data sekaligus, update jika sudah ada
        // Lebih efisien daripada insert/update satu per satu dalam loop
        if (!empty($rows)) {
            DB::table('stat_kebakaran_per_objek')->upsert(
                $rows,
                ['tahun', 'bulan', 'jenis_objek'], // kolom kunci untuk deteksi duplikat
                ['jumlah', 'updated_at']            // kolom yang di-update jika duplikat
            );
            $jumlahBerhasil = count($rows);
        }

        $this->command->info(
            "StatKebakaranObjek: {$jumlahBerhasil} dari {$jumlahBaris} baris CSV berhasil di-import."
        );
    }
}