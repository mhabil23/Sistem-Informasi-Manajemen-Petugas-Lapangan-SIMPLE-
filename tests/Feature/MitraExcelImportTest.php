<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MitraExcelImportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $petugas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Administrator SIMPLE',
            'username' => 'admin_test',
            'email' => 'admin_test@bps.go.id',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->petugas = User::factory()->create([
            'name' => 'Abd. Muis',
            'username' => 'muismustaming@gmail.com',
            'email' => 'muismustaming@gmail.com',
            'role' => 'petugas',
            'status' => 'active',
        ]);
    }

    public function test_admin_can_download_mitra_template(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.template-mitra'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=Template_Daftar_Mitra_SIMPEL.csv');
        $content = $response->streamedContent();
        $this->assertStringContainsString('SOBAT ID', $content);
        $this->assertStringContainsString('Nama Lengkap', $content);
        $this->assertStringContainsString('ahmadfauziliwang@gmail.com', $content);
    }

    public function test_admin_can_import_mitra_from_csv(): void
    {
        $csvContent = "\xEF\xBB\xBF" .
            "SOBAT ID,Nama Lengkap,Email,No Telp,Posisi,Alamat Kecamatan,Alamat Desa/Kel,Alamat Detail,Jenis Kelamin,Tempat Tanggal Lahir,Pendidikan\n" .
            "740822020003,Ahmad Fauzi,ahmadfauziliwang@gmail.com,082188167081,Mitra Pendataan,Lasusua,Lasusua,Dusun 2,Laki-Laki,\"JENEPONTO, 23 Januari 1993\",Tamat SMA/Sederajat\n" .
            "740822020017,Marjainah,marjaina@gmail.com,082393703204,Mitra Pendataan,Watunohu,Watunohu,Dusun 1,Perempuan,\"KOLAKA UTARA, 15 Oktober 1990\",Tamat SMA/Sederajat\n";

        $file = UploadedFile::fake()->createWithContent('daftar_mitra_test.csv', $csvContent);

        $response = $this->actingAs($this->admin)->post(route('admin.users.import-mitra'), [
            'excel_file' => $file,
        ]);

        $response->assertSessionHas('success');
        $response->assertRedirect();

        // Verifikasi akun dibuat di database
        $user1 = User::where('sobat_id', '740822020003')->first();
        $this->assertNotNull($user1);
        $this->assertEquals('Ahmad Fauzi', $user1->name);
        $this->assertEquals('ahmadfauziliwang@gmail.com', $user1->email);
        $this->assertEquals('petugas', $user1->role);
        $this->assertEquals('active', $user1->status);
        $this->assertEquals('Lasusua', $user1->district);
        $this->assertTrue(Hash::check('bps7408', $user1->password));

        $user2 = User::where('sobat_id', '740822020017')->first();
        $this->assertNotNull($user2);
        $this->assertEquals('Marjainah', $user2->name);
        $this->assertEquals('marjaina@gmail.com', $user2->email);
        $this->assertTrue(Hash::check('bps7408', $user2->password));
    }

    public function test_imported_mitra_can_login_with_sobat_id_and_default_password(): void
    {
        $user = User::factory()->create([
            'name' => 'Ahmad Fauzi',
            'username' => '740822020003',
            'sobat_id' => '740822020003',
            'email' => 'ahmadfauziliwang@gmail.com',
            'password' => Hash::make('bps7408'),
            'role' => 'petugas',
            'status' => 'active',
        ]);

        $response = $this->post(route('login.post'), [
            'login' => '740822020003',
            'password' => 'bps7408',
        ]);

        $response->assertRedirect(route('petugas.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_imported_mitra_can_login_with_email_and_default_password(): void
    {
        $user = User::factory()->create([
            'name' => 'Marjainah',
            'username' => 'marjainah_mitra',
            'sobat_id' => '740822020017',
            'email' => 'marjaina@gmail.com',
            'password' => Hash::make('bps7408'),
            'role' => 'petugas',
            'status' => 'active',
        ]);

        $response = $this->post(route('login.post'), [
            'login' => 'marjaina@gmail.com',
            'password' => 'bps7408',
        ]);

        $response->assertRedirect(route('petugas.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_petugas_cannot_access_import_mitra(): void
    {
        $file = UploadedFile::fake()->create('test.csv', 10);

        $response = $this->actingAs($this->petugas)->post(route('admin.users.import-mitra'), [
            'excel_file' => $file,
        ]);

        $response->assertRedirect(route('petugas.dashboard'));
    }
}
