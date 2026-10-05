<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hidran extends Model
{
    protected $fillable = [
        'nama',
        'lat',
        'lng',
        'jenis',
        'status',
        'alamat',
        'foto_url',
        'catatan',
        'kecamatan_id',
        'dicatat_oleh',
        'diupdate_oleh',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
    ];

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    public function pengupdate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diupdate_oleh');
    }
}