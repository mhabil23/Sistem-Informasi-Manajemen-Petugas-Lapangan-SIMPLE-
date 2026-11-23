<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\Schedule;
use App\Models\User;
use App\Services\GoogleSheetService;
use App\Services\SimpleExcelParser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use ZipArchive;

class ScheduleController extends Controller
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
        $query = Schedule::with(['assignment', 'officer']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($assignmentId = $request->input('assignment_id')) {
            $query->where('assignment_id', $assignmentId);
        }

        if ($officerId = $request->input('officer_id')) {
            $query->where('officer_id', $officerId);
        }

        $totalSchedules = Schedule::count();
        $totalUpcoming = Schedule::where('status', 'scheduled')->where('start_time', '>=', now())->count();
        $totalInProgress = Schedule::where('status', 'in_progress')->count();
        $totalCompleted = Schedule::where('status', 'completed')->count();

        $schedules = $query->orderBy('start_time', 'asc')->paginate(10)->withQueryString();

        $assignments = Assignment::orderBy('title')->get();
        $officers = User::fieldStaff()->where('status', 'active')->orderBy('name')->get();
        $assignmentOfficersMap = Assignment::getInvolvedOfficersMap();

        return view('admin.schedules.index', compact(
            'schedules',
            'totalSchedules',
            'totalUpcoming',
            'totalInProgress',
            'totalCompleted',
            'assignments',
            'officers',
            'assignmentOfficersMap'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'assignment_id' => ['nullable', 'exists:assignments,id'],
            'officer_id' => ['nullable', 'exists:users,id'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'location' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::in(['scheduled', 'in_progress', 'completed', 'cancelled'])],
        ], [
            'title.required' => 'Judul jadwal kegiatan wajib diisi.',
            'start_time.required' => 'Waktu mulai kegiatan wajib diisi.',
            'end_time.required' => 'Waktu selesai kegiatan wajib diisi.',
            'end_time.after' => 'Waktu selesai harus setelah waktu mulai.',
            'location.required' => 'Lokasi kegiatan wajib diisi.',
        ]);

        $schedule = Schedule::create($validated);

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->insert('schedules', [
                'id' => $schedule->id,
                'title' => $schedule->title,
                'assignment_id' => $schedule->assignment_id ?? '',
                'officer_id' => $schedule->officer_id ?? '',
                'start_time' => $schedule->start_time->toISOString(),
                'end_time' => $schedule->end_time->toISOString(),
                'location' => $schedule->location,
                'notes' => $schedule->notes ?? '',
                'status' => $schedule->status,
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
            ]);
        }

        ActivityLog::log(
            'create',
            'schedules',
            "Menambahkan jadwal kegiatan: {$schedule->title} di {$schedule->location}."
        );

        return redirect()->route('admin.schedules.index')->with('success', "Jadwal '{$schedule->title}' berhasil dibuat!");
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'assignment_id' => ['nullable', 'exists:assignments,id'],
            'officer_id' => ['nullable', 'exists:users,id'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'location' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::in(['scheduled', 'in_progress', 'completed', 'cancelled'])],
        ]);

        $schedule->update($validated);

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->update('schedules', $schedule->id, [
                'title' => $schedule->title,
                'assignment_id' => $schedule->assignment_id ?? '',
                'officer_id' => $schedule->officer_id ?? '',
                'start_time' => $schedule->start_time->toISOString(),
                'end_time' => $schedule->end_time->toISOString(),
                'location' => $schedule->location,
                'notes' => $schedule->notes ?? '',
                'status' => $schedule->status,
                'updated_at' => now()->toISOString(),
            ]);
        }

        ActivityLog::log(
            'update',
            'schedules',
            "Memperbarui jadwal kegiatan: {$schedule->title}."
        );

        return back()->with('success', "Jadwal '{$schedule->title}' berhasil diperbarui!");
    }

    public function destroy(Schedule $schedule)
    {
        $title = $schedule->title;
        $id = $schedule->id;
        $schedule->delete();

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->delete('schedules', $id, true);
        }

        ActivityLog::log(
            'delete',
            'schedules',
            "Menghapus jadwal kegiatan: {$title}."
        );

        return back()->with('success', "Jadwal '{$title}' berhasil dihapus.");
    }

    /**
     * Download format template Excel / CSV untuk import massal jadwal kegiatan.
     */
    public function downloadTemplate(Request $request)
    {
        $headersList = [
            'NO',
            'Nama Agenda',
            'Kegiatan Terkait',
            'Petugas Terkait',
            'Waktu Mulai',
            'Waktu Selesai',
            'Lokasi / Tempat',
            'Status',
            'Catatan / Perlengkapan',
        ];

        $sampleRows = [
            [
                1,
                'Briefing Tim & Pembagian Peta Kerja Lapangan',
                'Survey Pola Konsumsi Rumah Tangga (SPKI)',
                'Dini Amirul, S.ST, MIDS, M.Ec.Dev.',
                '2026-11-21 08:30',
                '2026-11-21 11:30',
                'Ruang Rapat Kantor BPS Kolaka Utara',
                'Terjadwal',
                'Membawa laptop, map dokumen, dan surat tugas.',
            ],
            [
                2,
                'Pencacahan Lapangan Responden Blok Sensus 001B',
                'Survey Pola Konsumsi Rumah Tangga (SPKI)',
                'Evayana',
                '2026-11-22 09:00',
                '2026-11-22 15:00',
                'Kecamatan Lasusua',
                'Terjadwal',
                'Pencacahan sampel responden 1 s.d 10.',
            ],
            [
                3,
                'Evaluasi Progres Mingguan & Validasi BAST',
                'Survey Pola Konsumsi Rumah Tangga (SPKI)',
                '-- Seluruh Petugas (Umum) --',
                '2026-11-25 14:00',
                '2026-11-25 16:30',
                'Aula Lantai 2 BPS Kolaka Utara',
                'Terjadwal',
                'Membawa seluruh berkas rekapitulasi lapangan.',
            ],
        ];

        if ($request->query('format') === 'xlsx') {
            $tmpPath = tempnam(sys_get_temp_dir(), 'tpl_schedule_') . '.xlsx';
            $this->createCleanXlsxTemplate($tmpPath, $headersList, $sampleRows);

            return response()->stream(function () use ($tmpPath) {
                readfile($tmpPath);
                @unlink($tmpPath);
            }, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename=Template_Jadwal_Kegiatan_SIMPEL.xlsx',
            ]);
        }

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename=Template_Jadwal_Kegiatan_SIMPEL.csv',
        ];

        $callback = function () use ($headersList, $sampleRows) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $headersList);
            foreach ($sampleRows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Preview dan ekstrak data jadwal kegiatan dari Excel / CSV secara instan via AJAX.
     */
    public function previewExcel(Request $request)
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
            $parsedRows = $this->excelParser->parseGeneric($file->getRealPath(), $ext);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membaca berkas Excel: ' . $e->getMessage(),
            ], 422);
        }

        if (empty($parsedRows)) {
            return response()->json([
                'success' => false,
                'message' => 'Berkas Excel kosong atau tidak ada baris data yang terbaca.',
            ], 422);
        }

        $normalized = $this->normalizeScheduleRows($parsedRows);

        if (empty($normalized)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ditemukan baris agenda kegiatan yang valid pada berkas Excel. Pastikan kolom Nama Agenda terisi.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'count' => count($normalized),
            'schedules' => $normalized,
            'message' => count($normalized) . ' agenda kegiatan berhasil dibaca dan siap di-generate!',
        ]);
    }

    /**
     * Import dan generate jadwal kegiatan secara massal ke database dari file Excel / CSV.
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => ['required', 'file', 'extensions:xlsx,xls,csv,txt', 'max:10240'],
            'import_mode' => ['nullable', 'in:append,replace'],
        ], [
            'excel_file.required' => 'Pilih berkas Excel (.xlsx) atau CSV yang akan diunggah.',
            'excel_file.extensions' => 'Format berkas harus berupa .xlsx, .xls, atau .csv.',
            'excel_file.max' => 'Ukuran berkas maksimal adalah 10 MB.',
        ]);

        $file = $request->file('excel_file');
        $ext = $file->getClientOriginalExtension();
        $importMode = $request->input('import_mode', 'append');

        try {
            $parsedRows = $this->excelParser->parseGeneric($file->getRealPath(), $ext);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memproses berkas Excel: ' . $e->getMessage());
        }

        if (empty($parsedRows)) {
            return back()->with('error', 'Berkas Excel kosong atau tidak ada baris data yang terbaca.');
        }

        $normalized = $this->normalizeScheduleRows($parsedRows);

        if (empty($normalized)) {
            return back()->with('error', 'Tidak ditemukan baris jadwal yang valid. Pastikan kolom Nama Agenda terisi.');
        }

        $createdCount = 0;

        DB::transaction(function () use ($normalized, $importMode, &$createdCount) {
            if ($importMode === 'replace') {
                Schedule::query()->delete();
            }

            foreach ($normalized as $item) {
                $schedule = Schedule::create([
                    'title' => $item['title'],
                    'assignment_id' => $item['assignment_id'],
                    'officer_id' => $item['officer_id'],
                    'start_time' => $item['start_time'],
                    'end_time' => $item['end_time'],
                    'location' => $item['location'],
                    'notes' => $item['notes'],
                    'status' => $item['status'],
                ]);

                if ($this->sheetService->isConfigured()) {
                    $this->sheetService->insert('schedules', [
                        'id' => $schedule->id,
                        'title' => $schedule->title,
                        'assignment_id' => $schedule->assignment_id ?? '',
                        'officer_id' => $schedule->officer_id ?? '',
                        'start_time' => $schedule->start_time->toISOString(),
                        'end_time' => $schedule->end_time->toISOString(),
                        'location' => $schedule->location,
                        'notes' => $schedule->notes ?? '',
                        'status' => $schedule->status,
                        'created_at' => now()->toISOString(),
                        'updated_at' => now()->toISOString(),
                    ]);
                }

                $createdCount++;
            }

            ActivityLog::log(
                'import',
                'schedules',
                "Mengimpor & meng-generate {$createdCount} jadwal kegiatan sekaligus dari berkas Excel (Mode: {$importMode})."
            );
        });

        $actionText = ($importMode === 'replace') ? 'menggantikan jadwal lama dan meng-generate' : 'meng-generate';

        return redirect()->route('admin.schedules.index')
            ->with('success', "Berhasil {$actionText} {$createdCount} agenda kegiatan dari berkas Excel!");
    }

    /**
     * Normalisasi baris mentah dari file Excel menjadi struktur jadwal kegiatan yang valid.
     */
    protected function normalizeScheduleRows(array $rawRows): array
    {
        $assignments = Assignment::all();
        $officers = User::all();
        $normalized = [];

        foreach ($rawRows as $idx => $row) {
            $title = $this->findColValue($row, ['namaagenda', 'judulagenda', 'agenda', 'kegiatanagenda', 'namakegiatan', 'judul']);
            if (empty($title)) {
                continue;
            }

            // Matching Kegiatan
            $assignmentId = null;
            $assignmentTitle = '-';
            $activityVal = $this->findColValue($row, ['kegiatanterkait', 'kegiatan', 'survei', 'assignment', 'namasurvei', 'proyek']);
            if (!empty($activityVal)) {
                $activityClean = trim($activityVal);
                if (is_numeric($activityClean)) {
                    $foundAssign = $assignments->firstWhere('id', (int) $activityClean);
                } else {
                    $foundAssign = $assignments->first(fn ($a) => stripos($a->title, $activityClean) !== false || stripos($activityClean, $a->title) !== false);
                }
                if ($foundAssign) {
                    $assignmentId = $foundAssign->id;
                    $assignmentTitle = $foundAssign->title;
                } else {
                    $assignmentTitle = $activityClean;
                }
            }

            // Matching Petugas
            $officerId = null;
            $officerName = '-- Seluruh Petugas (Umum) --';
            $officerVal = $this->findColValue($row, ['petugasterkait', 'petugas', 'namapetugas', 'pegawai', 'nip', 'username', 'pelaksana']);
            if (!empty($officerVal)) {
                $officerClean = trim($officerVal);
                $isAll = preg_match('/seluruh|semua|umum|\-\-/i', $officerClean);
                if (!$isAll) {
                    if (is_numeric($officerClean) && strlen($officerClean) <= 6) {
                        $foundOff = $officers->firstWhere('id', (int) $officerClean);
                    } else {
                        $foundOff = $officers->first(function ($u) use ($officerClean) {
                            return (!empty($u->nik) && $u->nik === $officerClean)
                                || (!empty($u->username) && strcasecmp($u->username, $officerClean) === 0)
                                || stripos($u->name, $officerClean) !== false
                                || stripos($officerClean, $u->name) !== false;
                        });
                    }

                    if ($foundOff) {
                        $officerId = $foundOff->id;
                        $officerName = $foundOff->name;
                    } else {
                        $officerName = $officerClean;
                    }
                }
            }

            // Waktu Mulai & Selesai
            $startVal = $this->findColValue($row, ['waktumulai', 'mulai', 'tanggalmulai', 'waktu', 'tanggal', 'starttime', 'start']);
            $startTime = $this->parseExcelDateTime($startVal);
            if (!$startTime) {
                $startTime = now()->addDays($idx + 1)->setTime(8, 30);
            }

            $endVal = $this->findColValue($row, ['waktuselesai', 'selesai', 'tanggalselesai', 'endtime', 'end']);
            $endTime = $this->parseExcelDateTime($endVal);
            if (!$endTime || $endTime->lessThanOrEqualTo($startTime)) {
                $endTime = $startTime->copy()->addHours(2);
            }

            // Lokasi
            $location = $this->findColValue($row, ['lokasi', 'tempat', 'ruang', 'ruangpertemuan', 'location']);
            if (empty($location)) {
                $location = 'Kantor BPS Kabupaten Kolaka Utara';
            }

            // Status
            $statusVal = strtolower(trim((string) $this->findColValue($row, ['status', 'statusjadwal', 'state'], 'scheduled')));
            $status = match (true) {
                str_contains($statusVal, 'selesai') || str_contains($statusVal, 'completed') || str_contains($statusVal, 'done') => 'completed',
                str_contains($statusVal, 'berlangsung') || str_contains($statusVal, 'progress') || str_contains($statusVal, 'jalan') => 'in_progress',
                str_contains($statusVal, 'batal') || str_contains($statusVal, 'cancel') => 'cancelled',
                default => 'scheduled',
            };

            $statusLabel = match ($status) {
                'completed' => 'Selesai',
                'in_progress' => 'Sedang Berlangsung',
                'cancelled' => 'Dibatalkan',
                default => 'Terjadwal',
            };

            // Catatan
            $notes = $this->findColValue($row, ['catatan', 'perlengkapan', 'instruksi', 'keterangan', 'notes']);

            $normalized[] = [
                'no' => count($normalized) + 1,
                'title' => $title,
                'assignment_id' => $assignmentId,
                'assignment_title' => $assignmentTitle,
                'officer_id' => $officerId,
                'officer_name' => $officerName,
                'start_time' => $startTime->format('Y-m-d H:i:s'),
                'start_time_formatted' => $startTime->translatedFormat('d M Y, H:i'),
                'end_time' => $endTime->format('Y-m-d H:i:s'),
                'end_time_formatted' => $endTime->translatedFormat('d M Y, H:i'),
                'time_range_formatted' => $startTime->format('d/m/Y H:i') . ' - ' . $endTime->format('H:i'),
                'location' => $location,
                'status' => $status,
                'status_label' => $statusLabel,
                'notes' => $notes ?: '',
            ];
        }

        return $normalized;
    }

    /**
     * Mencari nilai kolom dalam row Excel berdasarkan kata kunci yang fleksibel.
     */
    protected function findColValue(array $row, array $possibleKeywords, $default = null)
    {
        // 1. Prioritaskan pencarian kecocokan persis (exact match)
        foreach ($possibleKeywords as $keyword) {
            $cleanKeyword = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', $keyword)));
            foreach ($row as $k => $v) {
                $normalizedKey = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', (string) $k)));
                if ($normalizedKey === $cleanKeyword) {
                    $val = trim((string) $v);
                    if ($val !== '') {
                        return $val;
                    }
                }
            }
        }

        // 2. Pencarian substring
        foreach ($possibleKeywords as $keyword) {
            $cleanKeyword = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', $keyword)));
            foreach ($row as $k => $v) {
                $normalizedKey = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', (string) $k)));
                if (str_contains($normalizedKey, $cleanKeyword)) {
                    $val = trim((string) $v);
                    if ($val !== '') {
                        return $val;
                    }
                }
            }
        }

        return $default;
    }

    /**
     * Parsing tanggal dan waktu Excel dari berbagai format (string atau serial number).
     */
    protected function parseExcelDateTime(?string $val): ?Carbon
    {
        if (empty($val)) {
            return null;
        }

        $val = trim($val);

        // Jika berupa nomor serial date bawaan Excel (misal: 45617.35416)
        if (is_numeric($val) && (float) $val > 1000) {
            $days = (int) $val;
            $fraction = (float) $val - $days;
            $seconds = (int) round($fraction * 86400);
            $unixTimestamp = ($days - 25569) * 86400 + $seconds;

            return Carbon::createFromTimestamp($unixTimestamp);
        }

        try {
            // Format Indonesia: DD/MM/YYYY HH:mm atau DD-MM-YYYY HH:mm
            if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})(?:\s+(\d{1,2}):(\d{1,2}))?$/', $val, $m)) {
                $day = str_pad($m[1], 2, '0', STR_PAD_LEFT);
                $month = str_pad($m[2], 2, '0', STR_PAD_LEFT);
                $year = $m[3];
                $hour = isset($m[4]) ? str_pad($m[4], 2, '0', STR_PAD_LEFT) : '08';
                $minute = isset($m[5]) ? str_pad($m[5], 2, '0', STR_PAD_LEFT) : '30';

                return Carbon::createFromFormat('Y-m-d H:i:s', "{$year}-{$month}-{$day} {$hour}:{$minute}:00");
            }

            return Carbon::parse($val);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Generate file Excel (.xlsx) murni menggunakan ZipArchive tanpa dependensi eksternal.
     */
    protected function createCleanXlsxTemplate(string $outputPath, array $headers, array $rows): void
    {
        $zip = new ZipArchive();
        if ($zip->open($outputPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
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
    <sheet name="Jadwal Kegiatan" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>');

        $sharedStrings = [];
        foreach ($headers as $h) {
            $sharedStrings[] = (string) $h;
        }
        foreach ($rows as $r) {
            foreach ($r as $cell) {
                $sharedStrings[] = (string) $cell;
            }
        }

        $ssXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="'.count($sharedStrings).'" uniqueCount="'.count($sharedStrings).'">';
        foreach ($sharedStrings as $str) {
            $ssXml .= '<si><t>' . htmlspecialchars($str, ENT_XML1) . '</t></si>';
        }
        $ssXml .= '</sst>';
        $zip->addFromString('xl/sharedStrings.xml', $ssXml);

        $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <cols>
    <col min="1" max="1" width="8" customWidth="1"/>
    <col min="2" max="2" width="38" customWidth="1"/>
    <col min="3" max="3" width="35" customWidth="1"/>
    <col min="4" max="4" width="28" customWidth="1"/>
    <col min="5" max="5" width="20" customWidth="1"/>
    <col min="6" max="6" width="20" customWidth="1"/>
    <col min="7" max="7" width="30" customWidth="1"/>
    <col min="8" max="8" width="16" customWidth="1"/>
    <col min="9" max="9" width="38" customWidth="1"/>
  </cols>
  <sheetData>';

        $strIndex = 0;
        // Row 1 (Header)
        $sheetXml .= '<row r="1">';
        for ($col = 0; $col < count($headers); $col++) {
            $colLetter = chr(65 + $col);
            $sheetXml .= '<c r="'.$colLetter.'1" t="s"><v>'.$strIndex.'</v></c>';
            $strIndex++;
        }
        $sheetXml .= '</row>';

        // Data Rows
        foreach ($rows as $rowIdx => $rowCells) {
            $rowNum = $rowIdx + 2;
            $sheetXml .= '<row r="'.$rowNum.'">';
            for ($col = 0; $col < count($rowCells); $col++) {
                $colLetter = chr(65 + $col);
                $sheetXml .= '<c r="'.$colLetter.$rowNum.'" t="s"><v>'.$strIndex.'</v></c>';
                $strIndex++;
            }
            $sheetXml .= '</row>';
        }

        $sheetXml .= '  </sheetData>
</worksheet>';
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);

        $zip->close();
    }
}

