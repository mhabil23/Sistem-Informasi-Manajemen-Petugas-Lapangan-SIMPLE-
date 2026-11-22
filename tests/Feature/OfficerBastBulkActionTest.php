<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\OfficerBast;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfficerBastBulkActionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected OfficerBast $bast1;
    protected OfficerBast $bast2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'username' => 'test_admin_bast',
            'is_master_admin' => false,
            'status' => 'active',
        ]);

        $officer = User::factory()->create([
            'role' => 'petugas',
            'username' => 'test_officer_bast',
            'status' => 'active',
        ]);

        $assignment = Assignment::create([
            'title' => 'Kegiatan BAST Test',
            'start_date' => now(),
            'end_date' => now()->addDays(5),
            'location' => 'Kec. Lasusua',
            'status' => 'ongoing',
            'created_by' => $this->admin->id,
        ]);

        $this->bast1 = OfficerBast::create([
            'bast_number' => 'BAST-TEST-001',
            'bast_date' => now(),
            'assignment_id' => $assignment->id,
            'officer_id' => $officer->id,
            'bast_type' => 'kantor_ke_petugas',
            'status' => 'draft',
            'description' => 'Tanda terima perlengkapan petugas',
        ]);

        $this->bast2 = OfficerBast::create([
            'bast_number' => 'BAST-TEST-002',
            'bast_date' => now(),
            'assignment_id' => $assignment->id,
            'officer_id' => $officer->id,
            'bast_type' => 'kantor_ke_petugas',
            'status' => 'draft',
            'description' => 'Tanda terima perlengkapan petugas',
        ]);
    }

    public function test_bulk_action_can_set_status_to_finalized(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.basts.officers.bulk-action'), [
            'action' => 'set_finalized',
            'bast_ids' => [$this->bast1->id, $this->bast2->id],
        ]);

        $response->assertRedirect();
        $this->assertEquals('finalized', $this->bast1->fresh()->status);
        $this->assertEquals('finalized', $this->bast2->fresh()->status);
    }

    public function test_bulk_action_can_soft_delete_basts(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.basts.officers.bulk-action'), [
            'action' => 'delete',
            'bast_ids' => [$this->bast1->id, $this->bast2->id],
        ]);

        $response->assertRedirect();
        $this->assertSoftDeleted('officer_basts', ['id' => $this->bast1->id]);
        $this->assertSoftDeleted('officer_basts', ['id' => $this->bast2->id]);
    }
}
