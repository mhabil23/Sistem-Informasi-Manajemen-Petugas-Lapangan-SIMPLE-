<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\AssignmentOfficer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentBulkActionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $officer;
    protected Assignment $assignment1;
    protected Assignment $assignment2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'username' => 'test_admin_assignment',
            'is_master_admin' => false,
            'status' => 'active',
        ]);

        $this->officer = User::factory()->create([
            'role' => 'petugas',
            'username' => 'test_officer_assignment',
            'status' => 'active',
        ]);

        $this->assignment1 = Assignment::create([
            'title' => 'Kegiatan Lapangan Alpha',
            'start_date' => now(),
            'end_date' => now()->addDays(5),
            'location' => 'Kec. Lasusua',
            'status' => 'draft',
            'created_by' => $this->admin->id,
        ]);

        $this->assignment2 = Assignment::create([
            'title' => 'Kegiatan Lapangan Beta',
            'start_date' => now(),
            'end_date' => now()->addDays(10),
            'location' => 'Kec. Pakue',
            'status' => 'draft',
            'created_by' => $this->admin->id,
        ]);
    }

    public function test_bulk_action_can_set_status_to_ongoing(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.assignments.bulk-action'), [
            'action' => 'set_ongoing',
            'assignment_ids' => [$this->assignment1->id, $this->assignment2->id],
        ]);

        $response->assertRedirect();
        $this->assertEquals('ongoing', $this->assignment1->fresh()->status);
        $this->assertEquals('ongoing', $this->assignment2->fresh()->status);
    }

    public function test_bulk_action_can_set_status_to_completed(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.assignments.bulk-action'), [
            'action' => 'set_completed',
            'assignment_ids' => [$this->assignment1->id, $this->assignment2->id],
        ]);

        $response->assertRedirect();
        $this->assertEquals('completed', $this->assignment1->fresh()->status);
        $this->assertEquals('completed', $this->assignment2->fresh()->status);
    }

    public function test_bulk_action_can_set_status_to_draft(): void
    {
        $this->assignment1->update(['status' => 'ongoing']);
        $this->assignment2->update(['status' => 'completed']);

        $response = $this->actingAs($this->admin)->post(route('admin.assignments.bulk-action'), [
            'action' => 'set_draft',
            'assignment_ids' => [$this->assignment1->id, $this->assignment2->id],
        ]);

        $response->assertRedirect();
        $this->assertEquals('draft', $this->assignment1->fresh()->status);
        $this->assertEquals('draft', $this->assignment2->fresh()->status);
    }

    public function test_bulk_action_can_assign_officer_to_multiple_assignments(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.assignments.bulk-action'), [
            'action' => 'assign_officers',
            'assignment_ids' => [$this->assignment1->id, $this->assignment2->id],
            'officer_id' => $this->officer->id,
            'specific_task' => 'Pencacahan Lapangan Sensus',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('assignment_officers', [
            'assignment_id' => $this->assignment1->id,
            'user_id' => $this->officer->id,
            'specific_task' => 'Pencacahan Lapangan Sensus',
        ]);
        $this->assertDatabaseHas('assignment_officers', [
            'assignment_id' => $this->assignment2->id,
            'user_id' => $this->officer->id,
            'specific_task' => 'Pencacahan Lapangan Sensus',
        ]);
    }

    public function test_bulk_action_can_assign_multiple_officers_to_multiple_assignments(): void
    {
        $pegawai = User::factory()->create([
            'role' => 'pegawai',
            'username' => 'test_pegawai_bulk_asgn',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.assignments.bulk-action'), [
            'action' => 'assign_officers',
            'assignment_ids' => [$this->assignment1->id, $this->assignment2->id],
            'officer_ids' => [$this->officer->id, $pegawai->id],
            'specific_task' => 'Pengawasan & Pencacahan',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('assignment_officers', [
            'assignment_id' => $this->assignment1->id,
            'user_id' => $pegawai->id,
            'specific_task' => 'Pengawasan & Pencacahan',
        ]);
        $this->assertDatabaseHas('assignment_officers', [
            'assignment_id' => $this->assignment2->id,
            'user_id' => $pegawai->id,
            'specific_task' => 'Pengawasan & Pencacahan',
        ]);
    }

    public function test_bulk_action_can_soft_delete_multiple_assignments(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.assignments.bulk-action'), [
            'action' => 'delete',
            'assignment_ids' => [$this->assignment1->id, $this->assignment2->id],
        ]);

        $response->assertRedirect();
        $this->assertSoftDeleted('assignments', ['id' => $this->assignment1->id]);
        $this->assertSoftDeleted('assignments', ['id' => $this->assignment2->id]);
    }
}
