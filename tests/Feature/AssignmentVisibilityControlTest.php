<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\AssignmentOfficer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentVisibilityControlTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $petugas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'username' => 'admin_vis_' . uniqid(),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->petugas = User::factory()->create([
            'username' => 'petugas_vis_' . uniqid(),
            'role' => 'petugas',
            'status' => 'active',
        ]);
    }

    public function test_visibility_statuses_and_pseudo_logic_work_accurately(): void
    {
        // 1. Aktif Manual (Admin) - even outside date range
        $manual = Assignment::create([
            'title' => 'Penugasan Manual',
            'start_date' => Carbon::today()->subDays(20),
            'end_date' => Carbon::today()->subDays(10),
            'location' => 'Kec. Lasusua',
            'status' => 'ongoing',
            'is_enabled_by_admin' => true,
            'created_by' => $this->admin->id,
        ]);

        // 2. Aktif Periode - within date range, admin toggle OFF
        $periodActive = Assignment::create([
            'title' => 'Penugasan Periode',
            'start_date' => Carbon::today()->subDays(2),
            'end_date' => Carbon::today()->addDays(5),
            'location' => 'Kec. Pakue',
            'status' => 'ongoing',
            'is_enabled_by_admin' => false,
            'created_by' => $this->admin->id,
        ]);

        // 3. Nonaktif - future start_date, admin toggle OFF
        $inactive = Assignment::create([
            'title' => 'Penugasan Belum Mulai',
            'start_date' => Carbon::today()->addDays(5),
            'end_date' => Carbon::today()->addDays(15),
            'location' => 'Kec. Kodeoha',
            'status' => 'ongoing',
            'is_enabled_by_admin' => false,
            'created_by' => $this->admin->id,
        ]);

        // 4. Berakhir - past end_date, admin toggle OFF
        $ended = Assignment::create([
            'title' => 'Penugasan Berakhir',
            'start_date' => Carbon::today()->subDays(15),
            'end_date' => Carbon::today()->subDays(3),
            'location' => 'Kec. Watunohu',
            'status' => 'ongoing',
            'is_enabled_by_admin' => false,
            'created_by' => $this->admin->id,
        ]);

        $this->assertEquals('Aktif Manual (Admin)', $manual->visibility_status_label);
        $this->assertTrue($manual->shouldBeShownToOfficer());

        $this->assertEquals('Aktif Periode', $periodActive->visibility_status_label);
        $this->assertTrue($periodActive->shouldBeShownToOfficer());

        $this->assertEquals('Nonaktif', $inactive->visibility_status_label);
        $this->assertFalse($inactive->shouldBeShownToOfficer());

        $this->assertEquals('Berakhir', $ended->visibility_status_label);
        $this->assertFalse($ended->shouldBeShownToOfficer());
    }

    public function test_petugas_dashboard_only_shows_assignments_matching_admin_or_period_rule(): void
    {
        $manual = Assignment::create([
            'title' => 'Tugas Aktif Manual Luar Periode',
            'start_date' => Carbon::today()->subDays(30),
            'end_date' => Carbon::today()->subDays(20),
            'location' => 'Kec. A',
            'status' => 'ongoing',
            'is_enabled_by_admin' => true,
            'created_by' => $this->admin->id,
        ]);

        $period = Assignment::create([
            'title' => 'Tugas Aktif Periode Ini',
            'start_date' => Carbon::today()->subDay(),
            'end_date' => Carbon::today()->addDays(5),
            'location' => 'Kec. B',
            'status' => 'ongoing',
            'is_enabled_by_admin' => false,
            'created_by' => $this->admin->id,
        ]);

        $hidden = Assignment::create([
            'title' => 'Tugas Tersembunyi Kadaluarsa',
            'start_date' => Carbon::today()->subDays(10),
            'end_date' => Carbon::today()->subDays(2),
            'location' => 'Kec. C',
            'status' => 'ongoing',
            'is_enabled_by_admin' => false,
            'created_by' => $this->admin->id,
        ]);

        foreach ([$manual, $period, $hidden] as $asgn) {
            AssignmentOfficer::create([
                'assignment_id' => $asgn->id,
                'user_id' => $this->petugas->id,
                'status' => 'in_progress',
            ]);
        }

        $response = $this->actingAs($this->petugas)->get(route('petugas.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Tugas Aktif Manual Luar Periode');
        $response->assertSee('Tugas Aktif Periode Ini');
        $response->assertDontSee('Tugas Tersembunyi Kadaluarsa');
    }

    public function test_global_admin_disable_hides_all_assignments_without_deleting_data(): void
    {
        $assignment = Assignment::create([
            'title' => 'Tugas Sedang Berjalan',
            'start_date' => Carbon::today()->subDay(),
            'end_date' => Carbon::today()->addDays(5),
            'location' => 'Kec. Lasusua',
            'status' => 'ongoing',
            'is_enabled_by_admin' => true,
            'created_by' => $this->admin->id,
        ]);

        AssignmentOfficer::create([
            'assignment_id' => $assignment->id,
            'user_id' => $this->petugas->id,
            'status' => 'pending',
        ]);

        // Admin disables all assignments globally
        $toggleRes = $this->actingAs($this->admin)->patch(route('admin.assignments.global-toggle'), [
            'enabled' => '0',
        ]);
        $toggleRes->assertSessionHas('success');
        $this->assertFalse(Assignment::isGlobalPortalEnabled());

        // Petugas dashboard hides "Penugasan Lapangan Anda" section when no active tasks are open
        $dashRes = $this->actingAs($this->petugas)->get(route('petugas.dashboard'));
        $dashRes->assertStatus(200);
        $dashRes->assertDontSee('Tugas Sedang Berjalan');
        $dashRes->assertDontSee('Penugasan Lapangan Anda');

        // Dedicated Tugas menu still displays the admin-closed message
        $tugasRes = $this->actingAs($this->petugas)->get(route('petugas.assignments.index'));
        $tugasRes->assertStatus(200);
        $tugasRes->assertSee('Penugasan lapangan belum dibuka oleh Admin.');

        // Data still exists in DB and Admin can still see it
        $this->assertDatabaseHas('assignments', ['id' => $assignment->id, 'title' => 'Tugas Sedang Berjalan']);
        $adminRes = $this->actingAs($this->admin)->get(route('admin.assignments.index'));
        $adminRes->assertStatus(200);
        $adminRes->assertSee('Tugas Sedang Berjalan');

        // Admin re-enables globally -> "Penugasan Lapangan Anda" and assignment reappear on dashboard
        $this->actingAs($this->admin)->patch(route('admin.assignments.global-toggle'), [
            'enabled' => '1',
        ]);
        $this->assertTrue(Assignment::isGlobalPortalEnabled());

        $dashRes2 = $this->actingAs($this->petugas)->get(route('petugas.dashboard'));
        $dashRes2->assertSee('Penugasan Lapangan Anda');
        $dashRes2->assertSee('Tugas Sedang Berjalan');
    }

    public function test_petugas_sees_empty_message_when_global_on_but_no_assignments_active(): void
    {
        $expired = Assignment::create([
            'title' => 'Tugas Lama Selesai Periode',
            'start_date' => Carbon::today()->subDays(14),
            'end_date' => Carbon::today()->subDays(1),
            'location' => 'Kec. Lasusua',
            'status' => 'ongoing',
            'is_enabled_by_admin' => false,
            'created_by' => $this->admin->id,
        ]);

        AssignmentOfficer::create([
            'assignment_id' => $expired->id,
            'user_id' => $this->petugas->id,
            'status' => 'in_progress',
        ]);

        // Dashboard omits "Penugasan Lapangan Anda" when no active assignment exists
        $dashRes = $this->actingAs($this->petugas)->get(route('petugas.dashboard'));
        $dashRes->assertStatus(200);
        $dashRes->assertDontSee('Tugas Lama Selesai Periode');
        $dashRes->assertDontSee('Penugasan Lapangan Anda');

        // Dedicated Tugas menu still shows the empty-state message
        $tugasRes = $this->actingAs($this->petugas)->get(route('petugas.assignments.index'));
        $tugasRes->assertStatus(200);
        $tugasRes->assertSee('Belum ada penugasan lapangan yang tersedia saat ini.');
    }

    public function test_admin_can_toggle_individual_assignment_update_period_and_view_history(): void
    {
        $assignment = Assignment::create([
            'title' => 'Tugas Pengawasan Test',
            'start_date' => Carbon::today()->subDays(10),
            'end_date' => Carbon::today()->subDays(5),
            'location' => 'Kec. Ranteangin',
            'status' => 'ongoing',
            'is_enabled_by_admin' => false,
            'created_by' => $this->admin->id,
        ]);

        // Toggle ON manually by Admin
        $this->actingAs($this->admin)
            ->patch(route('admin.assignments.toggle-admin', $assignment))
            ->assertSessionHas('success');

        $this->assertTrue($assignment->fresh()->is_enabled_by_admin);
        $this->assertEquals('Aktif Manual (Admin)', $assignment->fresh()->visibility_status_label);

        // Update period to active dates & turn manual OFF
        $this->actingAs($this->admin)
            ->patch(route('admin.assignments.update-period', $assignment), [
                'start_date' => Carbon::today()->toDateString(),
                'end_date' => Carbon::today()->addDays(7)->toDateString(),
                'is_enabled_by_admin' => '0',
            ])
            ->assertSessionHas('success');

        $fresh = $assignment->fresh();
        $this->assertFalse($fresh->is_enabled_by_admin);
        $this->assertEquals('Aktif Periode', $fresh->visibility_status_label);

        // Admin can view history page
        $this->actingAs($this->admin)
            ->get(route('admin.assignments.history'))
            ->assertStatus(200)
            ->assertSee('Riwayat');

        // Petugas cannot modify Admin controls (RoleMiddleware redirects back to petugas.dashboard)
        $this->actingAs($this->petugas)
            ->patch(route('admin.assignments.global-toggle'), ['enabled' => '0'])
            ->assertRedirect(route('petugas.dashboard'));
        $this->actingAs($this->petugas)
            ->patch(route('admin.assignments.toggle-admin', $assignment))
            ->assertRedirect(route('petugas.dashboard'));
        $this->actingAs($this->petugas)
            ->patch(route('admin.assignments.update-period', $assignment), [
                'start_date' => Carbon::today()->toDateString(),
                'end_date' => Carbon::today()->addDays(7)->toDateString(),
            ])
            ->assertRedirect(route('petugas.dashboard'));
    }
}
