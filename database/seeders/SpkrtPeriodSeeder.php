<?php

namespace Database\Seeders;

use App\Models\SpkrtPeriod;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SpkrtPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        // 1. Periode Aktif (Sedang Berjalan - Pengisian SPKRT Dibuka)
        SpkrtPeriod::firstOrCreate(
            ['activity_name' => 'Survei Pertanian Terintegrasi (SPKRT) 2026'],
            [
                'start_date' => Carbon::now()->startOfMonth(),
                'end_date' => Carbon::now()->endOfMonth(),
                'is_active' => true,
                'description' => 'Periode pengisian formulir SPKRT (Tanda Terima Responden) untuk pendataan sampel ruta.',
                'created_by' => $admin?->id,
            ]
        );

        // 2. Contoh Periode yang Belum Dibuka (Masa Depan)
        SpkrtPeriod::firstOrCreate(
            ['activity_name' => 'Survei Uji Coba Lapangan Triwulan II'],
            [
                'start_date' => Carbon::now()->addDays(15),
                'end_date' => Carbon::now()->addDays(30),
                'is_active' => true,
                'description' => 'Periode pengisian SPKRT putaran kedua (belum dimulai).',
                'created_by' => $admin?->id,
            ]
        );

        // 3. Contoh Periode yang Sudah Berakhir / Ditutup
        SpkrtPeriod::firstOrCreate(
            ['activity_name' => 'Survei Pendahuluan SPKRT 2025'],
            [
                'start_date' => Carbon::now()->subMonths(2),
                'end_date' => Carbon::now()->subMonth(),
                'is_active' => true,
                'description' => 'Periode telah berakhir dan terkunci secara otomatis.',
                'created_by' => $admin?->id,
            ]
        );
    }
}
