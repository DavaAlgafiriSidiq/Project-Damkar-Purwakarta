<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model: KejadianKebakaran
 *
 * Model INTI sistem — merepresentasikan satu laporan kejadian
 * kebakaran atau penyelamatan yang dicatat oleh petugas Damkar.
 *
 * ATURAN BISNIS (wajib dipatuhi di semua query dashboard):
 * Gunakan scope scopeVerifiedOnly() untuk memastikan HANYA data
 * dengan status_verifikasi = "verified" yang masuk ke kalkulasi publik.
 * Data berstatus "draft" TIDAK BOLEH tampil di dashboard.
 *
 * Relasi:
 *   KejadianKebakaran -belongsTo-> Kecamatan
 *   KejadianKebakaran -belongsTo-> KategoriObjek
 *   KejadianKebakaran -belongsTo-> KategoriPenyebab
 *   KejadianKebakaran -belongsTo-> User (dilaporkan_oleh)
 *   KejadianKebakaran -belongsTo-> User (diverifikasi_oleh)
 */
class KejadianKebakaran extends Model
{
    protected $table = 'kejadian_kebakaran';

    protected $fillable = [
        'jenis_layanan',
        'tanggal_waktu_kejadian',
        'kecamatan_id',
        'kategori_objek_id',
        'kategori_penyebab_id',
        'latitude',
        'longitude',
        'jumlah_personel',
        'taksiran_kerugian',
        'taksiran_terselamatkan',
        'deskripsi',
        'korban_meninggal',
        'korban_luka_berat',
        'korban_luka_ringan',
        'kk_terdampak',
        'jiwa_terdampak',
        'status_verifikasi',
        'status_operasi',
        'tanggal_waktu_selesai',
        'dilaporkan_oleh',
        'diverifikasi_oleh',
        'diverifikasi_pada',
    ];

    // Cast tipe data agar konsisten saat digunakan di PHP maupun JSON output
    protected $casts = [
        'tanggal_waktu_kejadian' => 'datetime',  // otomatis jadi Carbon instance
        'tanggal_waktu_selesai'  => 'datetime',
        'diverifikasi_pada'      => 'datetime',
        'latitude'               => 'float',      // numerik, bukan string
        'longitude'              => 'float',
        'taksiran_kerugian'      => 'float',      // agar JSON output berupa number, bukan string
        'taksiran_terselamatkan' => 'float',
        'jumlah_personel'        => 'integer',
        'korban_meninggal'       => 'integer',
        'korban_luka_berat'      => 'integer',
        'korban_luka_ringan'     => 'integer',
        'kk_terdampak'           => 'integer',
        'jiwa_terdampak'         => 'integer',
    ];

    // ─── Eloquent Scopes ──────────────────────────────────────

    /**
     * Scope: Hanya ambil data yang sudah diverifikasi.
     *
     * WAJIB digunakan di SETIAP query yang menyuplai data ke dashboard publik.
     * Ini adalah aturan bisnis yang tidak boleh dilanggar.
     *
     * Cara pakai: KejadianKebakaran::verifiedOnly()->get()
     *             KejadianKebakaran::verifiedOnly()->where(...)->count()
     */
    public function scopeVerifiedOnly(Builder $query): Builder
    {
        // Filter hanya data berstatus 'verified' (sudah divalidasi admin/Humas)
        return $query->where('status_verifikasi', 'verified');
    }

    /**
     * Scope: Filter berdasarkan jenis layanan.
     *
     * Cara pakai: KejadianKebakaran::verifiedOnly()->ofJenis('darurat')->get()
     *
     * @param string $jenis 'darurat' (kebakaran) atau 'non_darurat' (rescue)
     */
    public function scopeOfJenis(Builder $query, string $jenis): Builder
    {
        return $query->where('jenis_layanan', $jenis);
    }

    /**
     * Scope: Filter berdasarkan tahun kejadian.
     *
     * Cara pakai: KejadianKebakaran::verifiedOnly()->ofTahun(2024)->get()
     */
    public function scopeOfTahun(Builder $query, int $tahun): Builder
    {
        // Gunakan YEAR() MySQL/MariaDB — kompatibel keduanya
        return $query->whereYear('tanggal_waktu_kejadian', $tahun);
    }

    // ─── Relasi ───────────────────────────────────────────────

    /**
     * Kecamatan lokasi kejadian ini terjadi.
     * Eager load dengan zonaLayanan untuk hindari N+1 query:
     * KejadianKebakaran::with('kecamatan.zonaLayanan')->verifiedOnly()->get()
     */
    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }

    /**
     * Kategori objek yang terbakar/diselamatkan.
     */
    public function kategoriObjek(): BelongsTo
    {
        return $this->belongsTo(KategoriObjek::class, 'kategori_objek_id');
    }

    /**
     * Dugaan penyebab kebakaran.
     */
    public function kategoriPenyebab(): BelongsTo
    {
        return $this->belongsTo(KategoriPenyebab::class, 'kategori_penyebab_id');
    }

    /**
     * Petugas lapangan yang menginput laporan ini.
     */
    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dilaporkan_oleh');
    }

    /**
     * Admin/Humas yang memverifikasi laporan ini (null jika masih draft).
     */
    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }
}