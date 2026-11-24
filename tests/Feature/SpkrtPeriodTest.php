<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Respondent;
use App\Models\SpkrtPeriod;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpkrtPeriodTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $petugas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'username' => 'admin_spkrt_' . uniqid(),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->petugas = User::factory()->create([
            'username' => 'petugas_spkrt_' . uniqid(),
            'role' => 'petugas',
            'status' => 'active',
        ]);
    }

    /**
     * Req 1 & 2: Admin dapat membuat periode pengisian SPKRT dengan nama, tanggal mulai, tanggal selesai, status aktif.
     */
    public function test_admin_can_create_spkrt_period(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.spkrt-periods.store'), [
            'activity_name' => 'Survei Pertanian SPKRT 2026',
            'start_date' => Carbon::today()->toDateString(),
            'end_date' => Carbon::today()->addDays(14)->toDateString(),
            'is_active' => '1',
            'description' => 'Pendataan serah terima dokumen responden',
        ]);

        $response->assertRedirect(route('admin.spkrt-periods.index'));
        $this->assertDatabaseHas('spkrt_periods', [
            'activity_name' => 'Survei Pertanian SPKRT 2026',
            'is_active' => 1,
            'created_by' => $this->admin->id,
        ]);
    }

    /**
     * Req 4: Jika periode belum dimulai, tampilkan pesan: "Pengisian belum dibuka"
     */
    public function test_period_not_started_shows_correct_message(): void
    {
        $period = SpkrtPeriod::create([
            'activity_name' => 'Survei Belum Mulai',
            'start_date' => Carbon::today()->addDays(5),
            'end_date' => Carbon::today()->addDays(15),
            'is_active' => true,
        ]);

        $this->assertEquals('not_started', $period->currentStatus());
        $this->assertEquals('Pengisian belum dibuka', $period->status_message);
        $this->assertFalse($period->isOpen());
    }

    /**
     * Req 5 & 8: Jika periode sudah berakhir, tampilkan pesan: "Periode pengisian telah ditutup"
     * dan otomatis mengunci formulir meskipun admin lupa menonaktifkannya.
     */
    public function test_period_ended_shows_correct_message_and_locks_automatically(): void
    {
        $period = SpkrtPeriod::create([
            'activity_name' => 'Survei Telah Berakhir',
            'start_date' => Carbon::today()->subDays(20),
            'end_date' => Carbon::today()->subDays(2),
            'is_active' => true, // Admin lupa menonaktifkan
        ]);

        $this->assertEquals('closed', $period->currentStatus());
        $this->assertEquals('Periode pengisian telah ditutup', $period->status_message);
        $this->assertFalse($period->isOpen()); // Harus otomatis terkunci (Req 8)
    }

    /**
     * Req 6: Jika periode aktif dan dalam rentang tanggal, tampilkan: "Pengisian SPKRT Dibuka"
     */
    public function test_active_period_shows_correct_message(): void
    {
        $period = SpkrtPeriod::create([
            'activity_name' => 'Survei Aktif',
            'start_date' => Carbon::today()->subDays(2),
            'end_date' => Carbon::today()->addDays(10),
            'is_active' => true,
        ]);

        $this->assertEquals('active', $period->currentStatus());
        $this->assertEquals('Pengisian SPKRT Dibuka', $period->status_message);
        $this->assertTrue($period->isOpen());
    }

    /**
     * Req 7: Admin dapat mengubah status aktif/nonaktif kapan saja.
     */
    public function test_admin_can_toggle_period_status_anytime(): void
    {
        $period = SpkrtPeriod::create([
            'activity_name' => 'Survei Toggle Test',
            'start_date' => Carbon::today()->subDays(2),
            'end_date' => Carbon::today()->addDays(10),
            'is_active' => true,
        ]);

        // Toggle to inactive
        $response = $this->actingAs($this->admin)
            ->patch(route('admin.spkrt-periods.toggle-status', $period));

        $response->assertSessionHas('success');
        $this->assertFalse($period->fresh()->is_active);
        $this->assertFalse($period->fresh()->isOpen());
        $this->assertEquals('Periode pengisian telah ditutup', $period->fresh()->status_message);

        // Toggle back to active
        $this->actingAs($this->admin)
            ->patch(route('admin.spkrt-periods.toggle-status', $period));

        $this->assertTrue($period->fresh()->is_active);
        $this->assertTrue($period->fresh()->isOpen());
        $this->assertEquals('Pengisian SPKRT Dibuka', $period->fresh()->status_message);
    }

    /**
     * Req 9: Petugas tidak dapat mengubah pengaturan periode.
     */
    public function test_petugas_cannot_access_or_modify_period_settings(): void
    {
        $period = SpkrtPeriod::create([
            'activity_name' => 'Survei Protected',
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today()->addDays(7),
            'is_active' => true,
        ]);

        // Access index as petugas -> redirected by RoleMiddleware
        $response = $this->actingAs($this->petugas)->get(route('admin.spkrt-periods.index'));
        $response->assertRedirect(route('petugas.dashboard'));

        // Try to toggle as petugas -> redirected by RoleMiddleware
        $toggle = $this->actingAs($this->petugas)
            ->patch(route('admin.spkrt-periods.toggle-status', $period));
        $toggle->assertRedirect(route('petugas.dashboard'));

        // Try to delete as petugas -> redirected by RoleMiddleware
        $delete = $this->actingAs($this->petugas)
            ->delete(route('admin.spkrt-periods.destroy', $period));
        $delete->assertRedirect(route('petugas.dashboard'));
    }

    /**
     * Req 10: Tampilkan status periode pada dashboard petugas.
     */
    public function test_period_status_displayed_on_petugas_dashboard(): void
    {
        $period = SpkrtPeriod::create([
            'activity_name' => 'Survei Pertanian Dashboard Test',
            'start_date' => Carbon::today()->subDays(1),
            'end_date' => Carbon::today()->addDays(5),
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->petugas)->get(route('petugas.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Survei Pertanian Dashboard Test');
        $response->assertSee('Pengisian SPKRT Dibuka');
    }

    /**
     * Req 3 & 8: Petugas hanya dapat mengisi formulir SPKRT jika status aktif dan tanggal sesuai.
     */
    public function test_petugas_cannot_submit_bast_when_period_is_closed(): void
    {
        // Period already ended
        SpkrtPeriod::create([
            'activity_name' => 'Survei Telah Berakhir',
            'start_date' => Carbon::today()->subDays(20),
            'end_date' => Carbon::today()->subDays(1),
            'is_active' => true,
        ]);

        $respondent = Respondent::create([
            'name' => 'Bapak Kepala Keluarga',
            'family_head_name' => 'Bapak Kepala Keluarga',
            'officer_id' => $this->petugas->id,
            'address' => 'Desa Sukamaju',
            'agency' => 'BPS Kolaka Utara',
            'purpose' => 'Sensus',
            'input_date' => Carbon::today(),
        ]);

        $response = $this->actingAs($this->petugas)->post(route('petugas.basts.respondents.store'), [
            'respondent_id' => $respondent->id,
            'handover_category' => 'KUES',
            'bast_date' => Carbon::today()->toDateString(),
            'recipient_type' => 'krt',
            'handover_items' => 'Kuesioner SPKRT',
        ]);

        // Form must be locked with error message
        $response->assertSessionHas('error', 'Periode pengisian telah ditutup');
        $this->assertDatabaseMissing('respondent_basts', [
            'respondent_id' => $respondent->id,
        ]);
    }
}
