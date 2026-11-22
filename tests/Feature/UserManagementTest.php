<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_view_user_management_screen(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.users.index', ['search' => 'Budi']));
        $response->assertStatus(200);
        $response->assertSee('Manajemen Pengguna');
        $response->assertSee('Total Pengguna');
        $response->assertSee('Tambah Pengguna Baru');
        $response->assertSee('Budi Santoso');
    }

    public function test_petugas_is_forbidden_from_user_management(): void
    {
        $petugas = User::where('role', 'petugas')->first();

        $response = $this->actingAs($petugas)->get(route('admin.users.index'));
        $response->assertRedirect(route('petugas.dashboard'));
    }

    public function test_admin_can_create_new_user(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Doni Kusuma, S.T.',
            'username' => 'doni_kusuma',
            'email' => 'doni@simple.go.id',
            'phone' => '081234567899',
            'password' => 'secret123',
            'role' => 'petugas',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'username' => 'doni_kusuma',
            'email' => 'doni@simple.go.id',
            'role' => 'petugas',
        ]);
    }

    public function test_admin_can_update_user(): void
    {
        $admin = User::where('role', 'admin')->first();
        $officer = User::where('role', 'petugas')->first();

        $response = $this->actingAs($admin)->put(route('admin.users.update', $officer), [
            'name' => 'Budi Santoso, M.Kom',
            'username' => 'budi_santoso',
            'email' => 'budi.baru@simple.go.id',
            'phone' => '089911223344',
            'role' => 'petugas',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $officer->id,
            'name' => 'Budi Santoso, M.Kom',
            'email' => 'budi.baru@simple.go.id',
        ]);
    }

    public function test_admin_can_toggle_user_status(): void
    {
        $admin = User::where('role', 'admin')->first();
        $officer = User::where('role', 'petugas')->first();

        $this->assertEquals('active', $officer->status);

        $response = $this->actingAs($admin)->patch(route('admin.users.toggle-status', $officer));
        $response->assertSessionHas('success');

        $this->assertEquals('inactive', $officer->fresh()->status);
    }

    public function test_admin_can_reset_user_password(): void
    {
        $admin = User::where('role', 'admin')->first();
        $officer = User::where('role', 'petugas')->first();

        $response = $this->actingAs($admin)->patch(route('admin.users.reset-password', $officer), [
            'new_password' => 'passwordBaru123',
        ]);

        $response->assertSessionHas('success');
        $this->assertTrue(Hash::check('passwordBaru123', $officer->fresh()->password));
    }

    public function test_admin_can_delete_user_soft_delete(): void
    {
        $admin = User::where('role', 'admin')->first();
        $officer = User::where('role', 'petugas')->first();

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $officer));
        $response->assertRedirect(route('admin.users.index'));

        $this->assertSoftDeleted('users', [
            'id' => $officer->id,
        ]);
    }

    public function test_admin_cannot_delete_self(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $admin));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'deleted_at' => null,
        ]);
    }

    public function test_admin_can_perform_bulk_actions_on_selected_users(): void
    {
        $admin = User::where('role', 'admin')->first();

        $user1 = User::factory()->create(['username' => 'pegawai_test_1', 'role' => 'pegawai', 'can_switch_role' => false, 'status' => 'active']);
        $user2 = User::factory()->create(['username' => 'pegawai_test_2', 'role' => 'pegawai', 'can_switch_role' => false, 'status' => 'active']);

        // 1. Bulk Grant Admin
        $response = $this->actingAs($admin)->post(route('admin.users.bulk-action'), [
            'action' => 'grant_admin',
            'user_ids' => [$user1->id, $user2->id],
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertTrue($user1->fresh()->can_switch_role);
        $this->assertTrue($user2->fresh()->can_switch_role);

        // 2. Bulk Deactivate
        $response = $this->actingAs($admin)->post(route('admin.users.bulk-action'), [
            'action' => 'deactivate',
            'user_ids' => [$user1->id, $user2->id],
        ]);
        $response->assertRedirect();
        $this->assertEquals('inactive', $user1->fresh()->status);
        $this->assertEquals('inactive', $user2->fresh()->status);

        // 3. Bulk Activate
        $response = $this->actingAs($admin)->post(route('admin.users.bulk-action'), [
            'action' => 'activate',
            'user_ids' => [$user1->id, $user2->id],
        ]);
        $response->assertRedirect();
        $this->assertEquals('active', $user1->fresh()->status);
        $this->assertEquals('active', $user2->fresh()->status);

        // 4. Bulk Delete
        $response = $this->actingAs($admin)->post(route('admin.users.bulk-action'), [
            'action' => 'delete',
            'user_ids' => [$user1->id, $user2->id],
        ]);
        $response->assertRedirect();
        $this->assertSoftDeleted('users', ['id' => $user1->id]);
        $this->assertSoftDeleted('users', ['id' => $user2->id]);
    }

    public function test_admin_cannot_bulk_delete_or_revoke_master_admin(): void
    {
        $admin = User::where('username', 'admin')->first();
        $master = User::factory()->create([
            'username' => 'administrator_master',
            'role' => 'admin',
            'is_master_admin' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.users.bulk-action'), [
            'action' => 'delete',
            'user_ids' => [$master->id],
        ]);
        $response->assertRedirect();
        $this->assertNull($master->fresh()->deleted_at);
    }

    public function test_admin_can_update_multiple_pegawai_admin_access_at_once(): void
    {
        $admin = User::where('role', 'admin')->first();

        $pegawai1 = User::factory()->create(['username' => 'pegawai_test_3', 'role' => 'pegawai', 'can_switch_role' => false]);
        $pegawai2 = User::factory()->create(['username' => 'pegawai_test_4', 'role' => 'pegawai', 'can_switch_role' => true]);

        // Berikan hanya ke pegawai1, cabut dari pegawai2
        $response = $this->actingAs($admin)->post(route('admin.users.update-multiple-pegawai-admin'), [
            'pegawai_ids' => [$pegawai1->id],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertTrue($pegawai1->fresh()->can_switch_role);
        $this->assertFalse($pegawai2->fresh()->can_switch_role);
    }
}

