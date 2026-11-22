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
        $response->assertSee('Data Responden');
        $response->assertSee('Kelompok Tani Sejahtera');
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
        $indexResponse->assertSee('Tanda Terima Responden');

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
        $response->assertSee('Tanda Terima Petugas');
    }

    public function test_admin_can_create_officer_bast_with_item_quantities_and_units()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.basts.officers.store'), [
            'assignment_id' => $this->assignment->id,
            'bast_type' => 'kantor_ke_petugas',
            'receipt_category' => 'PELATIHAN',
            'document_title' => 'Tanda Terima Perlengkapan Pelatihan',
            'status' => 'finalized',
            'items' => [
                ['name' => 'Buku Panduan / Modul Pelatihan', 'qty' => '1', 'unit' => 'Eks'],
                ['name' => 'Tas Pelatihan', 'qty' => '2', 'unit' => 'Buah'],
                ['name' => 'Bloknote & Pulpen', 'qty' => '1', 'unit' => 'Set'],
            ],
            'recipients' => [
                [
                    'user_id' => $this->petugas->id,
                    'recipient_name' => $this->petugas->name,
                    'position_unit' => 'Mitra BPS',
                    'origin_role' => 'Kecamatan Lasusua',
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.basts.officers.index'));

        $bast = OfficerBast::where('assignment_id', $this->assignment->id)->first();
        $this->assertNotNull($bast);
        $this->assertStringContainsString('Buku Panduan / Modul Pelatihan (1 Eks)', $bast->description);
        $this->assertStringContainsString('Tas Pelatihan (2 Buah)', $bast->description);
        $this->assertStringContainsString('Bloknote & Pulpen (1 Set)', $bast->description);

        $detailed = $bast->parsed_items_detailed;
        $this->assertCount(3, $detailed);
        $this->assertEquals('Buku Panduan / Modul Pelatihan', $detailed[0]['name']);
        $this->assertEquals('1', $detailed[0]['qty']);
        $this->assertEquals('Eks', $detailed[0]['unit']);
        $this->assertEquals('Tas Pelatihan', $detailed[1]['name']);
        $this->assertEquals('2', $detailed[1]['qty']);
        $this->assertEquals('Buah', $detailed[1]['unit']);

        // Verify PDF preview endpoint renders correctly with quantities
        $pdfResponse = $this->actingAs($this->admin)->get(route('admin.basts.officers.preview', $bast));
        $pdfResponse->assertStatus(200);
    }

    public function test_admin_can_create_officer_bast_with_maker_digital_signature()
    {
        $fakePngBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $response = $this->actingAs($this->admin)->post(route('admin.basts.officers.store'), [
            'assignment_id' => $this->assignment->id,
            'bast_type' => 'kantor_ke_petugas',
            'receipt_category' => 'PELATIHAN',
            'document_title' => 'Tanda Terima Perlengkapan Pelatihan',
            'maker_name' => 'Muh. Robbith Dinak, S.Tr.Stat.',
            'maker_nip' => '199907202023021001',
            'maker_signature_data' => $fakePngBase64,
            'status' => 'finalized',
            'items' => [
                ['name' => 'Buku Panduan', 'qty' => '1', 'unit' => 'Eks'],
            ],
            'recipients' => [
                [
                    'user_id' => $this->petugas->id,
                    'recipient_name' => $this->petugas->name,
                    'position_unit' => 'Mitra BPS',
                    'origin_role' => 'Kecamatan Lasusua',
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.basts.officers.index'));

        $bast = OfficerBast::where('maker_name', 'Muh. Robbith Dinak, S.Tr.Stat.')->first();
        $this->assertNotNull($bast);
        $this->assertNotNull($bast->maker_signature_path);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($bast->maker_signature_path);

        // Verify PDF preview endpoint renders correctly with maker signature
        $pdfResponse = $this->actingAs($this->admin)->get(route('admin.basts.officers.preview', $bast));
        $pdfResponse->assertStatus(200);
    }

    public function test_admin_can_download_bast_officer_excel_and_csv_templates()
    {
        $csvResponse = $this->actingAs($this->admin)->get(route('admin.basts.officers.template.download', ['format' => 'csv']));
        $csvResponse->assertStatus(200);
        $csvResponse->assertHeader('Content-Type', 'text/csv; charset=utf-8');

        $xlsxResponse = $this->actingAs($this->admin)->get(route('admin.basts.officers.template.download', ['format' => 'xlsx']));
        $xlsxResponse->assertStatus(200);
        $xlsxResponse->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_admin_can_parse_recipients_from_excel_file()
    {
        $csvContent = "NO,Nama,Penugasan,Kecamatan Asal\n";
        $csvContent .= "1,{$this->petugas->name},Petugas Pendataan Lapangan,Lasusua\n";
        $csvContent .= "2,Budi Santoso Baru,Mitra Lapangan,Wawo\n";

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('daftar_penerima.csv', $csvContent);

        $response = $this->actingAs($this->admin)->post(route('admin.basts.officers.parse-recipients-excel'), [
            'excel_file' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'count' => 2,
        ]);

        $data = $response->json('recipients');
        $this->assertEquals($this->petugas->name, $data[0]['recipient_name']);
        $this->assertEquals($this->petugas->id, $data[0]['user_id']);
        $this->assertTrue($data[0]['matched']);

        $this->assertEquals('Budi Santoso Baru', $data[1]['recipient_name']);
        $this->assertNull($data[1]['user_id']);
        $this->assertFalse($data[1]['matched']);
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
            'handover_category' => 'KUES',
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
            'handover_category' => 'KUES',
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

    public function test_officer_signature_is_permanently_preserved_when_bast_document_is_edited()
    {
        // 1. Buat BAST Blanko Petugas
        $bast = OfficerBast::create([
            'bast_number' => 'BAST-BLANKO/2026/XI/9999',
            'assignment_id' => $this->assignment->id,
            'bast_type' => 'kantor_ke_petugas',
            'receipt_category' => 'PELATIHAN',
            'bast_date' => now()->toDateString(),
            'document_title' => 'Tanda Terima Perlengkapan Awal',
            'maker_name' => 'Koordinator Logistik',
            'description' => "1. Tas Pelatihan (1 Buah)\n2. Modul (1 Eks)",
            'status' => 'finalized',
        ]);

        $bast->syncRecipients([
            [
                'user_id' => $this->petugas->id,
                'recipient_name' => $this->petugas->name,
                'position_unit' => 'Mitra BPS',
                'origin_role' => 'Lasusua',
                'sort_order' => 1,
            ],
        ]);

        // 2. Petugas menandatangani dokumen
        $recipient = $bast->recipients()->first();
        $this->assertNotNull($recipient);
        $fakeSign = 'signatures/officer_test_sig_' . uniqid() . '.png';
        $recipient->update([
            'signature_path' => $fakeSign,
            'signed_at' => now(),
            'goods_received' => true,
            'goods_received_at' => now(),
        ]);

        // 3. Admin / Pembuat mengedit dokumen (mengubah judul, menambah item, mengubah catatan)
        $updatePayload = [
            'assignment_id' => $this->assignment->id,
            'bast_type' => 'kantor_ke_petugas',
            'receipt_category' => 'PELATIHAN',
            'document_title' => 'Tanda Terima Perlengkapan Hasil Revisi',
            'maker_name' => 'Koordinator Logistik Baru',
            'items' => [
                ['name' => 'Tas Pelatihan', 'qty' => '1', 'unit' => 'Buah'],
                ['name' => 'Modul Tambahan Revisi', 'qty' => '2', 'unit' => 'Eks'],
            ],
            'notes' => 'Catatan revisi barang tambahan',
            'status' => 'finalized',
            'recipients' => [
                [
                    'recipient_id' => $recipient->id,
                    'user_id' => $this->petugas->id,
                    'recipient_name' => $this->petugas->name,
                    'position_unit' => 'Mitra BPS Lapangan',
                    'origin_role' => 'Kecamatan Lasusua',
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.basts.officers.update', $bast), $updatePayload);
        $response->assertRedirect(route('admin.basts.officers.index'));

        // 4. Pastikan tanda tangan petugas tetap ada, aman, dan tidak terhapus
        $recipient->refresh();
        $this->assertEquals($fakeSign, $recipient->signature_path, 'Tanda tangan petugas wajib tetap tersimpan dan tidak boleh terhapus saat dokumen diedit.');
        $this->assertTrue($recipient->goods_received, 'Status penerimaan barang petugas wajib tetap true.');
        $this->assertNotNull($recipient->signed_at, 'Waktu tanda tangan petugas wajib tetap tersimpan.');
        $this->assertEquals('Mitra BPS Lapangan', $recipient->position_unit);
    }

    public function test_petugas_can_create_bast_dokumen_terisi_with_official_bps_fields()
    {
        $payload = [
            'assignment_id' => $this->assignment->id,
            'bast_type' => 'petugas_ke_kantor',
            'bast_number' => 'B- 1382/74081/VS.330/2026',
            'document_title' => 'BERITA ACARA SERAH TERIMA HASIL PEKERJAAN PETUGAS PENDATAAN LAPANGAN',
            'activity_work_name' => 'Pendataan Lapangan Survei Pertanian Terintegrasi (SITASI) Tahun 2026 pada Badan Pusat Statistik Kabupaten Kolaka Utara',
            'maker_name' => 'Hidayatullah, SE',
            'maker_nip' => '198510292011011009',
            'first_party_position' => 'Pejabat Pembuat Komitmen Badan Pusat Statistik Kabupaten Kolaka Utara',
            'institution_name' => 'BPS Kabupaten Kolaka Utara',
            'first_party_address' => 'Jalan Protokol Poros DPRD, Kec. Lasusua, Kolaka Utara Sulawesi Tenggara',
            'second_party_nik' => '7408011212840002',
            'second_party_address' => 'Desa Watuliu, Kecamatan Lasusua',
            'contract_number' => 'PPK 7408/08/10/2026/001',
            'contract_date' => '2026-10-06',
            'doc_count' => '30 Dokumen',
            'document_place' => 'Badan Pusat Statistik Kabupaten Kolaka Utara',
            'status' => 'submitted',
        ];

        $response = $this->actingAs($this->petugas)->post(route('petugas.basts.officers.store'), $payload);
        $response->assertRedirect(route('petugas.basts.officers.index', ['type' => 'petugas_ke_kantor']));

        $bast = OfficerBast::where('bast_number', 'B- 1382/74081/VS.330/2026')->first();
        $this->assertNotNull($bast);
        $this->assertEquals('Hidayatullah, SE', $bast->maker_name);
        $this->assertEquals('198510292011011009', $bast->maker_nip);
        $this->assertEquals('Pejabat Pembuat Komitmen Badan Pusat Statistik Kabupaten Kolaka Utara', $bast->first_party_position);
        $this->assertEquals('7408011212840002', $bast->second_party_nik);
        $this->assertEquals('PPK 7408/08/10/2026/001', $bast->contract_number);
        $this->assertEquals('2026-10-06', $bast->contract_date->format('Y-m-d'));
        $this->assertEquals('30 Dokumen', $bast->doc_count);

        // Preview PDF rendering test
        $preview = $this->actingAs($this->petugas)->get(route('petugas.basts.officers.preview', $bast));
        $preview->assertStatus(200);
        $preview->assertHeader('Content-Type', 'application/pdf');

        $html = view('pdf.bast_officer', ['bast' => $bast])->render();
        $this->assertStringContainsString('BERITA ACARA SERAH TERIMA', $html);
        $this->assertStringContainsString('HASIL PEKERJAAN PETUGAS PENDATAAN LAPANGAN', $html);
        $this->assertStringContainsString('Hidayatullah, SE', $html);
        $this->assertStringContainsString($this->petugas->name, $html);
        $this->assertStringContainsString('7408011212840002', $html);
        $this->assertStringContainsString('PPK 7408/08/10/2026/001', $html);
        $this->assertStringContainsString('30 Dokumen', $html);
        $this->assertStringContainsString('PIHAK PERTAMA', $html);
        $this->assertStringContainsString('PIHAK KEDUA', $html);
    }
}
