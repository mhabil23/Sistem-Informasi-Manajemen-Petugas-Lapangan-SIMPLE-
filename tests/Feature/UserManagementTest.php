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

        $response = $this->actingAs($admin)->get(route('admin.users.index'));
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
}

