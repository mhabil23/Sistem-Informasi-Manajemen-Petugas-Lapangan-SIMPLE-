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

    public function test_public_user_can_access_attendance_page_via_token(): void
    {
        $training = TrainingEvent::create([
            'title' => 'Pelatihan Petugas SPKRT 2026',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'location' => 'Aula BPS Kab. Kolaka',
            'created_by' => $this->admin->id,
        ]);

        $p1 = TrainingParticipant::create([
            'training_event_id' => $training->id,
            'name' => 'Peserta Test 1',
            'category' => 'PCL',
            'attendance_status' => 'absent',
        ]);

        $response = $this->get(route('attendance.public.show', $training->public_token));
        $response->assertStatus(200);
        $response->assertSee('Pelatihan Petugas SPKRT 2026');
        $response->assertSee('Peserta Test 1');
    }

    public function test_public_participant_can_submit_signature(): void
    {
        $training = TrainingEvent::create([
            'title' => 'Pelatihan Petugas SPKRT 2026',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'location' => 'Aula BPS Kab. Kolaka',
            'created_by' => $this->admin->id,
        ]);

        $participant = TrainingParticipant::create([
            'training_event_id' => $training->id,
            'name' => 'Peserta Hadir Langsung',
            'category' => 'PCL',
            'attendance_status' => 'absent',
        ]);

        // 1x1 transparent PNG base64
        $dummySignature = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';

        $response = $this->post(route('attendance.public.submit', $training->public_token), [
            'participant_id' => $participant->id,
            'signature_data' => $dummySignature,
        ]);

        $response->assertRedirect(route('attendance.public.success', [
            'token' => $training->public_token,
            'p' => $participant->id,
        ]));

        $participant->refresh();
        $this->assertEquals('present', $participant->attendance_status);
        $this->assertNotNull($participant->signature_path);
        $this->assertNotNull($participant->signed_at);

        // Success page check
        $successResponse = $this->get(route('attendance.public.success', [
            'token' => $training->public_token,
            'p' => $participant->id,
        ]));
        $successResponse->assertStatus(200);
        $successResponse->assertSee('PRESENSI BERHASIL TERCATAT');
        $successResponse->assertSee('Peserta Hadir Langsung');
    }

    public function test_public_attendance_returns_404_for_invalid_token(): void
    {
        $response = $this->get('/presensi/token-palsu-ngawur-123');
        $response->assertStatus(404);
    }

    public function test_cannot_sign_again_if_participant_already_has_signature(): void
    {
        $training = TrainingEvent::create([
            'title' => 'Pelatihan Petugas SPKRT 2026',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'location' => 'Aula BPS Kab. Kolaka',
            'created_by' => $this->admin->id,
        ]);

        $participant = TrainingParticipant::create([
            'training_event_id' => $training->id,
            'name' => 'Peserta Sudah Hadir',
            'category' => 'PCL',
            'signature_path' => 'signatures/dummy.png',
            'attendance_status' => 'present',
        ]);

        $dummySignature = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';

        $response = $this->post(route('attendance.public.submit', $training->public_token), [
            'participant_id' => $participant->id,
            'signature_data' => $dummySignature,
        ]);

        $response->assertSessionHasErrors(['participant_id']);
    }

    public function test_device_with_cookie_cannot_submit_again_for_other_participant(): void
    {
        $training = TrainingEvent::create([
            'title' => 'Pelatihan Petugas SPKRT 2026',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'location' => 'Aula BPS Kab. Kolaka',
            'created_by' => $this->admin->id,
        ]);

        $p1 = TrainingParticipant::create([
            'training_event_id' => $training->id,
            'name' => 'Peserta Pertama',
            'category' => 'PCL',
            'signature_path' => 'signatures/dummy1.png',
            'attendance_status' => 'present',
        ]);

        $p2 = TrainingParticipant::create([
            'training_event_id' => $training->id,
            'name' => 'Peserta Kedua',
            'category' => 'PCL',
            'attendance_status' => 'absent',
        ]);

        $dummySignature = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';

        $response = $this->withCookie('signed_dh_' . $training->public_token, (string) $p1->id)
            ->post(route('attendance.public.submit', $training->public_token), [
                'participant_id' => $p2->id,
                'signature_data' => $dummySignature,
            ]);

        $response->assertSessionHasErrors(['error']);
    }
}
