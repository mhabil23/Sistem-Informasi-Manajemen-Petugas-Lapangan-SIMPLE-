<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\OfficerBast;
use App\Models\User;
use App\Services\GoogleSheetService;
use App\Services\SimpleExcelParser;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class OfficerBastController extends Controller
{
    protected GoogleSheetService $sheetService;
    protected SimpleExcelParser $excelParser;

    public function __construct(GoogleSheetService $sheetService, SimpleExcelParser $excelParser)
    {
        $this->sheetService = $sheetService;
        $this->excelParser = $excelParser;
    }

    public function index(Request $request)
    {
        $query = OfficerBast::with(['officer', 'assignment', 'recipients.user']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('bast_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('document_title', 'like', "%{$search}%")
                  ->orWhere('activity_subtitle', 'like', "%{$search}%")
                  ->orWhereHas('officer', fn ($o) => $o->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('recipients', fn ($r) => $r->where('recipient_name', 'like', "%{$search}%"))
                  ->orWhereHas('assignment', fn ($a) => $a->where('title', 'like', "%{$search}%"));
            });
        }

        if ($assignmentId = $request->input('assignment_id')) {
            $query->where('assignment_id', $assignmentId);
        }

        if ($officerId = $request->input('officer_id')) {
            $query->forOfficer((int) $officerId);
        }

        if ($bastType = $request->input('bast_type')) {
            if ($bastType === 'kantor_ke_petugas') {
                $query->where(function ($q) {
                    $q->where('bast_type', 'kantor_ke_petugas')
                      ->orWhereNull('bast_type');
                });
            } else {
                $query->where('bast_type', $bastType);
            }
        }

        $totalBasts = OfficerBast::count();
        $thisMonthBasts = OfficerBast::whereMonth('bast_date', now()->month)
                                     ->whereYear('bast_date', now()->year)
                                     ->count();

        $basts = $query->latest('bast_date')->latest('id')->paginate(10)->withQueryString();

        $assignments = Assignment::with(['officers' => fn ($q) => $q->orderBy('name')])
            ->orderBy('title')
            ->get();
        $officers = User::fieldStaff()->where('status', 'active')->orderBy('name')->get();
        $pegawaiList = User::getPegawaiSigners();

        return view('admin.basts.officers.index', compact(
            'basts',
            'totalBasts',
            'thisMonthBasts',
            'assignments',
            'officers',
            'pegawaiList'
        ));
    }

    public function store(Request $request)
    {
        [$bastData, $recipients] = $this->validateAndPrepare($request);

        if (empty($bastData['bast_number'])) {
            $bastData['bast_number'] = OfficerBast::generateBastNumber($bastData['bast_type']);
        }

        $bast = DB::transaction(function () use ($bastData, $recipients) {
            $created = OfficerBast::create($bastData);
            $created->syncRecipients($recipients);

            return $created;
        });

        $bast->load(['officer', 'recipients']);
        $this->syncSheetRow($bast, true);

        ActivityLog::log(
            'create',
            'basts',
            "Menerbitkan BAST Petugas nomor {$bast->bast_number} ({$bast->recipient_summary})."
        );

        return redirect()->route('admin.basts.officers.index')
            ->with('success', "BAST Petugas '{$bast->bast_number}' berhasil dibuat dan siap diunduh!");
    }

    public function update(Request $request, OfficerBast $bast)
    {
        [$bastData, $recipients] = $this->validateAndPrepare($request, $bast);

        if (empty($bastData['bast_number'])) {
            $bastData['bast_number'] = $bast->bast_number;
        }

        DB::transaction(function () use ($bast, $bastData, $recipients) {
            $bast->update($bastData);
            $bast->syncRecipients($recipients);
        });

        $bast->load(['officer', 'recipients']);
        $this->syncSheetRow($bast, false);

        ActivityLog::log(
            'update',
            'basts',
            "Memperbarui dokumen BAST Petugas nomor {$bast->bast_number} ({$bast->recipient_summary})."
        );

        return redirect()->route('admin.basts.officers.index')
            ->with('success', "BAST Petugas '{$bast->bast_number}' berhasil diperbarui!");
    }

    public function downloadPdf(OfficerBast $bast)
    {
        return $this->renderPdf($bast)->download($bast->pdfFilename());
    }

    public function previewPdf(OfficerBast $bast)
    {
        return $this->renderPdf($bast)->stream($bast->pdfFilename());
    }

    public function destroy(OfficerBast $bast)
    {
        $number = $bast->bast_number;
        $id = $bast->id;
        $bast->delete();

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->delete('officer_basts', $id, true);
        }

        ActivityLog::log(
            'delete',
            'basts',
            "Menghapus dokumen BAST Petugas nomor {$number}."
        );

        return back()->with('success', "Dokumen BAST '{$number}' berhasil dihapus.");
    }

    /**
     * Aksi massal (pilih lebih dari satu dokumen BAST Petugas via checkbox).
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => ['required', Rule::in(['set_finalized', 'set_draft', 'delete'])],
            'bast_ids' => ['required', 'array', 'min:1'],
            'bast_ids.*' => ['integer', 'exists:officer_basts,id'],
        ], [
            'bast_ids.required' => 'Pilih minimal satu dokumen BAST Petugas untuk melakukan aksi massal.',
            'bast_ids.min' => 'Pilih minimal satu dokumen BAST Petugas untuk melakukan aksi massal.',
        ]);

        $action = $validated['action'];
        $bastIds = array_unique($validated['bast_ids']);
        $basts = OfficerBast::whereIn('id', $bastIds)->get();
        $affectedCount = 0;
        $msg = '';

        switch ($action) {
            case 'set_finalized':
                foreach ($basts as $bast) {
                    $bast->update(['status' => 'finalized']);
                    if ($this->sheetService->isConfigured()) {
                        $this->sheetService->update('officer_basts', $bast->id, [
                            'status' => 'finalized',
                            'updated_at' => now()->toISOString(),
                        ]);
                    }
                    $affectedCount++;
                }
                $msg = "Sebanyak {$affectedCount} dokumen BAST Petugas berhasil diverifikasi (Status: Sah).";
                break;

            case 'set_draft':
                foreach ($basts as $bast) {
                    $bast->update(['status' => 'draft']);
                    if ($this->sheetService->isConfigured()) {
                        $this->sheetService->update('officer_basts', $bast->id, [
                            'status' => 'draft',
                            'updated_at' => now()->toISOString(),
                        ]);
                    }
                    $affectedCount++;
                }
                $msg = "Sebanyak {$affectedCount} dokumen BAST Petugas berhasil diubah menjadi Draft.";
                break;

            case 'delete':
                foreach ($basts as $bast) {
                    $id = $bast->id;
                    $bast->delete();
                    if ($this->sheetService->isConfigured()) {
                        $this->sheetService->delete('officer_basts', $id, true);
                    }
                    $affectedCount++;
                }
                $msg = "Sebanyak {$affectedCount} dokumen BAST Petugas berhasil dihapus.";
                break;
        }

        ActivityLog::log(
            'bulk_update',
            'basts',
            "Melakukan aksi massal '{$action}' pada {$affectedCount} dokumen BAST Petugas."
        );

        return back()->with('success', $msg);
    }

    /**
     * Download format template Excel / CSV untuk daftar penerima BAST.
     */
    public function downloadTemplate(Request $request)
    {
        $headersList = [
            'NO',
            'Nama',
            'Penugasan',
            'Kecamatan Asal',
        ];

        if ($request->query('format') === 'xlsx') {
            $tmpPath = tempnam(sys_get_temp_dir(), 'tpl_bast_') . '.xlsx';
            $this->createCleanRecipientsXlsxTemplate($tmpPath, $headersList);

            return response()->stream(function () use ($tmpPath) {
                readfile($tmpPath);
                @unlink($tmpPath);
            }, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename=Template_Daftar_Penerima_BAST.xlsx',
            ]);
        }

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename=Template_Daftar_Penerima_BAST.csv',
        ];

        $callback = function () use ($headersList) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $headersList);
            fputcsv($handle, [1, 'Evayana', 'Petugas Pendataan Lapangan', 'Ranteangin']);
            fputcsv($handle, [2, 'Nurfadillah NB', 'Petugas Pendataan Lapangan', 'Ranteangin']);
            fputcsv($handle, [3, 'Nurul Hidayah', 'Petugas Pendataan Lapangan', 'Lambai']);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Parse file Excel / CSV dan ekstrak daftar petugas / penerima untuk BAST.
     */
    public function parseExcelRecipients(Request $request)
    {
        $request->validate([
            'excel_file' => ['required', 'file', 'extensions:xlsx,xls,csv,txt', 'max:10240'],
        ], [
            'excel_file.required' => 'Pilih berkas Excel (.xlsx) atau CSV terlebih dahulu.',
            'excel_file.extensions' => 'Format berkas harus berupa .xlsx, .xls, atau .csv.',
            'excel_file.max' => 'Ukuran berkas maksimal 10 MB.',
        ]);

        $file = $request->file('excel_file');
        $ext = $file->getClientOriginalExtension();

        try {
            $parsedRows = $this->excelParser->parse($file->getRealPath(), $ext);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membaca berkas Excel: ' . $e->getMessage(),
            ], 422);
        }

        if (empty($parsedRows)) {
            return response()->json([
                'success' => false,
                'message' => 'Berkas Excel kosong atau baris data tidak terbaca.',
            ], 422);
        }

        $allOfficers = User::fieldStaff()->get();
        $recipientsMap = [];

        foreach ($parsedRows as $row) {
            $nameVal = '';
            foreach ($row as $key => $val) {
                $k = strtolower(trim((string) $key));
                if (in_array($k, ['nama', 'nama_petugas', 'nama_peserta', 'nama_penerima', 'nama_lengkap', 'name', 'petugas'], true)) {
                    $nameVal = trim((string) $val);
                    break;
                }
            }
            if ($nameVal === '') {
                foreach ($row as $key => $val) {
                    if (str_contains(strtolower((string) $key), 'nama')) {
                        $nameVal = trim((string) $val);
                        if ($nameVal !== '') break;
                    }
                }
            }

            if ($nameVal === '' || strtolower($nameVal) === 'nama' || strtolower($nameVal) === 'nama petugas') {
                continue;
            }

            $positionVal = '';
            foreach ($row as $key => $val) {
                $k = strtolower(trim((string) $key));
                if (in_array($k, ['penugasan', 'jabatan', 'posisi', 'unit_kerja', 'role', 'jabatan_unit_kerja'], true)) {
                    $positionVal = trim((string) $val);
                    if ($positionVal !== '') break;
                }
            }

            $originVal = '';
            foreach ($row as $key => $val) {
                $k = strtolower(trim((string) $key));
                if (in_array($k, ['kecamatan_asal', 'asal', 'wilayah', 'wilayah_tugas', 'kecamatan', 'alamat', 'bertugas_sebagai', 'lokasi'], true)) {
                    $originVal = trim((string) $val);
                    if ($originVal !== '') break;
                }
            }

            $norm = strtolower($nameVal);
            if (!isset($recipientsMap[$norm])) {
                $matchedUser = $allOfficers->first(function ($u) use ($nameVal) {
                    return strtolower(trim($u->name)) === strtolower(trim($nameVal));
                });

                $finalPosition = $positionVal !== ''
                    ? $positionVal
                    : ($matchedUser ? ($matchedUser->position ?: ($matchedUser->isPegawai() ? 'Pegawai BPS' : 'Mitra')) : 'Mitra');

                $finalOrigin = $originVal !== ''
                    ? $originVal
                    : ($matchedUser?->address ?: '-');

                $recipientsMap[$norm] = [
                    'user_id' => $matchedUser?->id,
                    'recipient_name' => $matchedUser?->name ?? $nameVal,
                    'position_unit' => $finalPosition,
                    'origin_role' => $finalOrigin,
                    'matched' => (bool) $matchedUser,
                ];
            }
        }

        $recipients = array_values($recipientsMap);

        if (empty($recipients)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ditemukan data penerima pada kolom Nama dalam file yang diunggah.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'count' => count($recipients),
            'recipients' => $recipients,
            'message' => "Berhasil membaca " . count($recipients) . " penerima dari berkas Excel.",
        ]);
    }

    /**
     * Buat file XLSX template penerima BAST.
     */
    protected function createCleanRecipientsXlsxTemplate(string $outputPath, array $headers): void
    {
        $zip = new \ZipArchive();
        if ($zip->open($outputPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \Exception("Tidak dapat membuat berkas Excel template.");
        }

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
    <sheet name="Daftar Penerima" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>');

        $strings = array_merge($headers, [
            '1', 'Evayana', 'Petugas Pendataan Lapangan', 'Ranteangin',
            '2', 'Nurfadillah NB', 'Petugas Pendataan Lapangan', 'Ranteangin',
            '3', 'Nurul Hidayah', 'Petugas Pendataan Lapangan', 'Lambai',
        ]);

        $ssXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="'.count($strings).'" uniqueCount="'.count($strings).'">';
        foreach ($strings as $s) {
            $ssXml .= '<si><t>' . htmlspecialchars((string) $s, ENT_XML1) . '</t></si>';
        }
        $ssXml .= '</sst>';
        $zip->addFromString('xl/sharedStrings.xml', $ssXml);

        $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <cols>
    <col min="1" max="1" width="8" customWidth="1"/>
    <col min="2" max="2" width="30" customWidth="1"/>
    <col min="3" max="3" width="30" customWidth="1"/>
    <col min="4" max="4" width="25" customWidth="1"/>
  </cols>
  <sheetData>
    <row r="1">
      <c r="A1" t="s"><v>0</v></c>
      <c r="B1" t="s"><v>1</v></c>
      <c r="C1" t="s"><v>2</v></c>
      <c r="D1" t="s"><v>3</v></c>
    </row>
    <row r="2">
      <c r="A2" t="s"><v>4</v></c>
      <c r="B2" t="s"><v>5</v></c>
      <c r="C2" t="s"><v>6</v></c>
      <c r="D2" t="s"><v>7</v></c>
    </row>
    <row r="3">
      <c r="A3" t="s"><v>8</v></c>
      <c r="B3" t="s"><v>9</v></c>
      <c r="C3" t="s"><v>10</v></c>
      <c r="D3" t="s"><v>11</v></c>
    </row>
    <row r="4">
      <c r="A4" t="s"><v>12</v></c>
      <c r="B4" t="s"><v>13</v></c>
      <c r="C4" t="s"><v>14</v></c>
      <c r="D4" t="s"><v>15</v></c>
    </row>
  </sheetData>
</worksheet>';
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
        $zip->close();
    }

    private function validateAndPrepare(Request $request, ?OfficerBast $existing = null): array
    {
        $bastType = $request->input('bast_type', $existing?->bast_type ?? 'kantor_ke_petugas');

        $uniqueRule = Rule::unique('officer_basts', 'bast_number');
        if ($existing) {
            $uniqueRule->ignore($existing->id);
        }

        $rules = [
            'assignment_id' => ['required', 'exists:assignments,id'],
            'bast_type' => ['nullable', Rule::in(['kantor_ke_petugas', 'petugas_ke_kantor'])],
            'bast_number' => ['nullable', 'string', 'max:100', $uniqueRule],
            'notes' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in(['draft', 'finalized', 'archived'])],
        ];

        if ($bastType === 'petugas_ke_kantor') {
            $rules['officer_id'] = ['required', 'exists:users,id'];
            $rules['description'] = ['nullable', 'string'];
            $rules['document_title'] = ['nullable', 'string', 'max:255'];
            $rules['activity_subtitle'] = ['nullable', 'string', 'max:255'];
            $rules['institution_name'] = ['nullable', 'string', 'max:255'];
            $rules['document_place'] = ['nullable', 'string', 'max:255'];
            $rules['maker_name'] = ['nullable', 'string', 'max:255'];
            $rules['maker_nip'] = ['nullable', 'string', 'max:100'];
            $rules['first_party_position'] = ['nullable', 'string', 'max:255'];
            $rules['first_party_address'] = ['nullable', 'string', 'max:500'];
            $rules['second_party_nik'] = ['nullable', 'string', 'max:30'];
            $rules['second_party_address'] = ['nullable', 'string', 'max:500'];
            $rules['contract_number'] = ['nullable', 'string', 'max:255'];
            $rules['contract_date'] = ['nullable', 'date'];
            $rules['doc_count'] = ['nullable', 'max:100'];
            $rules['activity_work_name'] = ['nullable', 'string', 'max:500'];
        } else {
            $rules['officer_id'] = ['nullable', 'exists:users,id'];
            $rules['receipt_category'] = ['nullable', Rule::in(['PELATIHAN', 'ATK', 'KUES', 'TIMBANGAN', 'ATK_RESPONDEN', 'RESPONDEN', 'PERLENGKAPAN'])];
            $rules['description'] = ['nullable', 'string'];
            $rules['document_title'] = ['nullable', 'string', 'max:255'];
            $rules['activity_subtitle'] = ['nullable', 'string', 'max:255'];
            $rules['institution_name'] = ['nullable', 'string', 'max:255'];
            $rules['document_place'] = ['nullable', 'string', 'max:255'];
            $rules['maker_name'] = ['nullable', 'string', 'max:255'];
            $rules['maker_nip'] = ['nullable', 'string', 'max:100'];
            $rules['maker_signature_data'] = ['nullable', 'string'];
            $rules['items'] = ['nullable', 'array'];
            $rules['items.*'] = ['nullable'];
            $rules['items.*.name'] = ['nullable', 'string', 'max:255'];
            $rules['items.*.qty'] = ['nullable'];
            $rules['items.*.unit'] = ['nullable', 'string', 'max:50'];
            $rules['recipients'] = ['nullable', 'array'];
            $rules['recipients.*.recipient_id'] = ['nullable', 'integer'];
            $rules['recipients.*.id'] = ['nullable', 'integer'];
            $rules['recipients.*.user_id'] = ['nullable', 'exists:users,id'];
            $rules['recipients.*.recipient_name'] = ['nullable', 'string', 'max:255'];
            $rules['recipients.*.position_unit'] = ['nullable', 'string', 'max:255'];
            $rules['recipients.*.origin_role'] = ['nullable', 'string', 'max:255'];
        }

        $validated = $request->validate($rules, [
            'assignment_id.required' => 'Kegiatan wajib dipilih.',
            'officer_id.required' => 'Petugas wajib dipilih.',
            'description.required' => 'Rincian serah terima dokumen/perlengkapan wajib diisi.',
            'bast_number.unique' => 'Nomor BAST tersebut sudah terdaftar.',
        ]);

        $assignment = Assignment::findOrFail($validated['assignment_id']);
        $bastDate = $existing && $existing->bast_date
            ? $existing->bast_date->toDateString()
            : now()->toDateString();

        if ($bastType === 'petugas_ke_kantor') {
            $officer = User::find($validated['officer_id']);
            $year = date('Y', strtotime($bastDate));
            $defaultWorkName = $validated['activity_work_name']
                ?? ($validated['activity_subtitle'] ?? "Pendataan Lapangan {$assignment->title} Tahun {$year} pada Badan Pusat Statistik Kabupaten Kolaka Utara");

            $bastData = [
                'assignment_id' => $validated['assignment_id'],
                'officer_id' => $validated['officer_id'],
                'bast_type' => 'petugas_ke_kantor',
                'receipt_category' => null,
                'bast_date' => $bastDate,
                'bast_number' => $validated['bast_number'] ?? null,
                'description' => trim($validated['description'] ?? "Penyerahan hasil pekerjaan {$defaultWorkName}"),
                'notes' => $validated['notes'] ?? null,
                'status' => $validated['status'],
                'document_title' => $validated['document_title'] ?? 'BERITA ACARA SERAH TERIMA HASIL PEKERJAAN PETUGAS PENDATAAN LAPANGAN',
                'activity_subtitle' => $validated['activity_subtitle'] ?? $assignment->title,
                'institution_name' => $validated['institution_name'] ?? 'BPS Kabupaten Kolaka Utara',
                'document_place' => $validated['document_place'] ?? 'Badan Pusat Statistik Kabupaten Kolaka Utara',
                'maker_name' => $validated['maker_name'] ?? 'Hidayatullah, SE',
                'maker_nip' => $validated['maker_nip'] ?? '198510292011011009',
                'first_party_position' => $validated['first_party_position'] ?? 'Pejabat Pembuat Komitmen Badan Pusat Statistik Kabupaten Kolaka Utara',
                'first_party_address' => $validated['first_party_address'] ?? 'Jalan Protokol Poros DPRD, Kec. Lasusua, Kolaka Utara Sulawesi Tenggara',
                'second_party_nik' => $validated['second_party_nik'] ?? ($officer->nik ?? ''),
                'second_party_address' => $validated['second_party_address'] ?? ($officer->address ?? 'Kecamatan Lasusua'),
                'contract_number' => $validated['contract_number'] ?? 'PPK 7408/06/10/2026/001',
                'contract_date' => $validated['contract_date'] ?? $bastDate,
                'doc_count' => $validated['doc_count'] ?? '30 Dokumen',
                'activity_work_name' => $defaultWorkName,
            ];

            return [$bastData, []];
        }

        $authUser = Auth::user();
        [$bastData, $cleanRecipients] = OfficerBast::prepareCollectivePayload(
            $request,
            $assignment,
            $bastDate,
            null,
            $authUser?->name ?? 'Koordinator Logistik BPS',
            ''
        );

        $bastData['bast_number'] = $validated['bast_number'] ?? null;
        $bastData['notes'] = $validated['notes'] ?? null;
        $bastData['status'] = $validated['status'];

        $makerSignaturePath = $existing?->maker_signature_path;
        $sigData = $request->input('maker_signature_data');
        if (!empty($sigData) && str_starts_with($sigData, 'data:image')) {
            $data = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $sigData));
            if (!empty($data)) {
                if (function_exists('imagecreatefromstring') && function_exists('imagecrop')) {
                    $im = @imagecreatefromstring($data);
                    if ($im) {
                        $w = imagesx($im);
                        $h = imagesy($im);
                        $minX = $w; $minY = $h; $maxX = 0; $maxY = 0;
                        $found = false;

                        for ($x = 0; $x < $w; $x++) {
                            for ($y = 0; $y < $h; $y++) {
                                $rgba = imagecolorat($im, $x, $y);
                                $alpha = ($rgba & 0x7F000000) >> 24;
                                if ($alpha < 120) {
                                    $found = true;
                                    if ($x < $minX) $minX = $x;
                                    if ($x > $maxX) $maxX = $x;
                                    if ($y < $minY) $minY = $y;
                                    if ($y > $maxY) $maxY = $y;
                                }
                            }
                        }

                        if ($found) {
                            $pad = 6;
                            $cropX = max(0, $minX - $pad);
                            $cropY = max(0, $minY - $pad);
                            $cropW = min($w - $cropX, ($maxX - $minX + 1) + ($pad * 2));
                            $cropH = min($h - $cropY, ($maxY - $minY + 1) + ($pad * 2));

                            $cropped = @imagecrop($im, ['x' => $cropX, 'y' => $cropY, 'width' => $cropW, 'height' => $cropH]);
                            if ($cropped) {
                                imagesavealpha($cropped, true);
                                ob_start();
                                imagepng($cropped);
                                $croppedData = ob_get_clean();
                                imagedestroy($cropped);
                                if (!empty($croppedData)) {
                                    $data = $croppedData;
                                }
                            }
                        }
                        imagedestroy($im);
                    }
                }

                $fileName = 'signatures/' . uniqid('sign_maker_') . '.png';
                Storage::disk('public')->put($fileName, $data);
                $makerSignaturePath = $fileName;
            }
        }

        $bastData['maker_signature_path'] = $makerSignaturePath;

        return [$bastData, $cleanRecipients];
    }

    private function renderPdf(OfficerBast $bast)
    {
        $bast->load(['officer', 'assignment', 'recipients']);

        return Pdf::loadView($bast->pdfViewName(), compact('bast'))
            ->setPaper('a4', 'portrait');
    }

    private function syncSheetRow(OfficerBast $bast, bool $isInsert): void
    {
        if (!$this->sheetService->isConfigured()) {
            return;
        }

        $payload = [
            'id' => $bast->id,
            'bast_number' => $bast->bast_number,
            'bast_type' => $bast->bast_type,
            'bast_date' => $bast->bast_date->toDateString(),
            'assignment_id' => $bast->assignment_id,
            'officer_id' => $bast->officer_id,
            'description' => $bast->description,
            'notes' => $bast->notes ?? '',
            'status' => $bast->status,
            'updated_at' => now()->toISOString(),
        ];

        if ($isInsert) {
            $payload['created_at'] = now()->toISOString();
            $this->sheetService->insert('officer_basts', $payload);
        } else {
            $this->sheetService->update('officer_basts', $bast->id, $payload);
        }
    }
}
