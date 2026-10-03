<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\KejadianKebakaran;
use App\Models\Kecamatan;
use App\Models\KategoriObjek;
use App\Models\KategoriPenyebab;
use App\Models\User;
use Carbon\Carbon;

/**
 * Seeder: KejadianKebakaranSeeder
 *
 * ═══════════════════════════════════════════════════════════════════════
 * SINGLE SOURCE OF TRUTH — Transformasi Data CSV → Data Transaksional
 * ═══════════════════════════════════════════════════════════════════════
 *
 * STRATEGI AUDIT (Zero-Miss):
 *
 * Terdapat 4 file CSV historis Damkar, masing-masing menyimpan dimensi
 * agregasi yang berbeda (objek, penyebab, kecamatan, rescue):
 *
 *   kebakaran_objek.csv    → bulan | jenis_objek | jumlah
 *   kebakaran_penyebab.csv → bulan | dugaan_penyebab | jumlah
 *   kebakaran_kecamatan.csv→ kecamatan | bulan | jumlah
 *   rescue_objek.csv       → jenis_objek | bulan | jumlah  ← KOLOM BEDA!
 *
 * Karena keempat CSV adalah rekap dari set kejadian yang SAMA (namun dari
 * sudut pandang dimensi berbeda), kita TIDAK bisa naively membuat baris
 * dari semua CSV sekaligus — ini akan mengakibatkan jumlah baris berlipat.
 *
 * PENDEKATAN YANG BENAR:
 *   A. Data KEBAKARAN (darurat):
 *      - Gunakan kebakaran_kecamatan.csv sebagai SUMBER UTAMA (poros looping)
 *        karena mengandung kecamatan + bulan + jumlah secara tepat.
 *      - Untuk setiap baris kecamatan, buat N kejadian dengan:
 *          * kecamatan sesuai CSV (akurat 100%)
 *          * kategori_objek dipilih acak berbobot dari kebakaran_objek.csv
 *          * kategori_penyebab dipilih acak berbobot dari kebakaran_penyebab.csv
 *          * bulan sesuai CSV
 *
 *   B. Data RESCUE (non_darurat):
 *      - Gunakan rescue_objek.csv sebagai SUMBER UTAMA
 *      - Perhatian: format kolom rescue_objek.csv adalah: jenis_objek, bulan, jumlah
 *        (BERBEDA dengan kebakaran_objek.csv yang: bulan, jenis_objek, jumlah)
 *
 * AUDIT FIX LOG:
 *   1. [FATAL] Rescue seeder membaca kolom secara TERBALIK → diperbaiki
 *   2. [FATAL] Semua kategori rescue tidak ada di DB → KategoriObjekSeeder diperluas
 *   3. [MISS]  Kecamatan "Tegal Waru" di CSV ≠ "Tegalwaru" di DB → normalisasi ditambah
 *   4. [MISS]  "Luar Kabupaten" di CSV tidak ada di kecamatan → dilewati (bukan kecamatan resmi)
 */
class KejadianKebakaranSeeder extends Seeder
{
    public function run(): void
    {
        $admin   = User::where('role', 'admin')->first();
        $petugas = User::where('role', 'petugas')->first();

        // Preload semua master data ke memory untuk efisiensi lookup
        $kecamatans       = Kecamatan::all()->keyBy('nama_kecamatan');
        $kategoriObjek    = KategoriObjek::all()->keyBy('nama_kategori');
        $kategoriPenyebab = KategoriPenyebab::all()->keyBy('nama_penyebab');

        if ($kecamatans->isEmpty() || $kategoriObjek->isEmpty()) {
            $this->command->error('Data master belum lengkap.');
            return;
        }

        // Helper konversi nama bulan (Indonesia dan singkatan) ke angka
        $toBulan = function ($str) {
            $map = [
                'JANUARI' => 1,  'JAN' => 1,
                'FEBRUARI'=> 2,  'FEB' => 2,
                'MARET'   => 3,  'MAR' => 3,
                'APRIL'   => 4,  'APR' => 4,
                'MEI'     => 5,  'MEI' => 5,
                'JUNI'    => 6,  'JUN' => 6,
                'JULI'    => 7,  'JUL' => 7,
                'AGUSTUS' => 8,  'AGS' => 8,
                'SEPTEMBER'=>9,  'SEP' => 9,
                'OKTOBER' => 10, 'OKT' => 10,
                'NOVEMBER'=> 11, 'NOV' => 11,
                'DESEMBER'=> 12, 'DES' => 12,
            ];
            return $map[strtoupper(trim($str))] ?? null;
        };

        // Normalisasi nama kecamatan: menyamakan variasi penulisan antara CSV dan DB
        $normKecamatan = function ($nama) {
            $map = [
                'Tegal Waru'     => 'Tegalwaru',   // CSV pakai spasi, DB tidak
                'Babakancikao'   => 'Babakancikao',
                'Luar Kabupaten' => null,           // Bukan kecamatan resmi, lewati
            ];
            return array_key_exists($nama, $map) ? $map[$nama] : $nama;
        };

        // ─── Pra-hitung bobot distribusi dari CSV ─────────────────────────
        // Kita buat tabel frekuensi per-bulan untuk objek dan penyebab
        // agar saat men-generate data, distribusinya proporsional terhadap CSV asli.

        // Bobot objek per bulan: [bulanAngka => [namaObjek => jumlah, ...]]
        $bobotObjekPerBulan = [];
        $fileObjek = base_path('../Pelengkap Projek Damkar/kebakaran_objek.csv');
        if (file_exists($fileObjek)) {
            $h = fopen($fileObjek, 'r');
            fgetcsv($h); // skip header: bulan, jenis_objek, jumlah
            while (($row = fgetcsv($h)) !== false) {
                if (count($row) < 3) continue;
                $bulan = $toBulan($row[0]);
                if (!$bulan) continue;
                $bobotObjekPerBulan[$bulan][trim($row[1])] = (int)$row[2];
            }
            fclose($h);
        }

        // Bobot penyebab per bulan: [bulanAngka => [namaPenyebab => jumlah, ...]]
        $bobotPenyebabPerBulan = [];
        $filePenyebab = base_path('../Pelengkap Projek Damkar/kebakaran_penyebab.csv');
        if (file_exists($filePenyebab)) {
            $h = fopen($filePenyebab, 'r');
            fgetcsv($h); // skip header: bulan, dugaan_penyebab, jumlah
            while (($row = fgetcsv($h)) !== false) {
                if (count($row) < 3) continue;
                $bulan = $toBulan($row[0]);
                if (!$bulan) continue;
                $bobotPenyebabPerBulan[$bulan][trim($row[1])] = (int)$row[2];
            }
            fclose($h);
        }

        // Helper: pilih item secara acak berbobot dari array [nama => bobot]
        $pilihBerbobot = function (array $bobot) {
            $total = array_sum($bobot);
            if ($total <= 0) return array_key_first($bobot);
            $rand = mt_rand(1, $total);
            $kumulatif = 0;
            foreach ($bobot as $nama => $nilai) {
                $kumulatif += $nilai;
                if ($rand <= $kumulatif) return $nama;
            }
            return array_key_last($bobot);
        };

        // ═══════════════════════════════════════════════════════════════════
        // A. DATA KEBAKARAN (darurat)
        //
        // POROS: kebakaran_kecamatan.csv  (kecamatan, bulan, jumlah)
        // Setiap baris → N kejadian dengan kecamatan & bulan yang AKURAT.
        // Objek & Penyebab dipilih acak berbobot dari CSV masing-masing.
        // ═══════════════════════════════════════════════════════════════════
        $fileKecamatan = base_path('../Pelengkap Projek Damkar/kebakaran_kecamatan.csv');
        $jumlahKebakaran = 0;
        $skipKecamatan   = 0;

        if (file_exists($fileKecamatan)) {
            $h = fopen($fileKecamatan, 'r');
            fgetcsv($h); // skip header: kecamatan, bulan, jumlah
            while (($row = fgetcsv($h)) !== false) {
                if (count($row) < 3) continue;

                $namaKecamatanCsv = trim($row[0]);
                $namaKecamatan    = $normKecamatan($namaKecamatanCsv);
                $bulan            = $toBulan($row[1]);
                $jumlah           = (int)$row[2];

                // Lewati kecamatan yang di luar wilayah resmi atau tidak terpetakan
                if ($namaKecamatan === null || !$kecamatans->has($namaKecamatan)) {
                    $this->command->warn("  Skip kecamatan: '$namaKecamatanCsv' (tidak ada di DB)");
                    $skipKecamatan++;
                    continue;
                }

                $kecamatanId = $kecamatans[$namaKecamatan]->id;

                for ($i = 0; $i < $jumlah; $i++) {
                    // Pilih objek acak berbobot sesuai distribusi bulan itu
                    $namaObjek  = ($bobotObjekPerBulan[$bulan] ?? [])
                        ? $pilihBerbobot($bobotObjekPerBulan[$bulan])
                        : null;
                    $objekId = $namaObjek && $kategoriObjek->has($namaObjek)
                        ? $kategoriObjek[$namaObjek]->id
                        : null;

                    // Pilih penyebab acak berbobot sesuai distribusi bulan itu
                    $namaPenyebab = ($bobotPenyebabPerBulan[$bulan] ?? [])
                        ? $pilihBerbobot($bobotPenyebabPerBulan[$bulan])
                        : null;
                    $penyebabId = $namaPenyebab && $kategoriPenyebab->has($namaPenyebab)
                        ? $kategoriPenyebab[$namaPenyebab]->id
                        : null;

                    KejadianKebakaran::create([
                        'jenis_layanan'          => 'darurat',
                        'tanggal_waktu_kejadian' => Carbon::create(2026, $bulan, rand(1, 28), rand(0, 23), rand(0, 59), 0),
                        'kecamatan_id'           => $kecamatanId,
                        'kategori_objek_id'      => $objekId,
                        'kategori_penyebab_id'   => $penyebabId,
                        'taksiran_kerugian'      => rand(5, 500) * 1000000,
                        'status_verifikasi'      => 'verified',
                        'dilaporkan_oleh'        => $petugas->id ?? 1,
                        'diverifikasi_oleh'      => $admin->id ?? 1,
                        'diverifikasi_pada'      => now(),
                    ]);
                    $jumlahKebakaran++;
                }
            }
            fclose($h);
        }

        // ═══════════════════════════════════════════════════════════════════
        // B. DATA RESCUE (non_darurat)
        //
        // POROS: rescue_objek.csv
        // AUDIT FIX: Format kolom rescue CSV adalah: jenis_objek, bulan, jumlah
        //            (BERBEDA dari kebakaran_objek: bulan, jenis_objek, jumlah)
        //            Seeder lama membacanya terbalik — sudah diperbaiki di sini.
        // ═══════════════════════════════════════════════════════════════════
        $fileRescue = base_path('../Pelengkap Projek Damkar/rescue_objek.csv');
        $jumlahRescue = 0;

        if (file_exists($fileRescue)) {
            $h = fopen($fileRescue, 'r');
            fgetcsv($h); // skip header: jenis_objek, bulan, jumlah
            while (($row = fgetcsv($h)) !== false) {
                if (count($row) < 3) continue;

                // AUDIT FIX: urutan kolom rescue CSV berbeda!
                //   $row[0] = jenis_objek  (BUKAN bulan)
                //   $row[1] = bulan        (BUKAN jenis_objek)
                //   $row[2] = jumlah
                $namaObjek = trim($row[0]);
                $bulan     = $toBulan($row[1]);
                $jumlah    = (int)$row[2];

                if (!$bulan) continue;

                $objekId = $kategoriObjek->has($namaObjek)
                    ? $kategoriObjek[$namaObjek]->id
                    : null;

                // Distribusikan ke kecamatan secara acak merata
                // (rescue_objek.csv tidak menyimpan dimensi kecamatan)
                $kecamatanList = $kecamatans->values();

                for ($i = 0; $i < $jumlah; $i++) {
                    KejadianKebakaran::create([
                        'jenis_layanan'          => 'non_darurat',
                        'tanggal_waktu_kejadian' => Carbon::create(2026, $bulan, rand(1, 28), rand(0, 23), rand(0, 59), 0),
                        'kecamatan_id'           => $kecamatanList->random()->id,
                        'kategori_objek_id'      => $objekId,
                        'kategori_penyebab_id'   => null, // Rescue tidak memiliki penyebab kebakaran
                        'status_verifikasi'      => 'verified',
                        'dilaporkan_oleh'        => $petugas->id ?? 1,
                        'diverifikasi_oleh'      => $admin->id ?? 1,
                        'diverifikasi_pada'      => now(),
                    ]);
                    $jumlahRescue++;
                }
            }
            fclose($h);
        }

        $this->command->info("✓ Kebakaran: {$jumlahKebakaran} baris (skip kecamatan luar wilayah: {$skipKecamatan})");
        $this->command->info("✓ Rescue   : {$jumlahRescue} baris");
        $this->command->info("✓ TOTAL    : " . ($jumlahKebakaran + $jumlahRescue) . " baris transaksional terverifikasi.");
    }
}
