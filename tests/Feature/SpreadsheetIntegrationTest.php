<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpreadsheetIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_access_spreadsheet_settings(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.spreadsheet.index'));
        $response->assertStatus(200);
        $response->assertSee('Database Spreadsheet');
        $response->assertSee('URL WEB APP GOOGLE APPS SCRIPT');
        $response->assertSee('Salin Kode Apps Script');
    }

    public function test_petugas_cannot_access_spreadsheet_settings(): void
    {
        $petugas = User::where('role', 'petugas')->first();

        $response = $this->actingAs($petugas)->get(route('admin.spreadsheet.index'));
        $response->assertRedirect(route('petugas.dashboard'));
    }

    public function test_admin_can_update_webapp_url(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->put(route('admin.spreadsheet.update'), [
            'webapp_url' => 'https://script.google.com/macros/s/AKfycbxTestDemoDeploymentId123456/exec',
        ]);

        $response->assertSessionHas('success');
    }
}

