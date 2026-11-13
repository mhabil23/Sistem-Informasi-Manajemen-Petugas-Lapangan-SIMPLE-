<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Respondent;
use App\Models\RespondentBast;
use App\Models\OfficerBast;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RespondentAndBastTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $petugas;
    protected Assignment $assignment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Administrator SIMPLE',
            'username' => 'admin_test',
            'email' => 'admin@test.com',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->petugas = User::factory()->create([
            'name' => 'Budi Petugas',
            'username' => 'petugas_test',
            'email' => 'petugas@test.com',
            'role' => 'petugas',
            'status' => 'active',
        ]);

        $this->assignment = Assignment::create([
            'title' => 'Pendataan Sarpras 2026',
            'description' => 'Pendataan alat dan mesin pertanian',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(14)->toDateString(),
            'location' => 'Kabupaten Malang',
            'status' => 'ongoing',
            'created_by' => $this->admin->id,
        ]);
    }

    public function test_admin_can_view_respondents_page_with_data()
    {
        $respondent = Respondent::create([
            'name' => 'Kelompok Tani Sejahtera',
            'address' => 'Desa Karangpandan RT 02 RW 01',
            'phone' => '081234567890',
            'agency' => 'Kelompok Tani',
            'purpose' => 'Verifikasi Mesin Traktor',
            'assignment_id' => $this->assignment->id,
            'officer_id' => $this->petugas->id,
            'input_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.respondents.index'));

        $response->assertStatus(200);
        $response->assertSee('Data Responden Lapangan');
        $response->assertSee('Kelompok Tani Sejahtera');
        $response->assertSee('Total Responden');
    }

    public function test_admin_can_create_new_respondent()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.respondents.store'), [
            'name' => 'Gapoktan Makmur',
            'address' => 'Jl. Raya Singosari No. 12',
            'phone' => '08567891234',
            'agency' => 'Gapoktan Mandiri',
            'purpose' => 'Bantuan Pompa Air',
            'assignment_id' => $this->assignment->id,
            'officer_id' => $this->petugas->id,
            'input_date' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('admin.respondents.index'));
        $this->assertDatabaseHas('respondents', [
            'name' => 'Gapoktan Makmur',
            'agency' => 'Gapoktan Mandiri',
        ]);
    }

    public function test_admin_can_access_and_create_bast_respondent()
    {
        $respondent = Respondent::create([
            'name' => 'Kelompok Tani Barokah',
            'address' => 'Desa Sumbersekar',
            'phone' => '081234567899',
            'agency' => 'Kelompok Tani Barokah',
            'purpose' => 'Serah terima bibit',
            'assignment_id' => $this->assignment->id,
            'officer_id' => $this->petugas->id,
            'input_date' => now()->toDateString(),
        ]);

        $indexResponse = $this->actingAs($this->admin)->get(route('admin.basts.respondents.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('BAST Responden');

        $storeResponse = $this->actingAs($this->admin)->post(route('admin.basts.respondents.store'), [
            'respondent_id' => $respondent->id,
            'bast_date' => now()->toDateString(),
            'bast_number' => 'BAST-RESP/TEST/001',
            'handover_items' => '1. Buku Pedoman Teknis\n2. Bantuan Alat',
            'notes' => 'Telah diterima dalam kondisi baik',
            'status' => 'finalized',
        ]);

        $storeResponse->assertRedirect(route('admin.basts.respondents.index'));
        $this->assertDatabaseHas('respondent_basts', [
            'bast_number' => 'BAST-RESP/TEST/001',
            'respondent_id' => $respondent->id,
        ]);
    }

    public function test_admin_can_access_officer_basts_page()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.basts.officers.index'));
        $response->assertStatus(200);
        $response->assertSee('BAST Petugas');
    }

    public function test_petugas_can_view_own_respondents_only()
    {
        // Respondent for this petugas
        Respondent::create([
            'name' => 'Responden Milik Budi',
            'address' => 'Jl. Merpati',
            'phone' => '08111111111',
            'agency' => 'Dinas A',
            'purpose' => 'Survei',
            'assignment_id' => $this->assignment->id,
            'officer_id' => $this->petugas->id,
            'input_date' => now()->toDateString(),
        ]);

        $otherPetugas = User::factory()->create([
            'username' => 'petugas2_test',
            'role' => 'petugas',
            'status' => 'active',
        ]);

        // Respondent for other petugas
        Respondent::create([
            'name' => 'Responden Milik Orang Lain',
            'address' => 'Jl. Elang',
            'phone' => '08222222222',
            'agency' => 'Dinas B',
            'purpose' => 'Survei',
            'assignment_id' => $this->assignment->id,
            'officer_id' => $otherPetugas->id,
            'input_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->petugas)->get(route('petugas.respondents.index'));
        $response->assertStatus(200);
        $response->assertSee('Responden Milik Budi');
        $response->assertDontSee('Responden Milik Orang Lain');
    }

    public function test_petugas_can_create_bast_with_krt_recipient()
    {
        $respondent = Respondent::create([
            'name' => 'Jamaluddin Ali',
            'address' => 'Desa Makmur',
            'phone' => '081234567899',
            'agency' => 'Kelompok Tani Subur',
            'purpose' => 'Pendataan',
            'assignment_id' => $this->assignment->id,
            'officer_id' => $this->petugas->id,
            'input_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->petugas)->post(route('petugas.basts.respondents.store'), [
            'respondent_id' => $respondent->id,
            'bast_date' => now()->toDateString(),
            'recipient_type' => 'krt',
            'handover_items' => '1. Kuesioner Sensus Lapangan',
            'notes' => 'Diserahkan langsung ke KRT',
        ]);

        $response->assertRedirect(route('petugas.basts.respondents.index'));
        $this->assertDatabaseHas('respondent_basts', [
            'respondent_id' => $respondent->id,
            'recipient_type' => 'krt',
            'recipient_name' => 'Jamaluddin Ali',
        ]);
    }

    public function test_petugas_can_create_bast_with_representative_recipient()
    {
        $respondent = Respondent::create([
            'name' => 'Jamaluddin Ali',
            'address' => 'Desa Makmur',
            'phone' => '081234567899',
            'agency' => 'Kelompok Tani Subur',
            'purpose' => 'Pendataan',
            'assignment_id' => $this->assignment->id,
            'officer_id' => $this->petugas->id,
            'input_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->petugas)->post(route('petugas.basts.respondents.store'), [
            'respondent_id' => $respondent->id,
            'bast_date' => now()->toDateString(),
            'recipient_type' => 'representative',
            'recipient_name' => 'Nama Keluarga',
            'recipient_relation' => 'Istri / Pasangan',
            'handover_items' => '1. Kuesioner Sensus Lapangan',
            'notes' => 'Diterima oleh istri karena KRT sedang di kebun',
        ]);

        $response->assertRedirect(route('petugas.basts.respondents.index'));
        $this->assertDatabaseHas('respondent_basts', [
            'respondent_id' => $respondent->id,
            'recipient_type' => 'representative',
            'recipient_name' => 'Nama Keluarga',
            'recipient_relation' => 'Istri / Pasangan',
        ]);
    }
}
