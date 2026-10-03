<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model: StatRescuePerObjek
 *
 * Model untuk tabel stat_rescue_per_objek yang menyimpan
 * data historis agregasi operasi penyelamatan (rescue)
 * berdasarkan jenis kasus per bulan.
 *
 * Sumber data: rescue_objek.csv
 * Jenis rescue: Sarang Tawon, Ular, Biawak, Kecelakaan, Kebakaran, dst.
 */
class StatRescuePerObjek extends Model
{
    protected $table = 'stat_rescue_per_objek';

    protected $fillable = [
        'tahun',
        'bulan',
        'jenis_objek',
        'jumlah',
    ];

    protected $casts = [
        'tahun'  => 'integer',
        'jumlah' => 'integer',
    ];
}