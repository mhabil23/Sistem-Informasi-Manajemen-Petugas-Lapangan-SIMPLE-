<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\AssignmentOfficer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetugasAssignmentBulkProgressTest extends TestCase
{
    use RefreshDatabase;

    protected User $officer;
    protected AssignmentOfficer $task1;
    protected AssignmentOfficer $task2;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::factory()->create([
            'role' => 'admin',
            'username' => 'test_admin_pet_asgn',
            'status' => 'active',
        ]);

        $this->officer = User::factory()->create([
            'role' => 'petugas',
            'username' => 'test_officer_pet_asgn',
            'status' => 'active',
        ]);

        $assignment1 = Assignment::create([
            'title' => 'Tugas Lapangan 1',
            'start_date' => now(),
            'end_date' => now()->addDays(3),
            'location' => 'Kec. Lasusua',
            'status' => 'ongoing',
            'created_by' => $admin->id,
        ]);

        $assignment2 = Assignment::create([
            'title' => 'Tugas Lapangan 2',
            'start_date' => now(),
            'end_date' => now()->addDays(5),
            'location' => 'Kec. Pakue',
            'status' => 'ongoing',
            'created_by' => $admin->id,
        ]);

        $this->task1 = AssignmentOfficer::create([
            'assignment_id' => $assignment1->id,
            'user_id' => $this->officer->id,
            'specific_task' => 'Pencacahan 1',
            'status' => 'pending',
        ]);

        $this->task2 = AssignmentOfficer::create([
            'assignment_id' => $assignment2->id,
            'user_id' => $this->officer->id,
            'specific_task' => 'Pencacahan 2',
            'status' => 'pending',
        ]);
    }

    public function test_officer_can_bulk_update_task_status_to_in_progress(): void
    {
        $response = $this->actingAs($this->officer)->post(route('petugas.assignments.bulk-progress'), [
            'status' => 'in_progress',
            'task_ids' => [$this->task1->id, $this->task2->id],
        ]);

        $response->assertRedirect();
        $this->assertEquals('in_progress', $this->task1->fresh()->status);
        $this->assertEquals('in_progress', $this->task2->fresh()->status);
    }

    public function test_officer_can_bulk_update_task_status_to_completed(): void
    {
        $response = $this->actingAs($this->officer)->post(route('petugas.assignments.bulk-progress'), [
            'status' => 'completed',
            'task_ids' => [$this->task1->id, $this->task2->id],
        ]);

        $response->assertRedirect();
        $this->assertEquals('completed', $this->task1->fresh()->status);
        $this->assertEquals('completed', $this->task2->fresh()->status);
    }
}
