<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model: User
 *
 * Merepresentasikan pengguna sistem Damkar Purwakarta.
 * Terdapat 2 role yang tersedia:
 *   - admin   : Administrator/Humas yang berwenang memverifikasi laporan
 *   - petugas : Petugas lapangan yang bertugas menginput kejadian
 *
 * PENTING: Kolom 'role' WAJIB ada di $fillable agar UserSeeder dan
 * pembuatan akun lewat mass assignment berjalan dengan benar.
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Kolom yang boleh diisi lewat mass assignment.
     * 'role' wajib disertakan agar UserSeeder dapat mengisi role saat pertama kali dibuat.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Wajib: digunakan oleh UserSeeder dan RoleMiddleware
    ];

    /**
     * Kolom yang disembunyikan dari serialisasi (JSON response / array).
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cast tipe data kolom agar konsisten saat digunakan di PHP.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }
}
