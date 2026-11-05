<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\AssignmentOfficer;
use App\Models\DocumentTemplate;
use App\Models\OfficerBast;
use App\Models\OfficerReport;
use App\Models\Respondent;
use App\Models\RespondentBast;
use App\Models\Schedule;
use App\Models\TrainingEvent;
use App\Models\TrainingParticipant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator SIMPLE',
                'email' => 'admin@simple.go.id',
                'phone' => '081234567890',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        $officer1 = User::firstOrCreate(
            ['username' => 'petugas1'],
            [
                'name' => 'Budi Santoso, S.Kom',
                'email' => 'petugas1@simple.go.id',
                'phone' => '081234567891',
                'password' => Hash::make('password'),
                'role' => 'petugas',
                'status' => 'active',
            ]
        );

        $officer2 = User::firstOrCreate(
            ['username' => 'petugas2'],
            [
                'name' => 'Siti Rahmawati, S.Tr.Stat',
                'email' => 'petugas2@simple.go.id',
                'phone' => '081234567892',
                'password' => Hash::make('password'),
                'role' => 'petugas',
                'status' => 'active',
            ]
        );

        // 2. Document Templates
        DocumentTemplate::firstOrCreate(
            ['code' => 'bast_respondent'],
            [
                'name' => 'Template BAST Responden',
                'type' => 'bast_respondent',
                'header_text' => 'PEMERINTAH REPUBLIK INDONESIA - SISTEM INFORMASI SIMPLE',
                'footer_text' => 'Dokumen resmi diterbitkan melalui Sistem Administrasi SIMPLE.',
                'content' => '<p>Pada hari ini tanggal {TANGGAL}, bertempat di {LOKASI}, telah dilaksanakan serah terima dokumen/kegiatan antara pihak Petugas SIMPLE dengan pihak Responden terkait.</p>',
                'is_active' => true,
            ]
        );

        DocumentTemplate::firstOrCreate(
            ['code' => 'bast_officer'],
            [
                'name' => 'Template BAST Petugas',
                'type' => 'bast_officer',
                'header_text' => 'BERITA ACARA SERAH TERIMA PETUGAS LAPANGAN SIMPLE',
                'footer_text' => 'Dokumen sah hasil verifikasi dan penugasan resmi.',
                'content' => '<p>Berita Acara ini menyatakan bahwa Petugas Lapangan telah menyelesaikan penugasan kegiatan {KEGIATAN} dan menyerahkan seluruh berkas administrasi kepada koordinator kegiatan.</p>',
                'is_active' => true,
            ]
        );

        // 3. Sample Assignment & Assignment Officers
        $assignment = Assignment::create([
            'title' => 'Pendataan Sensus SIMPLE Sektor Pertanian & UMKM 2026',
            'description' => 'Pendataan komprehensif profil responden pertanian terpadu dan pengumpulan berkas administrasi lapangan.',
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
            'location' => 'Kecamatan Sukamaju dan Sekitarnya',
            'status' => 'ongoing',
            'created_by' => $admin->id,
        ]);

        AssignmentOfficer::create([
            'assignment_id' => $assignment->id,
            'user_id' => $officer1->id,
            'specific_task' => 'Pendataan responden wilayah Desa Sukamaju Blok Timur (Target: 30 Responden)',
            'status' => 'in_progress',
            'progress_notes' => '15 responden telah dikunjungi dan diverifikasi.',
        ]);

        AssignmentOfficer::create([
            'assignment_id' => $assignment->id,
            'user_id' => $officer2->id,
            'specific_task' => 'Pendataan responden wilayah Desa Makmur Blok Barat (Target: 25 Responden)',
            'status' => 'in_progress',
            'progress_notes' => 'Pendataan berjalan lancar, berkoordinasi dengan kantor desa setempat.',
        ]);

        // 4. Sample Respondents
        $respondent1 = Respondent::create([
            'name' => 'Kelompok Tani Harapan Sejahtera (Bpk. Ahmad)',
            'address' => 'Jl. Raya Pertanian No. 12, Desa Sukamaju',
            'phone' => '082199887766',
            'agency' => 'Kelompok Tani Tingkat Desa',
            'purpose' => 'Pendataan Bantuan Sarana & Prasarana SIMPLE',
            'assignment_id' => $assignment->id,
            'officer_id' => $officer1->id,
            'input_date' => now()->subDays(3),
        ]);

        $respondent2 = Respondent::create([
            'name' => 'UD. Sumber Rezeki (Ibu Marlina)',
            'address' => 'Jl. Pasar Baru No. 45, Desa Makmur',
            'phone' => '085211223344',
            'agency' => 'Pelaku Usaha Pengolahan Hasil Tani',
            'purpose' => 'Verifikasi Lapangan Usaha Agribisnis',
            'assignment_id' => $assignment->id,
            'officer_id' => $officer2->id,
            'input_date' => now()->subDays(2),
        ]);

        // 5. Sample BAST
        RespondentBast::create([
            'bast_number' => 'BAST-RESP/2026/XI/001',
            'bast_date' => now()->subDays(2),
            'respondent_id' => $respondent1->id,
            'assignment_id' => $assignment->id,
            'officer_id' => $officer1->id,
            'handover_items' => "1. Formulir Kuesioner Lapangan SIMPLE (1 Rangkap)\n2. Salinan KTP & KK Penanggung Jawab\n3. Surat Keterangan Domisili Usaha Tani",
            'notes' => 'Serah terima berkas fisik dan digital telah lengkap tanpa kendala.',
            'status' => 'finalized',
        ]);

        OfficerBast::create([
            'bast_number' => 'BAST-PETUGAS/2026/XI/001',
            'bast_date' => now()->subDays(1),
            'assignment_id' => $assignment->id,
            'officer_id' => $officer1->id,
            'description' => 'Penyerahan bundel kuesioner gelombang 1 wilayah Kecamatan Sukamaju.',
            'notes' => 'Telah diperiksa kelengkapannya oleh Pengawas Administrasi.',
            'status' => 'finalized',
        ]);

        // 6. Sample Officer Report
        OfficerReport::create([
            'report_number' => 'LAP-SIMPLE/2026/XI/001',
            'user_id' => $officer1->id,
            'assignment_id' => $assignment->id,
            'activity_date' => now()->subDays(1),
            'activity_title' => 'Monitoring & Wawancara Lapangan Responden Tani Sukamaju',
            'work_description' => 'Melaksanakan kunjungan langsung ke lokasi responden Kelompok Tani Harapan Sejahtera. Melakukan wawancara mendalam mengenai produksi pertanian, kendala pupuk, dan verifikasi dokumen legalitas kelompok. Semua data tervalidasi dengan baik.',
            'status' => 'submitted',
        ]);

        // 7. Training Event & Participants
        $training = TrainingEvent::create([
            'title' => 'Bimbingan Teknis dan Pelatihan Administrasi Digital SIMPLE Angkatan I',
            'description' => 'Pelatihan tata kelola data lapangan, pengisian kuesioner digital, serta pembuatan dokumen serah terima terintegrasi.',
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(7),
            'location' => 'Aula Gedung Pertemuan Kantor Wilayah SIMPLE',
            'quota' => 50,
            'status' => 'upcoming',
            'created_by' => $admin->id,
        ]);

        TrainingParticipant::create([
            'training_event_id' => $training->id,
            'name' => 'Hendro Prasetyo, S.E.',
            'nip_or_id' => '198904122015031002',
            'category' => 'Pegawai',
            'agency' => 'Dinas Wilayah SIMPLE',
            'phone' => '081377889900',
            'email' => 'hendro@simple.go.id',
            'attendance_status' => 'present',
        ]);

        TrainingParticipant::create([
            'training_event_id' => $training->id,
            'name' => 'Dewi Anggraini',
            'nip_or_id' => 'MGR-2026-004',
            'category' => 'Magang Mitra',
            'agency' => 'Universitas Negeri',
            'phone' => '087766554433',
            'email' => 'dewi.ang@gmail.com',
            'attendance_status' => 'present',
        ]);

        TrainingParticipant::create([
            'training_event_id' => $training->id,
            'name' => 'Rahmat Hidayat',
            'nip_or_id' => 'DPW-JB-012',
            'category' => 'DPW',
            'agency' => 'DPW SIMPLE Regional 1',
            'phone' => '089911224455',
            'email' => 'rahmat.dpw@simple.go.id',
            'attendance_status' => 'present',
        ]);

        // 8. Schedules
        Schedule::create([
            'title' => 'Briefing Lapangan & Pembagian Wilayah Kerja Petugas',
            'assignment_id' => $assignment->id,
            'officer_id' => $officer1->id,
            'start_time' => now()->addDays(1)->setTime(8, 30),
            'end_time' => now()->addDays(1)->setTime(10, 30),
            'location' => 'Ruang Rapat Utama & Lapangan Sukamaju',
            'notes' => 'Wajib membawa form kuesioner dan tanda pengenal petugas.',
            'status' => 'scheduled',
        ]);

        // 9. Initial Activity Log
        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'setup',
            'module' => 'system',
            'description' => 'Inisialisasi sistem database SIMPLE dan pembuatan akun default.',
            'ip_address' => '127.0.0.1',
        ]);
    }
}
