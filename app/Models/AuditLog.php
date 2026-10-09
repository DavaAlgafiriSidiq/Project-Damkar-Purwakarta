<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model: AuditLog
 *
 * Menyimpan jejak audit setiap perubahan pada model-model penting sistem.
 * Di-trigger otomatis oleh Eloquent Observer yang terpasang di model KejadianKebakaran.
 *
 * TIDAK ada $fillable yang luas — semua field diisi secara eksplisit oleh
 * KejadianKebakaran::booted() agar tidak ada kebocoran data tidak sengaja.
 *
 * Kolom:
 *   user_id    → Siapa yang melakukan perubahan
 *   action     → 'created' | 'updated' | 'deleted'
 *   model_type → FQCN model (e.g. App\Models\KejadianKebakaran)
 *   model_id   → ID record yang diubah
 *   old_values → Data sebelum perubahan (JSON)
 *   new_values → Data sesudah perubahan (JSON)
 */
class AuditLog extends Model
{
    protected $table = 'audit_logs';

    /**
     * Hanya gunakan created_at, tidak perlu updated_at
     * karena log bersifat immutable (tidak pernah di-update).
     */
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    // ─── Relasi ───────────────────────────────────────────────

    /**
     * User yang melakukan aksi (nullable jika sistem/seeder).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
