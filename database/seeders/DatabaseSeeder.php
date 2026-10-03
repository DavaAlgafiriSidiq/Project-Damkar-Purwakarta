<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ZonaLayananSeeder::class,
            KecamatanSeeder::class,
            KategoriObjekSeeder::class,
            KategoriPenyebabSeeder::class,
            KejadianKebakaranSeeder::class, // Menggantikan seluruh Stat Seeder (Single Source of Truth)
        ]);
    }
}