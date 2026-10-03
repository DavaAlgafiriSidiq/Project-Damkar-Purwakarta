<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model: StatKebakaranPerPenyebab
 *
 * Model untuk tabel stat_kebakaran_per_penyebab yang menyimpan
 * data historis agregasi kebakaran berdasarkan dugaan penyebab per bulan.
 *
 * Sumber data: kebakaran_penyebab.csv
 */
class StatKebakaranPerPenyebab extends Model
{
    protected $table = 'stat_kebakaran_per_penyebab';

    protected $fillable = [
        'tahun',
        'bulan',
        'dugaan_penyebab',
        'jumlah',
    ];

    protected $casts = [
        'tahun'  => 'integer',
        'jumlah' => 'integer',
    ];
}