<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model: Kecamatan
 *
 * Merepresentasikan 17 kecamatan di Kabupaten Purwakarta,
 * masing-masing terhubung ke satu zona layanan Damkar.
 *
 * Relasi:
 *   Kecamatan -belongsTo-> ZonaLayanan
 *   Kecamatan -hasMany-> KejadianKebakaran
 */
class Kecamatan extends Model
{
    protected $table = 'kecamatan';

    protected $fillable = [
        'zona_layanan_id',
        'nama_kecamatan',
        'zona_layanan',
    ];

    // ─── Relasi ───────────────────────────────────────────────

    /**
     * Zona layanan yang bertanggung jawab atas kecamatan ini.
     * Satu kecamatan hanya masuk dalam satu zona layanan (WMK/UPTD).
     */
    public function zonaLayanan(): BelongsTo
    {
        return $this->belongsTo(ZonaLayanan::class, 'zona_layanan_id');
    }

    /**
     * Semua kejadian kebakaran/penyelamatan yang terjadi di kecamatan ini.
     *
     * Nama method sengaja menggunakan camelCase 'kejadianKebakaran' sesuai
     * standar penamaan method PHP/Laravel.
     */
    public function kejadianKebakaran(): HasMany
    {
        return $this->hasMany(KejadianKebakaran::class, 'kecamatan_id');
    }
}
