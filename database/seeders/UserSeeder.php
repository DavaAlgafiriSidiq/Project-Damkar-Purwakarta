<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder: UserSeeder
 *
 * Mengisi tabel users dengan 2 akun dummy untuk pengujian fitur role.
 * - Akun Admin: admin@damkar.com (Role: admin)
 * - Akun Petugas: petugas@damkar.com (Role: petugas)
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat akun Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@damkar.com'],
            [
                'name'     => 'Administrator Damkar',
                'password' => Hash::make('password'),
            ]
        );
        $admin->role = 'admin';
        $admin->save();

        // Buat akun Petugas
        $petugas = User::updateOrCreate(
            ['email' => 'petugas@damkar.com'],
            [
                'name'     => 'Petugas Lapangan',
                'password' => Hash::make('password'),
            ]
        );
        $petugas->role = 'petugas';
        $petugas->save();

        $this->command->info('UserSeeder: 2 Akun dummy berhasil dibuat.');
    }
}