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
