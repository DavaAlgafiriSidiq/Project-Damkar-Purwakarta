<?php

namespace Database\Seeders;

use App\Models\Hidran;
use App\Models\Kecamatan;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeder: data hidran plat merah (milik Pemda) di Kecamatan Purwakarta.
 *
 * Koordinat sumber dalam format DMS (derajat-menit-detik) dikonversi
 * ke desimal dengan rumus: desimal = derajat + menit/60 + detik/3600,
 * lalu dijadikan negatif untuk lintang selatan (S).
 *
 * Catatan konversi: pada data pertama (Taman Pancaniti), detik bujur
 * tertulis 111.8" (melebihi 60"). Ini ditangani dengan carry 1 menit:
 * 111.8" - 60" = 51.8", menit 26 + 1 = 27 -> 107°27'51.8"E.
 * Kalau ternyata itu salah ketik dari sumber asli, cukup perbaiki
 * nilai lat/lng pada baris pertama di bawah.
 */
class HidranSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::where('role', 'admin')->value('id');
        $kecamatanId = Kecamatan::where('nama_kecamatan', 'Purwakarta')->value('id');

        $data = [
            [
                'nama' => 'Taman Pancaniti',
                'lat' => -6.5549167,
                'lng' => 107.4643889,
                'alamat' => 'Jl. Jend. Ahmad Yani, Taman Pancaniti',
                'status' => 'rusak',
                'catatan' => 'Macet',
            ],
            [
                'nama' => 'Depan Taman Parcom',
                'lat' => -6.5607222,
                'lng' => 107.4375000,
                'alamat' => 'Jl. Basuki Rahmat, depan Taman Parcom',
                'status' => 'rusak',
                'catatan' => 'Macet',
            ],
            [
                'nama' => 'Sindangkasih - depan Younk Audio',
                'lat' => -6.5606389,
                'lng' => 107.4415556,
                'alamat' => 'Jl. Basuki Rahmat No. 43, Sindangkasih, depan Younk Audio',
                'status' => 'rusak',
                'catatan' => 'Macet',
            ],
            [
                'nama' => 'Pasar Rebo - Gapura Pertigaan',
                'lat' => -6.5615833,
                'lng' => 107.4471667,
                'alamat' => 'Pasar Rebo, gapura pertigaan',
                'status' => 'rusak',
                'catatan' => 'Air ada, harus dibuka kran bawah',
            ],
            [
                'nama' => 'Gang Sumba Simpang',
                'lat' => -6.5645556,
                'lng' => 107.4506111,
                'alamat' => 'Gang Sumba simpang (masuk gang sebelah kiri)',
                'status' => 'rusak',
                'catatan' => 'Mati, tidak ada air',
            ],
            [
                'nama' => 'Situ Buleud - Pertigaan Jl. Gudang',
                'lat' => -6.5551389,
                'lng' => 107.4461389,
                'alamat' => 'Situ Buleud, pertigaan Jl. Gudang',
                'status' => 'rusak',
                'catatan' => 'Macet',
            ],
            [
                'nama' => 'Hotel Priangan',
                'lat' => -6.5491944,
                'lng' => 107.4436944,
                'alamat' => 'Jl. Jend. Sudirman No. 31, Hotel Priangan',
                'status' => 'optimal',
                'catatan' => 'Bagus, air lancar',
            ],
            [
                'nama' => 'Pasar Jumat - Pos Pol Pasjum',
                'lat' => -6.5445833,
                'lng' => 107.4434722,
                'alamat' => 'Jl. Jend. Sudirman, Pasar Jumat, Pospol Pasjum',
                'status' => 'rusak',
                'catatan' => 'Macet',
            ],
            [
                'nama' => 'Rusunawa Kp. Sukamaju',
                'lat' => -6.5210278,
                'lng' => 107.4573056,
                'alamat' => 'Rusunawa Kp. Sukamaju RT 05/RW 05/07, Kel. Ciseureuh, Kec. Purwakarta',
                'status' => 'rusak',
                'catatan' => 'Ada 3 unit hidran di lokasi ini: 2 rusak, 1 baru tapi belum ada air',
            ],
            [
                'nama' => 'Rawasari Citalang-Munjul',
                'lat' => -6.5318333,
                'lng' => 107.4618056,
                'alamat' => 'Jl. Rawasari, Citalang-Munjul (seberang Perum BJI)',
                'status' => 'optimal',
                'catatan' => 'Bagus, air ada dan lancar',
            ],
            [
                'nama' => 'Kantor Kelurahan Munjul',
                'lat' => -6.5301111,
                'lng' => 107.4551111,
                'alamat' => 'Kantor Kelurahan Munjul',
                'status' => 'rusak',
                'catatan' => 'Mati, tidak ada air',
            ],
            [
                'nama' => 'Depan Puskesmas Koncara',
                'lat' => -6.5414722,
                'lng' => 107.4411111,
                'alamat' => 'Jl. Ibrahim Singadilaga No. 79, depan Puskesmas Koncara',
                'status' => 'rusak',
                'catatan' => 'Rusak',
            ],
            [
                'nama' => 'Asrama Polisi Cipaisan',
                'lat' => -6.5494444,
                'lng' => 107.4389167,
                'alamat' => 'Jl. Ahmad Yani, Asrama Polisi, Cipaisan',
                'status' => 'optimal',
                'catatan' => 'Bagus, air lancar',
            ],
            [
                'nama' => 'Depan Masjid Agung',
                'lat' => -6.5556111,
                'lng' => 107.4413611,
                'alamat' => 'Jl. Gandanegara, Purwakarta (depan Masjid Agung)',
                'status' => 'optimal',
                'catatan' => 'Bagus, air lancar',
            ],
            [
                'nama' => 'Diskominfo',
                'lat' => -6.5551944,
                'lng' => 107.4413333,
                'alamat' => 'Jl. Gandanegara, Diskominfo',
                'status' => 'rusak',
                'catatan' => 'Terkubur, terhalang tanaman, kondisi tidak bagus',
            ],
        ];

        foreach ($data as $row) {
            Hidran::create(array_merge($row, [
                'jenis' => 'plat_merah',
                'foto_url' => null,
                'kecamatan_id' => $kecamatanId,
                'dicatat_oleh' => $adminId,
            ]));
        }
    }
}