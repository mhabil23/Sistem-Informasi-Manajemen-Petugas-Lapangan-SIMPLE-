<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\BerandaMenuVisibilityService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BerandaMenuVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $petugas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'username' => 'admin_menu_' . uniqid(),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->petugas = User::factory()->create([
            'username' => 'petugas_menu_' . uniqid(),
            'role' => 'petugas',
            'status' => 'active',
        ]);
    }

    public function test_beranda_menus_are_hidden_by_default_from_both_dropdown_and_beranda(): void
    {
        $service = app(BerandaMenuVisibilityService::class);
        $this->assertFalse($service->shouldShowMenus());

        $response = $this->actingAs($this->petugas)->get(route('petugas.dashboard'));
        $response->assertStatus(200);

        $html = $response->getContent();
        $this->assertStringNotContainsString('Menu Aksi Lapangan', $html);
        $this->assertStringNotContainsString('Tanda Terima Blanko', $html);
        $this->assertStringNotContainsString('BAST Dokumen Terisi', $html);
        $this->assertStringNotContainsString('Laporan Perjadin', $html);
        $this->assertStringNotContainsString('Tanda Terima Responden (BAST)', $html);
    }

    public function test_admin_can_toggle_each_of_the_4_menus_individually(): void
    {
        // Turn on ONLY "laporan_perjadin"
        $this->actingAs($this->admin)
            ->patch(route('admin.beranda-menus.toggle'), [
                'menu_key' => BerandaMenuVisibilityService::MENU_LAPORAN_PERJADIN,
                'enabled' => '1',
            ])
            ->assertSessionHas('success');

        $this->flushSession();
        $resOnlyOne = $this->actingAs($this->petugas)->get(route('petugas.dashboard'));
        $resOnlyOne->assertStatus(200);
        $htmlOne = $resOnlyOne->getContent();
        $this->assertStringContainsString('Menu Aksi Lapangan', $htmlOne);
        $this->assertStringContainsString('Laporan Perjadin', $htmlOne);
        $this->assertStringContainsString('1 dari 4 Menu Tampil', $htmlOne);
        $this->assertStringNotContainsString('Tanda Terima Responden (BAST)', $htmlOne);
        $this->assertStringNotContainsString('Tanda Terima Blanko', $htmlOne);
        $this->assertStringNotContainsString('BAST Dokumen Terisi', $htmlOne);

        // Turn on ONLY one more menu: "tanda_terima_blanko" -> 2 of 4 visible
        $this->actingAs($this->admin)
            ->patch(route('admin.beranda-menus.toggle'), [
                'menu_key' => BerandaMenuVisibilityService::MENU_TANDA_TERIMA_BLANKO,
                'enabled' => '1',
            ])
            ->assertSessionHas('success');

        $this->flushSession();
        $resTwo = $this->actingAs($this->petugas)->get(route('petugas.dashboard'));
        $resTwo->assertStatus(200);
        $htmlTwo = $resTwo->getContent();
        $this->assertStringContainsString('2 dari 4 Menu Tampil', $htmlTwo);
        $this->assertStringContainsString('Laporan Perjadin', $htmlTwo);
        $this->assertStringContainsString('Tanda Terima Blanko', $htmlTwo);
        $this->assertStringNotContainsString('Tanda Terima Responden (BAST)', $htmlTwo);
        $this->assertStringNotContainsString('BAST Dokumen Terisi', $htmlTwo);

        // Turn off "laporan_perjadin" while keeping "tanda_terima_blanko" ON
        $this->actingAs($this->admin)
            ->patch(route('admin.beranda-menus.toggle'), [
                'menu_key' => BerandaMenuVisibilityService::MENU_LAPORAN_PERJADIN,
                'enabled' => '0',
            ])
            ->assertSessionHas('success');

        $this->flushSession();
        $resAfterOff = $this->actingAs($this->petugas)->get(route('petugas.dashboard'));
        $htmlAfterOff = $resAfterOff->getContent();
        $this->assertStringContainsString('Tanda Terima Blanko', $htmlAfterOff);
        $this->assertStringNotContainsString('Laporan Perjadin', $htmlAfterOff);
    }

    public function test_admin_can_toggle_all_4_menus_on_and_off_together(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.beranda-menus.toggle'), ['enabled' => '1'])
            ->assertSessionHas('success');

        $this->flushSession();
        $resAllOn = $this->actingAs($this->petugas)->get(route('petugas.dashboard'));
        $resAllOn->assertStatus(200);
        $resAllOn->assertSee('Semua Menu Tampil (4/4)');
        $resAllOn->assertSee('Tanda Terima Responden (BAST)');
        $resAllOn->assertSee('Tanda Terima Blanko');
        $resAllOn->assertSee('BAST Dokumen Terisi');
        $resAllOn->assertSee('Laporan Perjadin');

        $this->actingAs($this->admin)
            ->patch(route('admin.beranda-menus.toggle'), ['enabled' => '0'])
            ->assertSessionHas('success');

        $this->flushSession();
        $resAllOff = $this->actingAs($this->petugas)->get(route('petugas.dashboard'));
        $htmlOff = $resAllOff->getContent();
        $this->assertStringNotContainsString('Menu Aksi Lapangan', $htmlOff);
        $this->assertStringNotContainsString('Tanda Terima Responden (BAST)', $htmlOff);
    }

    public function test_individual_menu_period_controls_only_that_menu(): void
    {
        // Active period ONLY for tanda_terima_responden; future period ONLY for bast_dokumen_terisi
        $this->actingAs($this->admin)
            ->patch(route('admin.beranda-menus.update-period'), [
                'menu_key' => BerandaMenuVisibilityService::MENU_TANDA_TERIMA_RESPONDEN,
                'start_date' => Carbon::today()->subDay()->toDateString(),
                'end_date' => Carbon::today()->addDays(2)->toDateString(),
            ])
            ->assertSessionHas('success');

        $this->actingAs($this->admin)
            ->patch(route('admin.beranda-menus.update-period'), [
                'menu_key' => BerandaMenuVisibilityService::MENU_BAST_TERISI,
                'start_date' => Carbon::today()->addDays(5)->toDateString(),
                'end_date' => Carbon::today()->addDays(10)->toDateString(),
            ])
            ->assertSessionHas('success');

        $this->flushSession();
        $res = $this->actingAs($this->petugas)->get(route('petugas.dashboard'));
        $res->assertStatus(200);
        $html = $res->getContent();
        $this->assertStringContainsString('Tanda Terima Responden (BAST)', $html);
        $this->assertStringContainsString('1 dari 4 Menu Tampil', $html);
        $this->assertStringNotContainsString('Tanda Terima Blanko', $html);
        $this->assertStringNotContainsString('Laporan Perjadin', $html);
        $this->assertStringNotContainsString('BAST Dokumen Terisi', $html);
    }

    public function test_admin_pages_render_and_petugas_cannot_modify_controls(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.beranda-menus.index'))
            ->assertStatus(200)
            ->assertSee('Nyalakan Semua (4)')
            ->assertSee('Tanda Terima Responden (BAST)')
            ->assertSee('Tanda Terima Blanko Petugas')
            ->assertSee('BAST Dokumen Terisi Petugas');

        $this->actingAs($this->admin)
            ->get(route('admin.spkrt-periods.index'))
            ->assertStatus(200)
            ->assertSee('Kontrol Tampil Menu Beranda Petugas');

        $this->actingAs($this->petugas)
            ->get(route('admin.beranda-menus.index'))
            ->assertRedirect(route('petugas.dashboard'));

        $this->actingAs($this->petugas)
            ->patch(route('admin.beranda-menus.toggle'), [
                'menu_key' => BerandaMenuVisibilityService::MENU_LAPORAN_PERJADIN,
                'enabled' => '1',
            ])
            ->assertRedirect(route('petugas.dashboard'));

        $this->actingAs($this->petugas)
            ->patch(route('admin.beranda-menus.update-period'), [
                'menu_key' => BerandaMenuVisibilityService::MENU_LAPORAN_PERJADIN,
                'start_date' => Carbon::today()->toDateString(),
                'end_date' => Carbon::today()->addDay()->toDateString(),
            ])
            ->assertRedirect(route('petugas.dashboard'));

        $this->assertFalse(app(BerandaMenuVisibilityService::class)->shouldShowMenus());
    }
}
