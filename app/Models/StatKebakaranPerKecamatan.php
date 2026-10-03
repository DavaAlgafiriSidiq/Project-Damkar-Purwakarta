<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model: StatKebakaranPerKecamatan
 *
 * Model untuk tabel stat_kebakaran_per_kecamatan yang menyimpan
 * data historis agregasi kebakaran berdasarkan kecamatan per bulan.
 *
 * Sumber data: kebakaran_kecamatan.csv
 * Catatan: kolom bulan menggunakan singkatan 3 huruf (JAN, FEB, dst.)
 * sesuai format asli CSV sumber.
 */
class StatKebakaranPerKecamatan extends Model
{
    protected $table = 'stat_kebakaran_per_kecamatan';

    protected $fillable = [
        'tahun',
        'bulan',
        'kecamatan',
        'jumlah',
    ];

    protected $casts = [
        'tahun'  => 'integer',
        'jumlah' => 'integer',
    ];
}