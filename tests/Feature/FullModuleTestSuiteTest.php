<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\DocumentTemplate;
use App\Models\OfficerReport;
use App\Models\Respondent;
use App\Models\TrainingEvent;
use App\Models\TrainingParticipant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FullModuleTestSuiteTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $petugas;
    protected Assignment $assignment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Administrator SIMPLE',
            'username' => 'admin_test',
            'email' => 'admin@test.com',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->petugas = User::factory()->create([
            'name' => 'Petugas Satu',
            'username' => 'petugas_test',
            'email' => 'petugas@test.com',
            'role' => 'petugas',
            'status' => 'active',
        ]);

        $this->assignment = Assignment::create([
            'title' => 'Kegiatan Bimtek & Pendataan',
            'description' => 'Pendataan sarpras pertanian',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
            'location' => 'Malang',
            'status' => 'ongoing',
            'created_by' => $this->admin->id,
        ]);
    }

    public function test_admin_and_petugas_reports_workflow()
    {
        // 1. Petugas submits a report
        $createResponse = $this->actingAs($this->petugas)->post(route('petugas.reports.store'), [
            'activity_date' => now()->toDateString(),
            'activity_title' => 'Survei Alsintan Desa Karangpandan',
            'assignment_id' => $this->assignment->id,
            'work_description' => 'Telah dilakukan verifikasi traktor roda 4',
            'action_type' => 'submitted',
        ]);

        $createResponse->assertRedirect(route('petugas.reports.index'));
        $this->assertDatabaseHas('officer_reports', [
            'activity_title' => 'Survei Alsintan Desa Karangpandan',
            'status' => 'submitted',
        ]);

        $report = OfficerReport::where('activity_title', 'Survei Alsintan Desa Karangpandan')->first();

        // 2. Admin views report list
        $adminListResponse = $this->actingAs($this->admin)->get(route('admin.reports.index'));
        $adminListResponse->assertStatus(200);
        $adminListResponse->assertSee('Survei Alsintan Desa Karangpandan');

        // 3. Admin reviews and approves report
        $reviewResponse = $this->actingAs($this->admin)->put(route('admin.reports.review', $report), [
            'status' => 'approved',
            'admin_notes' => 'Laporan sangat lengkap dan valid.',
        ]);

        $reviewResponse->assertSessionHas('success');
        $this->assertDatabaseHas('officer_reports', [
            'id' => $report->id,
            'status' => 'approved',
            'reviewed_by' => $this->admin->id,
        ]);
    }

    public function test_training_management_and_attendance_pdf()
    {
        // 1. Admin creates training event
        $createTrainingResponse = $this->actingAs($this->admin)->post(route('admin.trainings.store'), [
            'title' => 'Bimbingan Teknis SPKRT Angkatan 1',
            'description' => 'Pelatihan enumerator',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'location' => 'Aula Gedung Utama',
            'quota' => 40,
            'status' => 'upcoming',
        ]);

        $createTrainingResponse->assertRedirect(route('admin.trainings.index'));
        $this->assertDatabaseHas('training_events', [
            'title' => 'Bimbingan Teknis SPKRT Angkatan 1',
        ]);

        $training = TrainingEvent::where('title', 'Bimbingan Teknis SPKRT Angkatan 1')->first();

        // 2. Admin adds participant
        $addParticipantResponse = $this->actingAs($this->admin)->post(route('admin.trainings.participants.store', $training), [
            'name' => 'Ahmad Fauzi',
            'nip_or_id' => '199001012015011001',
            'category' => 'DPW',
            'agency' => 'Dinas Ketahanan Pangan',
            'phone' => '081234567890',
            'attendance_status' => 'present',
        ]);

        $addParticipantResponse->assertSessionHas('success');
        $this->assertDatabaseHas('training_participants', [
            'training_event_id' => $training->id,
            'name' => 'Ahmad Fauzi',
            'category' => 'DPW',
        ]);

        // 3. Admin accesses training show view
        $showResponse = $this->actingAs($this->admin)->get(route('admin.trainings.show', $training));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Ahmad Fauzi');
        $showResponse->assertSee('DPW');

        // 4. Admin downloads attendance PDF
        $pdfResponse = $this->actingAs($this->admin)->get(route('admin.trainings.attendance.pdf', $training));
        $pdfResponse->assertStatus(200);
        $this->assertEquals('application/pdf', $pdfResponse->headers->get('content-type'));
    }

    public function test_document_template_crud()
    {
        $createResponse = $this->actingAs($this->admin)->post(route('admin.templates.store'), [
            'code' => 'bast_serah_terima_v1',
            'name' => 'Template BAST Serah Terima Sarpras',
            'type' => 'bast',
            'header_text' => 'Kop Pemkab',
            'content' => 'Telah diserahterimakan dokumen pada {TANGGAL} kepada {NAMA}.',
            'footer_text' => 'Arsip Resmi',
            'is_active' => '1',
        ]);

        $createResponse->assertSessionHas('success');
        $this->assertDatabaseHas('document_templates', [
            'code' => 'bast_serah_terima_v1',
        ]);

        $indexResponse = $this->actingAs($this->admin)->get(route('admin.templates.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('bast_serah_terima_v1');
    }

    public function test_recapitulation_and_exports()
    {
        // Create respondent for recap
        Respondent::create([
            'name' => 'Responden Gapoktan',
            'address' => 'Jl. Mawar No. 4',
            'phone' => '0812345678',
            'agency' => 'Gapoktan Subur',
            'purpose' => 'Survei',
            'assignment_id' => $this->assignment->id,
            'officer_id' => $this->petugas->id,
            'input_date' => now()->toDateString(),
        ]);

        // 1. View recap index
        $indexResponse = $this->actingAs($this->admin)->get(route('admin.recapitulation.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Rekapitulasi');
        $indexResponse->assertSee('Responden Gapoktan');

        // 2. Export PDF
        $pdfResponse = $this->actingAs($this->admin)->get(route('admin.recapitulation.pdf'));
        $pdfResponse->assertStatus(200);
        $this->assertEquals('application/pdf', $pdfResponse->headers->get('content-type'));

        // 3. Export Excel CSV
        $excelResponse = $this->actingAs($this->admin)->get(route('admin.recapitulation.excel', ['dataset' => 'respondents']));
        $excelResponse->assertStatus(200);
        $this->assertStringContainsString('text/csv', $excelResponse->headers->get('content-type'));
    }

    public function test_activity_log_viewer()
    {
        ActivityLog::log(
            'create',
            'users',
            'Administrator membuat akun uji coba.'
        );

        $response = $this->actingAs($this->admin)->get(route('admin.logs.index'));
        $response->assertStatus(200);
        $response->assertSee('Administrator membuat akun uji coba.');
    }
}

