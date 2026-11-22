<?php

namespace Tests\Feature;

use App\Models\OfficerReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportBulkActionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected OfficerReport $report1;
    protected OfficerReport $report2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'username' => 'test_admin_rep',
            'is_master_admin' => false,
            'status' => 'active',
        ]);

        $officer = User::factory()->create([
            'role' => 'petugas',
            'username' => 'test_officer_rep',
            'status' => 'active',
        ]);

        $this->report1 = OfficerReport::create([
            'user_id' => $officer->id,
            'officer_name' => $officer->name,
            'activity_title' => 'Laporan Lapangan 1',
            'activity_date' => now(),
            'work_description' => 'Hasil wawancara 1',
            'status' => 'submitted',
        ]);

        $this->report2 = OfficerReport::create([
            'user_id' => $officer->id,
            'officer_name' => $officer->name,
            'activity_title' => 'Laporan Lapangan 2',
            'activity_date' => now(),
            'work_description' => 'Hasil wawancara 2',
            'status' => 'submitted',
        ]);
    }

    public function test_bulk_action_can_approve_reports(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.reports.bulk-action'), [
            'action' => 'approve',
            'report_ids' => [$this->report1->id, $this->report2->id],
        ]);

        $response->assertRedirect();
        $this->assertEquals('approved', $this->report1->fresh()->status);
        $this->assertEquals('approved', $this->report2->fresh()->status);
    }

    public function test_bulk_action_can_delete_reports(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.reports.bulk-action'), [
            'action' => 'delete',
            'report_ids' => [$this->report1->id, $this->report2->id],
        ]);

        $response->assertRedirect();
        $this->assertSoftDeleted('officer_reports', ['id' => $this->report1->id]);
        $this->assertSoftDeleted('officer_reports', ['id' => $this->report2->id]);
    }
}
