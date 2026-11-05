<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\AssignmentOfficer;
use App\Models\OfficerReport;
use App\Models\Respondent;
use App\Models\RespondentBast;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MonitoringDashboardSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure primary assignment exists
        $assignment = Assignment::firstOrCreate(
            ['title' => 'Pendataan Sensus SIMPLE Sektor Pertanian & UMKM 2026'],
            [
                'description' => 'Pendataan komprehensif profil responden pertanian terpadu dan pengumpulan berkas administrasi lapangan.',
                'start_date' => now()->startOfMonth(),
                'end_date' => now()->endOfMonth(),
                'location' => 'Wilayah Terpadu Multi-Kecamatan',
                'target_respondents' => 680,
                'status' => 'ongoing',
                'created_by' => 1,
            ]
        );
        $assignment->update(['target_respondents' => 680]);

        // 2. Daftar Petugas Lapangan dengan variasi progres
        $officersData = [
            ['username' => 'budi.santoso', 'name' => 'Budi Santoso, S.Kom', 'phone' => '081234567891', 'district' => 'Kecamatan Sukamaju'],
            ['username' => 'siti.rahmawati', 'name' => 'Siti Rahmawati, S.Tr.Stat', 'phone' => '081234567892', 'district' => 'Kecamatan Coblong'],
            ['username' => 'ahmad.fauzi', 'name' => 'Ahmad Fauzi, S.Si', 'phone' => '081298765431', 'district' => 'Kecamatan Sumur Bandung'],
            ['username' => 'dewi.lestari', 'name' => 'Dewi Lestari, S.E.', 'phone' => '081345678902', 'district' => 'Kecamatan Cicendo'],
            ['username' => 'hendra.wijaya', 'name' => 'Hendra Wijaya, S.P.', 'phone' => '081567890123', 'district' => 'Kecamatan Lengkong'],
            ['username' => 'rini.kartika', 'name' => 'Rini Kartika, S.Si', 'phone' => '081789012345', 'district' => 'Kecamatan Andir'],
            ['username' => 'agus.setiawan', 'name' => 'Agus Setiawan, S.Kom', 'phone' => '081901234567', 'district' => 'Kecamatan Sukamaju'],
            ['username' => 'nurul.hidayah', 'name' => 'Nurul Hidayah, S.Sos', 'phone' => '082123456789', 'district' => 'Kecamatan Coblong'],
            ['username' => 'eko.prasetyo', 'name' => 'Eko Prasetyo, S.P.', 'phone' => '082234567890', 'district' => 'Kecamatan Sumur Bandung'],
            ['username' => 'fitri.handayani', 'name' => 'Fitri Handayani, S.Stat', 'phone' => '082345678901', 'district' => 'Kecamatan Cicendo'],
            ['username' => 'rizki.ramadhan', 'name' => 'Rizki Ramadhan, S.T.', 'phone' => '085212345678', 'district' => 'Kecamatan Lengkong'],
            ['username' => 'anisa.putri', 'name' => 'Anisa Putri, S.E.', 'phone' => '085323456789', 'district' => 'Kecamatan Andir'],
        ];

        $createdOfficers = [];
        foreach ($officersData as $item) {
            $user = User::firstOrCreate(
                ['username' => $item['username']],
                [
                    'name' => $item['name'],
                    'email' => $item['username'] . '@simple.go.id',
                    'phone' => $item['phone'],
                    'password' => Hash::make('password'),
                    'role' => 'petugas',
                    'status' => 'active',
                ]
            );

            AssignmentOfficer::firstOrCreate(
                ['assignment_id' => $assignment->id, 'user_id' => $user->id],
                [
                    'specific_task' => "Wilayah kerja {$item['district']}",
                    'status' => 'in_progress',
                    'progress_notes' => 'Sedang melaksanakan pendataan lapangan',
                ]
            );

            $createdOfficers[] = [
                'user' => $user,
                'district' => $item['district'],
            ];
        }

        // 3. Populate Respondents up to 680 if needed
        $currentRespondentsCount = Respondent::count();
        $targetTotal = 680;

        if ($currentRespondentsCount < $targetTotal) {
            $districts = [
                'Kecamatan Sukamaju',
                'Kecamatan Coblong',
                'Kecamatan Sumur Bandung',
                'Kecamatan Cicendo',
                'Kecamatan Lengkong',
                'Kecamatan Andir',
            ];

            $sampleNames = [
                'Kelompok Tani Harapan Jaya', 'UD. Berkah Mandiri', 'Koperasi Agro Makmur',
                'Bpk. Supardi Tani', 'Ibu Ratna Pertiwi', 'CV. Mitra Subur Sentosa',
                'Kelompok Tani Bina Usaha', 'UD. Hasil Bumi', 'Bpk. Herman Suherman',
                'Ibu Endang Sri Rejeki', 'Kelompok Tani Maju Bersama', 'Toko Tani Makmur',
                'Bpk. Bambang Irawan', 'Ibu Yuliana Pertanian', 'UD. Agro Lestari',
                'Kelompok Budidaya Mandiri', 'Bpk. Gunawan Wibowo', 'Ibu Nurhayati Tani',
                'Kelompok Perkebunan Subur', 'CV. Aneka Hasil Tani',
            ];

            $toCreate = $targetTotal - $currentRespondentsCount;
            $batch = [];
            $now = now();

            for ($i = 0; $i < $toCreate; $i++) {
                $officerInfo = $createdOfficers[$i % count($createdOfficers)];
                $nameBase = $sampleNames[$i % count($sampleNames)];
                $district = $officerInfo['district'];

                $batch[] = [
                    'name' => $nameBase . ' #' . ($currentRespondentsCount + $i + 1),
                    'address' => "Jl. Wilayah RT 0" . (($i % 9) + 1) . "/RW 0" . (($i % 5) + 1) . ", " . $district,
                    'district' => $district,
                    'phone' => '08' . rand(111111111, 999999999),
                    'agency' => 'Pelaku Usaha / Pertanian ' . $district,
                    'purpose' => 'Pendataan Sensus Sektor Pertanian & UMKM SIMPLE 2026',
                    'assignment_id' => $assignment->id,
                    'officer_id' => $officerInfo['user']->id,
                    'input_date' => $now->copy()->subDays($i % 14)->toDateString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($batch) >= 100) {
                    Respondent::insert($batch);
                    $batch = [];
                }
            }

            if (!empty($batch)) {
                Respondent::insert($batch);
            }
        }

        // 4. Update existing respondents district if null
        Respondent::whereNull('district')->orWhere('district', '')->update([
            'district' => 'Kecamatan Sukamaju',
        ]);

        // 5. Generate BAST responses to reach realistic progress (misal ~412 dari 680 sudah BAST = ~60.5%)
        $targetBastCount = 412;
        $currentBastCount = RespondentBast::count();

        if ($currentBastCount < $targetBastCount) {
            // Give higher completion to some officers (100%), medium to others (60%), and very low/zero to a few (slow officers for testing!)
            // Officers 0, 1, 2, 3: high progress
            // Officers 4, 5, 6, 7: medium progress
            // Officers 8, 9, 10, 11: slow / low progress (perlu notifikasi!)
            $respondentsWithoutBast = Respondent::whereDoesntHave('basts')->get();

            $bastBatch = [];
            $romanMonths = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
            $romanMonth = $romanMonths[now()->month - 1];
            $year = now()->year;
            $bastIndex = $currentBastCount + 1;

            foreach ($respondentsWithoutBast as $resp) {
                if ($bastIndex > $targetBastCount) {
                    break;
                }

                // Decide whether this respondent gets BAST based on officer performance profile
                $officerId = $resp->officer_id;
                // Petugas lambat: officer ID yang besar punya kemungkinan kecil dapat BAST
                $officerMod = $officerId % 4;
                if ($officerMod === 3 && rand(1, 10) > 2) {
                    // Petugas lambat: 80% skip BAST
                    continue;
                } elseif ($officerMod === 2 && rand(1, 10) > 5) {
                    // Petugas sedang
                    continue;
                }

                $bastBatch[] = [
                    'bast_number' => sprintf('BAST-RESP/%s/%s/%04d', $year, $romanMonth, $bastIndex),
                    'bast_date' => $resp->input_date ? $resp->input_date->toDateString() : now()->toDateString(),
                    'respondent_id' => $resp->id,
                    'assignment_id' => $assignment->id,
                    'officer_id' => $resp->officer_id,
                    'handover_items' => "1. Formulir Kuesioner Responden Terisi Lengkap\n2. Lembar Verifikasi Lapangan",
                    'notes' => 'Telah diverifikasi dan ditandatangani sesuai format standar.',
                    'status' => 'finalized',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $bastIndex++;

                if (count($bastBatch) >= 100) {
                    RespondentBast::insert($bastBatch);
                    $bastBatch = [];
                }
            }

            if (!empty($bastBatch)) {
                RespondentBast::insert($bastBatch);
            }
        }

        // 6. Generate Officer Reports for some officers (some have submitted, some have not)
        $officers = User::where('role', 'petugas')->get();
        foreach ($officers as $idx => $officer) {
            // Half the officers have submitted reports, half haven't yet
            if ($idx % 2 === 0) {
                OfficerReport::firstOrCreate(
                    [
                        'user_id' => $officer->id,
                        'assignment_id' => $assignment->id,
                    ],
                    [
                        'report_number' => sprintf('LAP-SPD/2026/%03d', $officer->id),
                        'officer_name' => $officer->name,
                        'officer_nip' => '19900101' . rand(100000, 999999),
                        'id_type' => 'nip',
                        'destination_purpose' => 'Pendataan Sensus dan Verifikasi Lapangan BAST Sektor Pertanian & UMKM',
                        'activity_date' => now()->subDays(rand(1, 5))->toDateString(),
                        'activity_title' => 'Laporan Perjalanan Dinas Lapangan ' . $assignment->title,
                        'work_description' => 'Telah melakukan kunjungan ke wilayah responden, verifikasi fisik, penyerahan kuesioner dan penandatanganan dokumen BAST.',
                        'status' => ($idx % 4 === 0) ? 'approved' : 'submitted',
                    ]
                );
            }
        }
    }
}

