<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriObjek;

/**
 * Seeder: KategoriObjekSeeder
 *
 * AUDIT FIX: Daftar kategori diperluas untuk mencakup SEMUA kategori objek
 * yang muncul di KEDUA file CSV:
 *   1. kebakaran_objek.csv  → objek yang terbakar
 *   2. rescue_objek.csv     → jenis operasi penyelamatan
 *
 * Sebelumnya hanya memuat kategori kebakaran saja, sehingga seluruh data
 * rescue kehilangan relasi kategori_objek_id (selalu NULL).
 */
class KategoriObjekSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Kategori Kebakaran (dari kebakaran_objek.csv) ────────────────
        $kategorisKebakaran = [
            ['nama_kategori' => 'Kendaraan',                    'is_karhutla' => false, 'jenis' => 'kebakaran'],
            ['nama_kategori' => 'Rumah Tinggal',                'is_karhutla' => false, 'jenis' => 'kebakaran'],
            ['nama_kategori' => 'Pasar/Pertokoan/Ruko',         'is_karhutla' => false, 'jenis' => 'kebakaran'],
            ['nama_kategori' => 'Perkantoran/Sekolah/RS',       'is_karhutla' => false, 'jenis' => 'kebakaran'],
            ['nama_kategori' => 'Perindustrian',                'is_karhutla' => false, 'jenis' => 'kebakaran'],
            ['nama_kategori' => 'Perkebunan/Hutan/Lahan',       'is_karhutla' => true,  'jenis' => 'kebakaran'], // ← Karhutla
            ['nama_kategori' => 'Tower Cell/GI Listrik/Genset', 'is_karhutla' => false, 'jenis' => 'kebakaran'],
            ['nama_kategori' => 'Peternakan',                   'is_karhutla' => false, 'jenis' => 'kebakaran'],
            ['nama_kategori' => 'Lain-lain',                    'is_karhutla' => false, 'jenis' => 'kebakaran'],
        ];

        // ─── Kategori Rescue (dari rescue_objek.csv) ──────────────────────
        // AUDIT FIX: Kategori ini sebelumnya TIDAK ADA di database, menyebabkan
        // semua data rescue kehilangan referensi kategori_objek_id.
        $kategorisRescue = [
            ['nama_kategori' => 'Sarang Tawon',                                         'is_karhutla' => false, 'jenis' => 'rescue'],
            ['nama_kategori' => 'Ular',                                                 'is_karhutla' => false, 'jenis' => 'rescue'],
            ['nama_kategori' => 'Monyet/lutung',                                        'is_karhutla' => false, 'jenis' => 'rescue'],
            ['nama_kategori' => 'Biawak',                                               'is_karhutla' => false, 'jenis' => 'rescue'],
            ['nama_kategori' => 'Kucing',                                               'is_karhutla' => false, 'jenis' => 'rescue'],
            ['nama_kategori' => 'Binatang lainnya (musang, anjing, buaya, tokek, dll)', 'is_karhutla' => false, 'jenis' => 'rescue'],
            ['nama_kategori' => 'Tercebur Sumur',                                       'is_karhutla' => false, 'jenis' => 'rescue'],
            ['nama_kategori' => 'Orang Hilang',                                         'is_karhutla' => false, 'jenis' => 'rescue'],
            ['nama_kategori' => 'Orang Tenggelam',                                      'is_karhutla' => false, 'jenis' => 'rescue'],
            ['nama_kategori' => 'Pohon Tumbang',                                        'is_karhutla' => false, 'jenis' => 'rescue'],
            ['nama_kategori' => 'Angin Puting Beliung',                                 'is_karhutla' => false, 'jenis' => 'rescue'],
            ['nama_kategori' => 'Kebakaran',                                            'is_karhutla' => false, 'jenis' => 'rescue'], // Kebakaran yang ditangani sbg rescue
            ['nama_kategori' => 'Kecelakaan/Sakit/cincin',                             'is_karhutla' => false, 'jenis' => 'rescue'],
        ];

        $semua = array_merge($kategorisKebakaran, $kategorisRescue);

        foreach ($semua as $kategori) {
            KategoriObjek::updateOrCreate(
                ['nama_kategori' => $kategori['nama_kategori']],
                [
                    'jenis_layanan' => $kategori['jenis'],
                    'is_karhutla'   => $kategori['is_karhutla'],
                ]
            );
        }

        $this->command->info('KategoriObjek: ' . count($semua) . ' kategori berhasil di-seed (kebakaran + rescue).');
    }
}