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

    /**
     * Scope untuk mengurutkan kecamatan secara sistematis:
     * 1. WMK Pusat
     * 2. WMK UPTD 1
     * 3. WMK UPTD 2
     * 4. WMK UPTD 3
     * 5. Luar Kabupaten / Luar Daerah (di urutan paling akhir)
     * Diikuti urutan nama_kecamatan ASC di setiap zona.
     */
    public function scopeOrderByZona($query)
    {
        return $query->orderByRaw("
            CASE 
                WHEN LOWER(nama_kecamatan) LIKE '%luar%' OR LOWER(COALESCE(zona_layanan, '')) LIKE '%luar%' THEN 5
                WHEN LOWER(COALESCE(zona_layanan, '')) LIKE '%pusat%' THEN 1
                WHEN LOWER(COALESCE(zona_layanan, '')) LIKE '%uptd 1%' OR LOWER(COALESCE(zona_layanan, '')) LIKE '%uptd1%' THEN 2
                WHEN LOWER(COALESCE(zona_layanan, '')) LIKE '%uptd 2%' OR LOWER(COALESCE(zona_layanan, '')) LIKE '%uptd2%' THEN 3
                WHEN LOWER(COALESCE(zona_layanan, '')) LIKE '%uptd 3%' OR LOWER(COALESCE(zona_layanan, '')) LIKE '%uptd3%' THEN 4
                ELSE 4.5
            END ASC,
            nama_kecamatan ASC
        ");
    }
}
