<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PegawaiSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PegawaiAndDualRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_pegawai_artisan_command_imports_all_25_bps_officers(): void
    {
        $exitCode = Artisan::call('import:pegawai');
        $this->assertSame(0, $exitCode);

        // Verify total imported pegawai
        $this->assertSame(25, User::where('role', 'pegawai')->count());

        // Verify Ibu Siti Rogayah
        $siti = User::where('username', 'siti.rogayah')->first();
        $this->assertNotNull($siti);
        $this->assertSame('Siti Rogayah, S.ST, M.S.E, M.P.P.', $siti->name);
        $this->assertSame('198708152009122001', $siti->nik);
        $this->assertSame('340053274', $siti->sobat_id);
        $this->assertSame('Pembina, IV/a', $siti->rank);
        $this->assertSame('Statistisi Ahli Muda', $siti->position);
        $this->assertTrue($siti->can_switch_role);
        $this->assertTrue($siti->canSwitchRole());

        // Verify Kepala BPS Dini Amirul
        $dini = User::where('username', 'dini.amirul')->first();
        $this->assertNotNull($dini);
        $this->assertSame('Dini Amirul, S.ST, MIDS, M.Ec.Dev.', $dini->name);
        $this->assertSame('198712242009122001', $dini->nik);
        $this->assertSame('Kepala BPS Kabupaten Kolaka Utara', $dini->position);
        $this->assertTrue($dini->can_switch_role);

        // Verify Kasubbag Umum Tri Halisir
        $tri = User::where('username', 'tri.halisir')->first();
        $this->assertNotNull($tri);
        $this->assertSame('199002122019031001', $tri->nik);
        $this->assertSame('Kepala Subbagian Umum', $tri->position);
        $this->assertTrue($tri->can_switch_role);

        // Verify an officer without default dual role (e.g. Asma Rahmani)
        $asma = User::where('username', 'asma.rahmani')->first();
        $this->assertNotNull($asma);
        $this->assertSame('199809092021042001', $asma->nik);
        $this->assertFalse($asma->can_switch_role);
    }

    public function test_pegawai_can_login_using_nip(): void
    {
        $this->seed(PegawaiSeeder::class);

        $siti = User::where('username', 'siti.rogayah')->first();
        $this->assertNotNull($siti);

        // Login using NIP as login credential
        $response = $this->post(route('login.post'), [
            'login' => '198708152009122001',
            'password' => 'bps7408',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($siti);
    }

    public function test_dual_role_user_can_switch_between_admin_and_pegawai_modes(): void
    {
        $this->seed(PegawaiSeeder::class);

        $siti = User::where('username', 'siti.rogayah')->first();
        $this->assertTrue($siti->canSwitchRole());

        $this->actingAs($siti);

        // 1. Switch to Admin mode
        $switchAdmin = $this->post(route('role.switch'), [
            'role' => 'admin',
        ]);
        $switchAdmin->assertRedirect(route('admin.dashboard'));
        $switchAdmin->assertSessionHas('active_role', 'admin');

        // As active Admin, she can access admin-only area (e.g. Manajemen Pengguna)
        $usersPage = $this->get(route('admin.users.index'));
        $usersPage->assertOk();
        $usersPage->assertSee('Manajemen Pengguna');

        // 2. Switch to Pegawai mode
        $switchPegawai = $this->post(route('role.switch'), [
            'role' => 'pegawai',
        ]);
        $switchPegawai->assertRedirect(route('petugas.dashboard'));
        $switchPegawai->assertSessionHas('active_role', 'pegawai');

        // As active Pegawai, she can access petugas portal
        $petugasDashboard = $this->get(route('petugas.dashboard'));
        $petugasDashboard->assertOk();

        // But when in Pegawai mode, admin-only routes redirect with error
        $blockedAdmin = $this->get(route('admin.users.index'));
        $blockedAdmin->assertRedirect(route('petugas.dashboard'));
    }

    public function test_user_without_dual_role_cannot_switch_to_admin(): void
    {
        $this->seed(PegawaiSeeder::class);

        $asma = User::where('username', 'asma.rahmani')->first();
        $this->assertFalse($asma->canSwitchRole());

        $this->actingAs($asma);

        $response = $this->post(route('role.switch'), [
            'role' => 'admin',
        ]);

        $response->assertSessionHas('error');
    }

    public function test_admin_can_toggle_dual_role_for_an_officer(): void
    {
        $admin = User::create([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'admin@bps.go.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $officer = User::create([
            'name' => 'Budi Pegawai',
            'username' => 'budi.pegawai',
            'email' => 'budi@bps.go.id',
            'password' => Hash::make('password'),
            'role' => 'pegawai',
            'can_switch_role' => false,
            'status' => 'active',
        ]);

        $this->actingAs($admin);

        // Toggle ON
        $response = $this->patch(route('admin.users.toggle-dual-role', $officer));
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertTrue($officer->fresh()->can_switch_role);

        // Toggle OFF
        $response2 = $this->patch(route('admin.users.toggle-dual-role', $officer));
        $response2->assertRedirect();
        $this->assertFalse($officer->fresh()->can_switch_role);
    }

    public function test_admin_can_revoke_and_grant_admin_access_for_pegawai(): void
    {
        $admin = User::create([
            'name' => 'Super Admin',
            'username' => 'admin',
            'email' => 'admin@bps.go.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $pegawai = User::create([
            'name' => 'Pegawai Dual Role',
            'username' => 'pegawai.dual',
            'email' => 'pegawai.dual@bps.go.id',
            'password' => Hash::make('password'),
            'role' => 'pegawai',
            'can_switch_role' => true,
            'status' => 'active',
        ]);

        $this->actingAs($admin);

        // 1. Explicit Revoke Admin Access
        $responseRevoke = $this->patch(route('admin.users.revoke-admin', $pegawai));
        $responseRevoke->assertRedirect();
        $responseRevoke->assertSessionHas('success');
        $this->assertFalse($pegawai->fresh()->can_switch_role);
        $this->assertFalse($pegawai->fresh()->canSwitchRole());

        // 2. Explicit Grant Admin Access
        $responseGrant = $this->patch(route('admin.users.grant-admin', $pegawai));
        $responseGrant->assertRedirect();
        $responseGrant->assertSessionHas('success');
        $this->assertTrue($pegawai->fresh()->can_switch_role);
        $this->assertTrue($pegawai->fresh()->canSwitchRole());

        // 3. Admin cannot revoke their own primary admin account
        $responseSelf = $this->patch(route('admin.users.revoke-admin', $admin));
        $responseSelf->assertSessionHas('error');
    }

    public function test_admin_can_bulk_revoke_all_pegawai_admin_access(): void
    {
        $admin = User::create([
            'name' => 'Super Admin',
            'username' => 'admin',
            'email' => 'admin@bps.go.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $pegawai1 = User::create([
            'name' => 'Pegawai Satu',
            'username' => 'pegawai1',
            'email' => 'pegawai1@bps.go.id',
            'password' => Hash::make('password'),
            'role' => 'pegawai',
            'can_switch_role' => true,
            'status' => 'active',
        ]);

        $pegawai2 = User::create([
            'name' => 'Pegawai Dua',
            'username' => 'pegawai2',
            'email' => 'pegawai2@bps.go.id',
            'password' => Hash::make('password'),
            'role' => 'pegawai',
            'can_switch_role' => true,
            'status' => 'active',
        ]);

        $this->actingAs($admin);

        // Execute bulk revocation
        $response = $this->post(route('admin.users.revoke-all-pegawai-admin'));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertFalse($pegawai1->fresh()->can_switch_role);
        $this->assertFalse($pegawai2->fresh()->can_switch_role);

        // When pegawai tries to access admin routes, they are rejected
        $this->actingAs($pegawai1->fresh());
        $responseAdmin = $this->get(route('admin.users.index'));
        $responseAdmin->assertRedirect(route('petugas.dashboard'));
    }

    public function test_get_pegawai_signers_returns_official_bps_officers_with_nip(): void
    {
        $signers = User::getPegawaiSigners();
        $this->assertNotEmpty($signers);

        $hidayatullah = $signers->first(fn ($s) => str_contains($s['name'], 'Hidayatullah'));
        $this->assertNotNull($hidayatullah);
        $this->assertNotEmpty($hidayatullah['nip']);
        $this->assertSame('198510292011011001', $hidayatullah['nip']);

        $admin = User::create([
            'name' => 'Super Admin',
            'username' => 'admin_test',
            'email' => 'admin_test@bps.go.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.basts.officers.index'));
        $response->assertStatus(200);
        $response->assertSee('terisiPegawaiSelect');
        $response->assertSee('blankoPegawaiSelect');
        $response->assertSee('198510292011011001');
    }
}
