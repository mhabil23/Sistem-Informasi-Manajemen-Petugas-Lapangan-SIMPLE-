<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Respondent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use ZipArchive;

class ExcelImportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $petugas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Administrator SIMPLE',
            'username' => 'admin_simple',
            'email' => 'admin@bps.go.id',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->petugas = User::factory()->create([
            'name' => 'Evayana',
            'username' => 'evayana',
            'email' => 'evayana@bps.go.id',
            'sobat_id' => '740801001',
            'role' => 'petugas',
            'status' => 'active',
        ]);
    }

    public function test_admin_can_download_excel_csv_template()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.assignments.template.download'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=Template_Kegiatan_dan_Sampel_SIMPEL.csv');
        $this->assertStringContainsString('Nama', $response->streamedContent());
        $this->assertStringContainsString('Kecamatan Asal', $response->streamedContent());
        $this->assertStringContainsString('Penugasan', $response->streamedContent());
    }

    public function test_admin_can_download_excel_xlsx_template()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.assignments.template.download', ['format' => 'xlsx']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=Template_Kegiatan_dan_Sampel_SIMPEL.xlsx');
    }

    public function test_admin_can_import_csv_and_auto_allocate_officers_and_respondents()
    {
        $csvContent = "\xEF\xBB\xBF" .
            "ID SOBAT,Nama,Nama Kegiatan,Pembebanan Anggaran,Penugasan,Wilayah Tugas,Nama Kepala Keluarga Sampel\n" .
            "740801001,Evayana,Survey Pola Konsumsi Rumah Tangga,DIPA BPS Kabupaten Kolaka Utara Tahun Anggaran 2026,Petugas Pendataan Lapangan,Ranteangin,Andi\n" .
            "740801001,Evayana,Survey Pola Konsumsi Rumah Tangga,DIPA BPS Kabupaten Kolaka Utara Tahun Anggaran 2026,Petugas Pendataan Lapangan,Ranteangin,Anyutama\n" .
            "740801002,Nurfadillah NB,Survey Pola Konsumsi Rumah Tangga,DIPA BPS Kabupaten Kolaka Utara Tahun Anggaran 2026,Petugas Pendataan Lapangan,Lasusua,Kamase\n";

        $file = UploadedFile::fake()->createWithContent('7408_SPKRT_2027.csv', $csvContent);

        $response = $this->actingAs($this->admin)->post(route('admin.assignments.import'), [
            'excel_file' => $file,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(14)->toDateString(),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check assignment was created
        $this->assertDatabaseHas('assignments', [
            'title' => 'Survey Pola Konsumsi Rumah Tangga',
            'basis_dipa' => 'DIPA BPS Kabupaten Kolaka Utara Tahun Anggaran 2026',
        ]);

        $assignment = Assignment::where('title', 'Survey Pola Konsumsi Rumah Tangga')->first();
        $this->assertNotNull($assignment);

        // Check Evayana is assigned
        $this->assertTrue($assignment->officers->contains($this->petugas));

        // Check new officer Nurfadillah NB was auto-created and assigned
        $this->assertDatabaseHas('users', [
            'name' => 'Nurfadillah NB',
            'sobat_id' => '740801002',
            'role' => 'petugas',
        ]);

        $newOfficer = User::where('name', 'Nurfadillah NB')->first();
        $this->assertTrue($assignment->officers->contains($newOfficer));

        // Check respondents were created and allocated to the right officer
        $this->assertDatabaseHas('respondents', [
            'assignment_id' => $assignment->id,
            'officer_id' => $this->petugas->id,
            'family_head_name' => 'Andi',
            'district' => 'Ranteangin',
        ]);

        $this->assertDatabaseHas('respondents', [
            'assignment_id' => $assignment->id,
            'officer_id' => $this->petugas->id,
            'family_head_name' => 'Anyutama',
            'district' => 'Ranteangin',
        ]);

        $this->assertDatabaseHas('respondents', [
            'assignment_id' => $assignment->id,
            'officer_id' => $newOfficer->id,
            'family_head_name' => 'Kamase',
            'district' => 'Lasusua',
        ]);

        // Check target count matches
        $this->assertEquals(3, $assignment->fresh()->target_respondents);
    }

    public function test_admin_can_import_real_xlsx_file()
    {
        // Dynamically build a valid minimal XLSX archive
        $tmpXlsx = tempnam(sys_get_temp_dir(), 'test_xlsx') . '.xlsx';
        $zip = new ZipArchive();
        $zip->open($tmpXlsx, ZipArchive::CREATE);

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>
</Types>');

        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>');

        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>
</Relationships>');

        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Contoh Database SIMPLE" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>');

        $sharedStrings = [
            'ID SOBAT',
            'Nama',
            'Nama Kegiatan',
            'Pembebanan Anggaran',
            'Penugasan',
            'Wilayah Tugas',
            'Nama Kepala Keluarga Sampel',
            '740801001',
            'Evayana',
            'Survei Sosial Ekonomi Nasional',
            'DIPA BPS Kolaka Utara',
            'Petugas Pendataan Lapangan',
            'Katoi',
            'Keluarga Budi',
        ];

        $ssXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';
        foreach ($sharedStrings as $s) {
            $ssXml .= '<si><t>' . htmlspecialchars($s) . '</t></si>';
        }
        $ssXml .= '</sst>';
        $zip->addFromString('xl/sharedStrings.xml', $ssXml);

        $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetData>
    <row r="1">
      <c r="A1" t="s"><v>0</v></c>
      <c r="B1" t="s"><v>1</v></c>
      <c r="C1" t="s"><v>2</v></c>
      <c r="D1" t="s"><v>3</v></c>
      <c r="E1" t="s"><v>4</v></c>
      <c r="F1" t="s"><v>5</v></c>
      <c r="G1" t="s"><v>6</v></c>
    </row>
    <row r="2">
      <c r="A2" t="s"><v>7</v></c>
      <c r="B2" t="s"><v>8</v></c>
      <c r="C2" t="s"><v>9</v></c>
      <c r="D2" t="s"><v>10</v></c>
      <c r="E2" t="s"><v>11</v></c>
      <c r="F2" t="s"><v>12</v></c>
      <c r="G2" t="s"><v>13</v></c>
    </row>
  </sheetData>
</worksheet>';
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
        $zip->close();

        $uploadedFile = new UploadedFile($tmpXlsx, '7408_SPKRT_2027.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $response = $this->actingAs($this->admin)->post(route('admin.assignments.import'), [
            'excel_file' => $uploadedFile,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('assignments', [
            'title' => 'Survei Sosial Ekonomi Nasional',
        ]);

        $this->assertDatabaseHas('respondents', [
            'family_head_name' => 'Keluarga Budi',
            'district' => 'Katoi',
        ]);

        @unlink($tmpXlsx);
    }

    public function test_admin_can_preview_excel_via_ajax()
    {
        $csvContent = "\xEF\xBB\xBF" .
            "ID SOBAT,Nama,Nama Kegiatan,Pembebanan Anggaran,Penugasan,Wilayah Tugas,Nama Kepala Keluarga Sampel\n" .
            "740801001,Evayana,Survey Pola Konsumsi Rumah Tangga,DIPA BPS Kabupaten Kolaka Utara,Petugas Pendataan Lapangan,Ranteangin,Andi\n" .
            "740801001,Evayana,Survey Pola Konsumsi Rumah Tangga,DIPA BPS Kabupaten Kolaka Utara,Petugas Pendataan Lapangan,Ranteangin,Anyutama\n";

        $file = UploadedFile::fake()->createWithContent('preview.csv', $csvContent);

        $response = $this->actingAs($this->admin)->postJson(route('admin.assignments.preview-excel'), [
            'excel_file' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'title' => 'Survey Pola Konsumsi Rumah Tangga',
            'basis_dipa' => 'DIPA BPS Kabupaten Kolaka Utara',
            'total_samples' => 2,
            'matched_officer_ids' => [$this->petugas->id],
        ]);
    }

    public function test_admin_can_create_assignment_with_excel_upload_in_modal()
    {
        $csvContent = "\xEF\xBB\xBF" .
            "ID SOBAT,Nama,Nama Kegiatan,Pembebanan Anggaran,Penugasan,Wilayah Tugas,Nama Kepala Keluarga Sampel\n" .
            "740801001,Evayana,Pendataan Usaha Pertanian 2026,DIPA 2026,PPL,Lasusua,Petani Subur\n";

        $file = UploadedFile::fake()->createWithContent('upload.csv', $csvContent);

        $response = $this->actingAs($this->admin)->post(route('admin.assignments.store'), [
            'title' => 'Pendataan Usaha Pertanian 2026',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
            'location' => 'Kecamatan Lasusua',
            'status' => 'ongoing',
            'excel_file' => $file,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('assignments', [
            'title' => 'Pendataan Usaha Pertanian 2026',
        ]);
        $this->assertDatabaseHas('respondents', [
            'family_head_name' => 'Petani Subur',
            'officer_id' => $this->petugas->id,
        ]);
    }

    public function test_admin_can_preview_and_upload_exact_officer_template()
    {
        $csvContent = "\xEF\xBB\xBF" .
            "NO,Nama,Jenis Kelamin,Penugasan,Kecamatan Asal,Kelas,BANK\n" .
            "1,Evayana,Perempuan,Petugas Pendataan Lapangan,Ranteangin,1 - C (Kifaat),\n" .
            "2,Nurfadillah NB,Perempuan,Petugas Pendataan Lapangan,Ranteangin,1 - C (Kifaat),\n" .
            "3,Nurul Hidayah,Perempuan,Petugas Pendataan Lapangan,Lambai,2 - D (Berlian Bawah),\n";

        $file = UploadedFile::fake()->createWithContent('daftar_petugas.csv', $csvContent);

        $response = $this->actingAs($this->admin)->postJson(route('admin.assignments.preview-excel'), [
            'excel_file' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'location' => 'Kecamatan Ranteangin, Lambai',
            'matched_officer_ids' => [$this->petugas->id],
        ]);
        $this->assertCount(3, $response->json('officers'));
    }
}
