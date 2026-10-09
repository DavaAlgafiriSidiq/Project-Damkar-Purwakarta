<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    use SoftDeletes;

    protected $table = 'kejadian_kebakaran';

    /**
     * Kolom yang boleh diisi lewat mass assignment.
     *
     * KEAMANAN:
     *   - 'status_verifikasi' TIDAK ada di sini. Server hardcode 'draft' saat petugas
     *     submit. Hanya Admin/Humas yang boleh mengubahnya via update() eksplisit.
     *   - 'diverifikasi_oleh' & 'diverifikasi_pada' TIDAK ada di sini. Hanya diisi
     *     oleh VerifikasiKejadianController::approve() / update() saat status verified.
     *   - 'nama_pelapor' ada di sini untuk akuntabilitas akun bersama (Satu Akun Bersama).
     *
     * @var array<int, string>
     */
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
        'nama_pelapor',
        'status_operasi',
        'tanggal_waktu_selesai',
        'dilaporkan_oleh',
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

    // ─── Eloquent Observer (booted) ───────────────────────────

    /**
     * Hook lifecycle Eloquent untuk audit logging otomatis.
     *
     * Mencatat setiap event created / updated / deleted ke tabel audit_logs.
     * Kolom sensitif (status_verifikasi, diverifikasi_*) SENGAJA ikut di-log
     * karena audit log adalah catatan internal, bukan endpoint publik.
     */
    protected static function booted(): void
    {
        // Helper untuk menulis ke audit_logs
        $writeLog = function (string $action, ?array $oldValues, ?array $newValues) {
            try {
                AuditLog::create([
                    'user_id'    => auth()->id(),       // null jika dipanggil dari seeder/artisan
                    'action'     => $action,
                    'model_type' => static::class,
                    'model_id'   => 0,                  // placeholder; di-update setelah create
                    'old_values' => $oldValues,
                    'new_values' => $newValues,
                    'created_at' => now(),
                ]);
            } catch (\Throwable $e) {
                // Jangan sampai audit log error menghentikan operasi utama
                \Log::error('[AuditLog] Gagal menulis log: ' . $e->getMessage());
            }
        };

        // Event: Laporan baru dibuat
        static::created(function (KejadianKebakaran $model) use ($writeLog) {
            AuditLog::create([
                'user_id'    => auth()->id(),
                'action'     => 'created',
                'model_type' => static::class,
                'model_id'   => $model->id,
                'old_values' => null,
                'new_values' => $model->getAttributes(),
                'created_at' => now(),
            ]);
        });

        // Event: Laporan diperbarui
        static::updated(function (KejadianKebakaran $model) use ($writeLog) {
            // Hanya catat kolom yang benar-benar berubah (dirty)
            $changed = $model->getChanges();
            if (empty($changed)) {
                return;
            }

            // Ambil nilai lama hanya untuk kolom yang berubah
            $oldValues = array_intersect_key($model->getOriginal(), $changed);

            AuditLog::create([
                'user_id'    => auth()->id(),
                'action'     => 'updated',
                'model_type' => static::class,
                'model_id'   => $model->id,
                'old_values' => $oldValues,
                'new_values' => $changed,
                'created_at' => now(),
            ]);
        });

        // Event: Laporan dihapus (soft delete)
        static::deleted(function (KejadianKebakaran $model) {
            AuditLog::create([
                'user_id'    => auth()->id(),
                'action'     => 'deleted',
                'model_type' => static::class,
                'model_id'   => $model->id,
                'old_values' => $model->getAttributes(),
                'new_values' => null,
                'created_at' => now(),
            ]);
        });
    }

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