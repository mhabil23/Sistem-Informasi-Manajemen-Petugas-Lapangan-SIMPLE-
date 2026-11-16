<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class MitraPetugasSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info("Menjalankan seeder Mitra Petugas Lapangan...");
        Artisan::call('import:mitra-petugas');
        $this->command->line(Artisan::output());
    }
}

