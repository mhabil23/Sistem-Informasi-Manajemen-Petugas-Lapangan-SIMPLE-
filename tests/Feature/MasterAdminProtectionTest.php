<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MasterAdminProtectionTest extends TestCase
{
    use RefreshDatabase;

    protected User $masterAdmin;
    protected User $adminPetugas;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->masterAdmin = User::where('username', 'admin')->first();

        // Create an "admin petugas" / employee with dual role admin privileges
        $this->adminPetugas = User::create([
            'name' => 'Petugas Admin Lapangan',
            'username' => 'petugas_admin',
            'email' => 'petugas_admin@simple.go.id',
            'password' => Hash::make('password123'),
            'role' => 'pegawai',
            'can_switch_role' => true,
            'is_master_admin' => false,
            'status' => 'active',
        ]);
    }

    public function test_administrator_simple_is_identified_as_master_admin(): void
    {
        $this->assertTrue($this->masterAdmin->isMasterAdmin());
        $this->assertSame('Master Admin', $this->masterAdmin->roleLabel());
        $this->assertFalse($this->adminPetugas->isMasterAdmin());
    }

    public function test_admin_petugas_cannot_revoke_master_admin_rights(): void
    {
        $response = $this->actingAs($this->adminPetugas)
            ->withSession(['active_role' => 'admin'])
            ->patch(route('admin.users.revoke-admin', $this->masterAdmin));

        $response->assertSessionHas('error');
        $this->masterAdmin->refresh();
        $this->assertSame('admin', $this->masterAdmin->role);
        $this->assertTrue($this->masterAdmin->isMasterAdmin());
    }

    public function test_admin_petugas_cannot_delete_master_admin(): void
    {
        $response = $this->actingAs($this->adminPetugas)
            ->withSession(['active_role' => 'admin'])
            ->delete(route('admin.users.destroy', $this->masterAdmin));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', [
            'id' => $this->masterAdmin->id,
            'username' => 'admin',
            'deleted_at' => null,
        ]);
    }

    public function test_admin_petugas_cannot_deactivate_master_admin(): void
    {
        $response = $this->actingAs($this->adminPetugas)
            ->withSession(['active_role' => 'admin'])
            ->patch(route('admin.users.toggle-status', $this->masterAdmin));

        $response->assertSessionHas('error');
        $this->masterAdmin->refresh();
        $this->assertSame('active', $this->masterAdmin->status);
    }

    public function test_admin_petugas_cannot_change_master_admin_role_or_data(): void
    {
        $response = $this->actingAs($this->adminPetugas)
            ->withSession(['active_role' => 'admin'])
            ->put(route('admin.users.update', $this->masterAdmin), [
                'name' => 'Hacked Admin Name',
                'username' => 'admin',
                'email' => 'admin@simple.go.id',
                'role' => 'petugas', // Attempt to demote Master Admin
                'status' => 'inactive',
            ]);

        $response->assertSessionHas('error');
        $this->masterAdmin->refresh();
        $this->assertSame('admin', $this->masterAdmin->role);
        $this->assertSame('active', $this->masterAdmin->status);
        $this->assertSame('Administrator SIMPLE', $this->masterAdmin->name);
    }

    public function test_admin_petugas_cannot_reset_master_admin_password(): void
    {
        $oldPasswordHash = $this->masterAdmin->password;

        $response = $this->actingAs($this->adminPetugas)
            ->withSession(['active_role' => 'admin'])
            ->patch(route('admin.users.reset-password', $this->masterAdmin), [
                'new_password' => 'hackedpassword123',
            ]);

        $response->assertSessionHas('error');
        $this->masterAdmin->refresh();
        $this->assertSame($oldPasswordHash, $this->masterAdmin->password);
    }

    public function test_master_admin_badge_and_protection_rendered_in_table(): void
    {
        // When viewed by admin petugas in admin mode
        $response = $this->actingAs($this->adminPetugas)
            ->withSession(['active_role' => 'admin'])
            ->get(route('admin.users.index'));
        $response->assertStatus(200);
        $response->assertSee('Master Admin');
        $response->assertSee('Terlindungi');
    }
}
