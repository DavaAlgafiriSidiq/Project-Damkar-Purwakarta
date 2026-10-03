<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model: KategoriObjek
 *
 * Menyimpan daftar kategori objek yang terbakar atau jenis operasi rescue.
 * Kolom is_karhutla menandai apakah kategori ini termasuk
 * Kebakaran Hutan dan Lahan (Karhutla) — berguna untuk filter laporan khusus.
 *
 * Kolom jenis_layanan membedakan apakah kategori ini untuk:
 *   - 'kebakaran' : digunakan pada kejadian darurat (api)
 *   - 'rescue'    : digunakan pada kejadian non_darurat (penyelamatan)
 *
 * Relasi: KategoriObjek -hasMany-> KejadianKebakaran
 */
class KategoriObjek extends Model
{
    protected $table = 'kategori_objek';

    protected $fillable = [
        'nama_kategori',
        'jenis_layanan',
        'is_karhutla',
    ];

    /**
     * Cast otomatis: is_karhutla selalu menjadi boolean (bukan 0/1 integer).
     * Ini penting agar filter Karhutla di DashboardController bekerja benar.
     */
    protected $casts = [
        'is_karhutla' => 'boolean',
    ];

    // ─── Eloquent Scopes ──────────────────────────────────────

    /**
     * Scope: Filter kategori khusus kebakaran.
     * Cara pakai: KategoriObjek::kebakaran()->get()
     */
    public function scopeKebakaran($query)
    {
        return $query->where('jenis_layanan', 'kebakaran');
    }

    /**
     * Scope: Filter kategori khusus rescue (penyelamatan).
     * Cara pakai: KategoriObjek::rescue()->get()
     */
    public function scopeRescue($query)
    {
        return $query->where('jenis_layanan', 'rescue');
    }

    // ─── Relasi ───────────────────────────────────────────────

    /**
     * Kejadian kebakaran yang termasuk kategori objek ini.
     *
     * Nama method menggunakan camelCase 'kejadianKebakaran' sesuai standar Laravel.
     */
    public function kejadianKebakaran(): HasMany
    {
        return $this->hasMany(KejadianKebakaran::class, 'kategori_objek_id');
    }
}
