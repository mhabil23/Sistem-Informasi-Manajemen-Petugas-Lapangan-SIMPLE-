<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MitraImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->artisan('import:mitra-petugas');
    }

    public function test_imported_mitra_account_exists_and_can_login(): void
    {
        $mitra = User::where('role', 'petugas')
            ->whereNotNull('sobat_id')
            ->first();

        $this->assertNotNull($mitra, 'Akun mitra hasil import harus ada di database.');
        $this->assertNotEmpty($mitra->sobat_id);
        $this->assertEquals($mitra->username, $mitra->sobat_id);
        $this->assertEquals('petugas', $mitra->role);

        // Test login menggunakan username (SOBAT ID) dan default password 'bps7408'
        $response = $this->post('/login', [
            'login' => $mitra->username,
            'password' => 'bps7408',
        ]);

        $response->assertRedirect(route('petugas.dashboard'));
        $this->assertAuthenticatedAs($mitra);

        // Akses dashboard petugas
        $dashResponse = $this->actingAs($mitra)->get(route('petugas.dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee($mitra->name);
    }
}
