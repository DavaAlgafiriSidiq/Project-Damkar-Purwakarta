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

    /**
     * Scope: Urutkan data dengan memaksa opsi yang mengandung 'Lain-lain' atau 'Lainnya' di posisi terbawah.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * @param  string  $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderWithLainLast($query, $column = 'nama_kategori', $direction = 'asc')
    {
        return $query->orderByRaw(
            "CASE WHEN LOWER(nama_kategori) LIKE '%lain-lain%' OR LOWER(nama_kategori) LIKE '%lainnya%' THEN 1 ELSE 0 END ASC, {$column} {$direction}"
        );
    }

    /**
     * Helper: Urutkan collection agar item 'Lain-lain' / 'Lainnya' berada di urutan paling bawah.
     *
     * @param  \Illuminate\Support\Collection  $collection
     * @param  string  $column
     * @return \Illuminate\Support\Collection
     */
    public static function sortCollectionWithLainLast($collection, $column = 'nama_kategori')
    {
        return $collection->sort(function ($a, $b) use ($column) {
            $valA = strtolower($a->{$column} ?? '');
            $valB = strtolower($b->{$column} ?? '');

            $isLainA = (str_contains($valA, 'lain-lain') || str_contains($valA, 'lainnya')) ? 1 : 0;
            $isLainB = (str_contains($valB, 'lain-lain') || str_contains($valB, 'lainnya')) ? 1 : 0;

            if ($isLainA !== $isLainB) {
                return $isLainA <=> $isLainB;
            }

            return $valA <=> $valB;
        })->values();
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
