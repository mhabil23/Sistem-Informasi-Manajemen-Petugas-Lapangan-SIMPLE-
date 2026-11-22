<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\TrainingEvent;
use App\Models\TrainingParticipant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AttendanceSheetTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $officer;
    protected Assignment $assignment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin SIMPEL',
            'username' => 'admin_test',
            'email' => 'admin_test@bps.go.id',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->officer = User::factory()->create([
            'name' => 'Budi Santoso',
            'username' => 'budi_test',
            'email' => 'budi_test@bps.go.id',
            'role' => 'petugas',
            'status' => 'active',
            'position' => 'Petugas Pendataan Lapangan',
            'district' => 'Kecamatan Lasusua',
        ]);

        $this->assignment = Assignment::create([
            'title' => 'Survei Pola Konsumsi Rumah Tangga 2026',
            'description' => 'Pendataan SPKI',
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
            'location' => 'Kecamatan Lasusua',
            'status' => 'ongoing',
            'created_by' => $this->admin->id,
        ]);
    }

    public function test_admin_can_access_attendance_index_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.trainings.index'));
        $response->assertStatus(200);
        $response->assertSee('Daftar Hadir');
        $response->assertSee('Buat Daftar Hadir Baru');
        $response->assertSee('Upload Excel Peserta');
    }

    public function test_admin_can_download_attendance_excel_and_csv_templates(): void
    {
        $csvResponse = $this->actingAs($this->admin)->get(route('admin.trainings.template.download', ['format' => 'csv']));
        $csvResponse->assertStatus(200);
        $csvResponse->assertHeader('Content-Type', 'text/csv; charset=utf-8');

        $xlsxResponse = $this->actingAs($this->admin)->get(route('admin.trainings.template.download', ['format' => 'xlsx']));
        $xlsxResponse->assertStatus(200);
        $xlsxResponse->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_admin_can_parse_participants_excel_file(): void
    {
        $csvContent = "NO,Nama Peserta,Jabatan / Unit Kerja,Asal / Instansi\n";
        $csvContent .= "1,\"Ahmad Fauzi\",\"Petugas Lapangan\",\"Kecamatan Lasusua\"\n";
        $csvContent .= "2,\"Siti Rahmawati, S.Tr.Stat\",\"PML\",\"BPS Kolaka Utara\"\n";

        $file = UploadedFile::fake()->createWithContent('peserta.csv', $csvContent);

        $response = $this->actingAs($this->admin)->post(route('admin.trainings.parse-excel'), [
            'excel_file' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'count' => 2,
        ]);
        $participants = $response->json('participants');
        $this->assertEquals('Ahmad Fauzi', $participants[0]['name']);
        $this->assertEquals('Siti Rahmawati, S.Tr.Stat', $participants[1]['name']);
    }

    public function test_admin_can_store_and_update_attendance_document_with_participants(): void
    {
        $payload = [
            'document_number' => 'DH-BPS/2026/XI/0099',
            'document_title' => 'DAFTAR HADIR PESERTA',
            'activity_subtitle' => 'Briefing Petugas Sensus Pertanian 2026',
            'institution_name' => 'BPS Kabupaten Kolaka Utara',
            'document_place' => 'Hotel Utama Lasusua',
            'attendance_date' => '2026-11-20',
            'assignment_id' => $this->assignment->id,
            'maker_name' => 'Galang Gemilang, A.Md.Stat.',
            'maker_nip' => '199505052020121001',
            'notes' => 'Seluruh petugas hadir tepat waktu.',
            'participants' => [
                [
                    'name' => 'Budi Santoso',
                    'category' => 'PCL',
                    'agency' => 'Kecamatan Lasusua',
                ],
                [
                    'name' => 'Nurul Hidayah',
                    'category' => 'PML',
                    'agency' => 'Kecamatan Lambai',
                ],
            ],
        ];

        // 1. Store
        $storeResponse = $this->actingAs($this->admin)->post(route('admin.trainings.store'), $payload);
        $storeResponse->assertRedirect(route('admin.trainings.index'));

        $this->assertDatabaseHas('training_events', [
            'document_number' => 'DH-BPS/2026/XI/0099',
            'activity_subtitle' => 'Briefing Petugas Sensus Pertanian 2026',
            'document_place' => 'Hotel Utama Lasusua',
        ]);

        $training = TrainingEvent::where('document_number', 'DH-BPS/2026/XI/0099')->first();
        $this->assertCount(2, $training->participants);

        // 2. Preview PDF
        $previewResponse = $this->actingAs($this->admin)->get(route('admin.trainings.preview', $training));
        $previewResponse->assertStatus(200);
        $this->assertEquals('application/pdf', $previewResponse->headers->get('content-type'));

        // 3. Download PDF
        $pdfResponse = $this->actingAs($this->admin)->get(route('admin.trainings.attendance.pdf', $training));
        $pdfResponse->assertStatus(200);
        $this->assertEquals('application/pdf', $pdfResponse->headers->get('content-type'));

        // 4. Update
        $existingP1 = $training->participants()->first();
        $updatePayload = $payload;
        $updatePayload['activity_subtitle'] = 'Briefing Petugas Sensus Pertanian 2026 (Revisi)';
        $updatePayload['participants'] = [
            [
                'id' => $existingP1->id,
                'name' => 'Budi Santoso, S.E.',
                'category' => 'Koordinator Lapangan',
                'agency' => 'Kecamatan Lasusua',
            ],
            [
                'name' => 'Dewi Lestari',
                'category' => 'Enumerator',
                'agency' => 'Kecamatan Wawo',
            ],
        ];

        $updateResponse = $this->actingAs($this->admin)->put(route('admin.trainings.update', $training), $updatePayload);
        $updateResponse->assertRedirect(route('admin.trainings.index'));

        $training->refresh();
        $this->assertEquals('Briefing Petugas Sensus Pertanian 2026 (Revisi)', $training->activity_subtitle);
        $this->assertCount(2, $training->participants);
        $this->assertDatabaseHas('training_participants', [
            'name' => 'Budi Santoso, S.E.',
            'category' => 'Koordinator Lapangan',
        ]);
        $this->assertDatabaseHas('training_participants', [
            'name' => 'Dewi Lestari',
        ]);
    }
}
