<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model: KategoriPenyebab
 *
 * Menyimpan daftar dugaan penyebab kebakaran yang digunakan
 * oleh Damkar Purwakarta, contoh:
 *   - Arus Pendek Listrik
 *   - Kompor Gas
 *   - Pembakaran Sampah/Lahan
 *
 * Catatan: hanya digunakan untuk jenis_layanan = 'darurat' (kebakaran).
 * Kejadian rescue (non_darurat) tidak memiliki kategori penyebab.
 *
 * Relasi: KategoriPenyebab -hasMany-> KejadianKebakaran
 */
class KategoriPenyebab extends Model
{
    protected $table = 'kategori_penyebab';

    protected $fillable = [
        'nama_penyebab',
    ];

    // ─── Eloquent Scopes & Helpers ───────────────────────────

    /**
     * Scope: Urutkan data dengan memaksa opsi yang mengandung 'Lain-lain' atau 'Lainnya' di posisi terbawah.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * @param  string  $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderWithLainLast($query, $column = 'nama_penyebab', $direction = 'asc')
    {
        return $query->orderByRaw(
            "CASE WHEN LOWER(nama_penyebab) LIKE '%lain-lain%' OR LOWER(nama_penyebab) LIKE '%lainnya%' THEN 1 ELSE 0 END ASC, {$column} {$direction}"
        );
    }

    /**
     * Helper: Urutkan collection agar item 'Lain-lain' / 'Lainnya' berada di urutan paling bawah.
     *
     * @param  \Illuminate\Support\Collection  $collection
     * @param  string  $column
     * @return \Illuminate\Support\Collection
     */
    public static function sortCollectionWithLainLast($collection, $column = 'nama_penyebab')
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
     * Kejadian kebakaran yang memiliki dugaan penyebab ini.
     *
     * Nama method menggunakan camelCase 'kejadianKebakaran' sesuai standar Laravel.
     */
    public function kejadianKebakaran(): HasMany
    {
        return $this->hasMany(KejadianKebakaran::class, 'kategori_penyebab_id');
    }
}
