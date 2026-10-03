<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriPenyebab;

/**
 * Seeder: KategoriPenyebabSeeder
 *
 * Mengisi tabel kategori_penyebab dengan daftar dugaan penyebab kebakaran
 * yang digunakan Damkar Purwakarta, sesuai kolom dugaan_penyebab di CSV.
 */
class KategoriPenyebabSeeder extends Seeder
{
    public function run(): void
    {
        $penyebabs = [
            'Arus Pendek Listrik',
            'Arus Pendek Accu',
            'Kompor Gas',
            'Kompor Tradisional/Hawu',
            'Pembakaran Sampah/Lahan',
            'Puntung Rokok',
            'Lilin/Korek Api/Bensin',
            'Petasan/Bensin/Pengelasan',
            'Tabrakan/Macet Rem',
            'Belum diketahui',
            'Lainnya',
        ];

        foreach ($penyebabs as $penyebab) {
            KategoriPenyebab::updateOrCreate(
                ['nama_penyebab' => $penyebab]
            );
        }

        $this->command->info('KategoriPenyebab: ' . count($penyebabs) . ' penyebab berhasil di-seed.');
    }
}