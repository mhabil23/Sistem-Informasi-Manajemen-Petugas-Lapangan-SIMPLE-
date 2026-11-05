<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('SIMPLE');
        $response->assertSee('Masuk ke Sistem');
    }

    public function test_admin_can_authenticate_and_access_admin_dashboard(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->post('/login', [
            'login' => $admin->username,
            'password' => 'admin123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);

        $dashResponse = $this->actingAs($admin)->get(route('admin.dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Dashboard Administrator');
        $dashResponse->assertSee('Total Petugas');
    }

    public function test_petugas_cannot_access_admin_dashboard(): void
    {
        $petugas = User::where('role', 'petugas')->first();

        $response = $this->actingAs($petugas)->get(route('admin.dashboard'));
        $response->assertRedirect(route('petugas.dashboard'));
    }

    public function test_petugas_can_authenticate_and_access_petugas_dashboard(): void
    {
        $petugas = User::where('role', 'petugas')->first();

        $response = $this->post('/login', [
            'login' => $petugas->username,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('petugas.dashboard'));
        $this->assertAuthenticatedAs($petugas);

        $dashResponse = $this->actingAs($petugas)->get(route('petugas.dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Dashboard Petugas');
        $dashResponse->assertSee('Tugas Diberikan');
    }

    public function test_user_can_logout(): void
    {
        $user = User::first();

        $response = $this->actingAs($user)->post('/logout');
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_user_can_update_profile(): void
    {
        $user = User::first();

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Nama Baru Diperbarui',
            'email' => 'emailbaru@sp4rt.go.id',
            'phone' => '089988776655',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nama Baru Diperbarui',
            'email' => 'emailbaru@sp4rt.go.id',
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));

        $petugasResponse = $this->get(route('petugas.dashboard'));
        $petugasResponse->assertRedirect(route('login'));
    }
}
