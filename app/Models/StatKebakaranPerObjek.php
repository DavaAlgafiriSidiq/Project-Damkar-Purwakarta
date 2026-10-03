<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model: StatKebakaranPerObjek
 *
 * Model untuk tabel stat_kebakaran_per_objek yang menyimpan
 * data historis agregasi kebakaran berdasarkan jenis objek per bulan.
 *
 * Data ini berasal dari import CSV (kebakaran_objek.csv) dan
 * digunakan sebagai data awal dashboard sebelum sistem real-time
 * menghasilkan data yang cukup untuk analisis.
 */
class StatKebakaranPerObjek extends Model
{
    protected $table = 'stat_kebakaran_per_objek';

    protected $fillable = [
        'tahun',
        'bulan',
        'jenis_objek',
        'jumlah',
    ];

    // Cast numerik agar JSON output berupa number, bukan string
    protected $casts = [
        'tahun'  => 'integer',
        'jumlah' => 'integer',
    ];
}