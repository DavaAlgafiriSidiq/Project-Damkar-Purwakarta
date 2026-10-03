<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model: ZonaLayanan
 *
 * Merepresentasikan 4 zona operasional Damkar Purwakarta:
 *   - WMK Pusat    (kode: WMK)
 *   - UPTD 1-Plered    (kode: UPTD1)
 *   - UPTD 2-Wanayasa  (kode: UPTD2)
 *   - UPTD 3-Cikopo    (kode: UPTD3)
 *
 * Setiap zona membawahi beberapa kecamatan yang menjadi wilayah tanggung jawabnya.
 *
 * Relasi: ZonaLayanan -hasMany-> Kecamatan
 */
class ZonaLayanan extends Model
{
    /**
     * Nama tabel eksplisit ditetapkan karena Laravel akan default ke
     * "zona_layanans" (plural otomatis bahasa Inggris yang tidak sesuai
     * dengan nama tabel bahasa Indonesia).
     */
    protected $table = 'zona_layanan';

    /**
     * Kolom yang boleh diisi via mass assignment.
     * kode_zona : kode singkat zona (misal: WMK, UPTD1)
     * nama_zona : nama lengkap zona layanan
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kode_zona',
        'nama_zona',
    ];

    // ─── Relasi ───────────────────────────────────────────────

    /**
     * Kecamatan-kecamatan yang termasuk dalam zona layanan ini.
     * Satu zona mencakup beberapa kecamatan (one-to-many).
     *
     * Nama method 'kecamatans' mempertahankan konvensi Laravel (plural)
     * untuk konsistensi dengan eager loading:
     *   Kecamatan::with('zonaLayanan')->get()
     *   ZonaLayanan::with('kecamatans')->get()
     */
    public function kecamatans(): HasMany
    {
        return $this->hasMany(Kecamatan::class, 'zona_layanan_id');
    }
}
