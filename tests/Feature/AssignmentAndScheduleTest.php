<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\AssignmentOfficer;
use App\Models\Schedule;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentAndScheduleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_view_assignments_index(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.assignments.index'));
        $response->assertStatus(200);
        $response->assertSee('Kegiatan');
        $response->assertSee('Total Kegiatan');
        $response->assertSee('Buat Kegiatan Baru');
    }

    public function test_admin_can_create_assignment(): void
    {
        $admin = User::where('role', 'admin')->first();
        $officer = User::where('role', 'petugas')->first();

        $response = $this->actingAs($admin)->post(route('admin.assignments.store'), [
            'title' => 'Verifikasi Lapangan Usaha Agribisnis 2026',
            'description' => 'Pendataan terpadu petani dan UMKM lokal.',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(10)->toDateString(),
            'location' => 'Kecamatan Makmur',
            'status' => 'ongoing',
            'officer_ids' => [$officer->id],
        ]);

        $this->assertDatabaseHas('assignments', [
            'title' => 'Verifikasi Lapangan Usaha Agribisnis 2026',
            'location' => 'Kecamatan Makmur',
        ]);

        $createdAssignment = Assignment::where('title', 'Verifikasi Lapangan Usaha Agribisnis 2026')->first();
        $response->assertRedirect(route('admin.assignments.show', $createdAssignment));

        $this->assertDatabaseHas('assignment_officers', [
            'assignment_id' => $createdAssignment->id,
            'user_id' => $officer->id,
        ]);
    }

    public function test_admin_can_assign_officer_with_specific_task(): void
    {
        $admin = User::where('role', 'admin')->first();
        $assignment = Assignment::first();
        $officer = User::where('role', 'petugas')->orderBy('id', 'desc')->first();

        // Remove if already assigned to test clean assignment
        AssignmentOfficer::where('assignment_id', $assignment->id)->where('user_id', $officer->id)->delete();

        $response = $this->actingAs($admin)->post(route('admin.assignments.officers.assign', $assignment), [
            'user_id' => $officer->id,
            'specific_task' => 'Pendataan 20 responden di Desa Makmur Blok Barat.',
            'progress_notes' => 'Siap mulai besok pagi.',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('assignment_officers', [
            'assignment_id' => $assignment->id,
            'user_id' => $officer->id,
            'specific_task' => 'Pendataan 20 responden di Desa Makmur Blok Barat.',
        ]);
    }

    public function test_petugas_can_view_assigned_tasks_and_update_progress(): void
    {
        $officer = User::where('role', 'petugas')->first();
        $task = AssignmentOfficer::where('user_id', $officer->id)->first();

        $response = $this->actingAs($officer)->get(route('petugas.assignments.index'));
        $response->assertStatus(200);
        $response->assertSee('Daftar Penugasan Kegiatan');

        $updateResponse = $this->actingAs($officer)->put(route('petugas.assignments.progress.update', $task), [
            'status' => 'in_progress',
            'progress_notes' => '10 kuesioner berhasil diselesaikan hari ini.',
        ]);

        $updateResponse->assertSessionHas('success');
        $this->assertEquals('in_progress', $task->fresh()->status);
        $this->assertEquals('10 kuesioner berhasil diselesaikan hari ini.', $task->fresh()->progress_notes);
    }

    public function test_admin_can_manage_schedules(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.schedules.index'));
        $response->assertStatus(200);
        $response->assertSee('Jadwal Kegiatan');

        $createResponse = $this->actingAs($admin)->post(route('admin.schedules.store'), [
            'title' => 'Rapat Evaluasi Mingguan Petugas',
            'start_time' => now()->addDays(2)->setTime(9, 0)->format('Y-m-d H:i:s'),
            'end_time' => now()->addDays(2)->setTime(11, 0)->format('Y-m-d H:i:s'),
            'location' => 'Ruang Command Center SP4RT/SIMPLE',
            'status' => 'scheduled',
        ]);

        $createResponse->assertRedirect(route('admin.schedules.index'));
        $this->assertDatabaseHas('schedules', [
            'title' => 'Rapat Evaluasi Mingguan Petugas',
        ]);
    }

    public function test_petugas_can_view_own_schedules(): void
    {
        $officer = User::where('role', 'petugas')->first();

        $response = $this->actingAs($officer)->get(route('petugas.schedules.index'));
        $response->assertStatus(200);
        $response->assertSee('Agenda Lapangan Saya');
    }
}
