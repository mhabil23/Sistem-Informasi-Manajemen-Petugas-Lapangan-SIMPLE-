<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\TrainingEvent;
use App\Models\TrainingParticipant;
use App\Models\User;
use App\Services\GoogleSheetService;
use App\Services\SimpleExcelParser;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use ZipArchive;

class TrainingController extends Controller
{
    protected GoogleSheetService $sheetService;
    protected SimpleExcelParser $excelParser;

    public function __construct(GoogleSheetService $sheetService, SimpleExcelParser $excelParser)
    {
        $this->sheetService = $sheetService;
        $this->excelParser = $excelParser;
    }

    /**
     * Tampilan Utama Daftar Hadir (Daftar Dokumen Presensi Kegiatan & Pelatihan).
     */
    public function index(Request $request)
    {
        $query = TrainingEvent::with(['creator', 'assignment', 'participants']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('document_number', 'like', "%{$search}%")
                  ->orWhere('document_title', 'like', "%{$search}%")
                  ->orWhere('activity_subtitle', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('document_place', 'like', "%{$search}%")
                  ->orWhere('maker_name', 'like', "%{$search}%")
                  ->orWhereHas('participants', fn ($p) => $p->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('assignment', fn ($a) => $a->where('title', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        if ($assignmentId = $request->input('assignment_id')) {
            $query->where('assignment_id', $assignmentId);
        }

        $totalEvents = TrainingEvent::count();
        $thisMonthCount = TrainingEvent::whereMonth('start_date', now()->month)
                                      ->whereYear('start_date', now()->year)
                                      ->count();
        $totalParticipants = TrainingParticipant::count();

        $trainings = $query->latest('start_date')->latest('id')->paginate(10)->withQueryString();

        $assignments = Assignment::with(['officers' => fn ($q) => $q->orderBy('name')])
            ->orderBy('title')
            ->get();
        $officers = User::fieldStaff()->where('status', 'active')->orderBy('name')->get();

        return view('admin.trainings.index', compact(
            'trainings',
            'totalEvents',
            'thisMonthCount',
            'totalParticipants',
            'assignments',
            'officers'
        ));
    }

    /**
     * Simpan Dokumen Daftar Hadir Baru.
     */
    public function store(Request $request)
    {
        // 1. Cek apakah menggunakan format kolektif Daftar Hadir (konsep BAST Blanko)
        if ($request->has('participants') || $request->has('document_number') || $request->has('document_title')) {
            return $this->storeAttendanceDocument($request);
        }

        // 2. Format lama (kompatibilitas backward untuk test eksisting)
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'location' => ['required', 'string', 'max:255'],
            'quota' => ['required', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['upcoming', 'ongoing', 'completed', 'cancelled'])],
        ], [
            'title.required' => 'Nama kegiatan pelatihan / daftar hadir wajib diisi.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'location.required' => 'Lokasi pelaksanaan wajib diisi.',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['document_number'] = TrainingEvent::generateDocumentNumber();
        $validated['document_title'] = 'DAFTAR HADIR PESERTA';
        $validated['activity_subtitle'] = $validated['title'];
        $validated['institution_name'] = 'BPS Kabupaten Kolaka Utara Tahun ' . now()->year;
        $validated['document_place'] = $validated['location'];
        $validated['maker_name'] = Auth::user()?->name ?? 'Penanggung Jawab Kegiatan';

        $training = TrainingEvent::create($validated);

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->insert('training_events', [
                'id' => $training->id,
                'title' => $training->title,
                'description' => $training->description ?? '',
                'start_date' => $training->start_date->toDateString(),
                'end_date' => $training->end_date->toDateString(),
                'location' => $training->location,
                'quota' => $training->quota,
                'status' => $training->status,
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
            ]);
        }

        ActivityLog::log(
            'create',
            'trainings',
            "Membuat dokumen daftar hadir baru: {$training->title}."
        );

        return redirect()->route('admin.trainings.index')
            ->with('success', "Daftar Hadir '{$training->title}' berhasil dibuat!");
    }

    /**
     * Simpan dokumen Daftar Hadir dengan konsep kolektif (seperti Tanda Terima Petugas).
     */
    protected function storeAttendanceDocument(Request $request)
    {
        $validated = $request->validate([
            'document_number' => ['nullable', 'string', 'max:100'],
            'document_title' => ['nullable', 'string', 'max:255'],
            'activity_subtitle' => ['required', 'string', 'max:255'],
            'institution_name' => ['nullable', 'string', 'max:255'],
            'document_place' => ['nullable', 'string', 'max:255'],
            'attendance_date' => ['nullable', 'date'],
            'assignment_id' => ['nullable', 'exists:assignments,id'],
            'maker_name' => ['nullable', 'string', 'max:255'],
            'maker_nip' => ['nullable', 'string', 'max:100'],
            'maker_signature_data' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'participants' => ['nullable', 'array'],
            'participants.*.name' => ['required_with:participants', 'string', 'max:255'],
            'participants.*.category' => ['nullable', 'string', 'max:255'],
            'participants.*.agency' => ['nullable', 'string', 'max:255'],
        ], [
            'activity_subtitle.required' => 'Subjudul Kegiatan / Pelatihan wajib diisi.',
        ]);

        $docNumber = !empty($validated['document_number']) ? trim($validated['document_number']) : TrainingEvent::generateDocumentNumber();
        $date = !empty($validated['attendance_date']) ? $validated['attendance_date'] : now()->toDateString();
        $place = !empty($validated['document_place']) ? trim($validated['document_place']) : 'Kantor BPS Kabupaten Kolaka Utara';
        $title = !empty($validated['activity_subtitle']) ? trim($validated['activity_subtitle']) : 'Daftar Hadir Kegiatan Lapangan';

        // Tanda tangan digital pembuat daftar
        $makerSigPath = $this->saveSignatureData($request->input('maker_signature_data'));

        $rawParticipants = $request->input('participants', []);
        $cleanParticipants = [];
        $idx = 0;
        foreach ($rawParticipants as $p) {
            $name = trim($p['name'] ?? '');
            if ($name === '') {
                continue;
            }
            $cleanParticipants[] = [
                'name' => $name,
                'category' => trim($p['category'] ?? 'Petugas Lapangan'),
                'agency' => trim($p['agency'] ?? 'Kabupaten Kolaka Utara'),
                'nip_or_id' => trim($p['nip_or_id'] ?? ''),
                'attendance_status' => 'present',
                'sort_order' => $idx++,
            ];
        }

        $training = DB::transaction(function () use ($validated, $docNumber, $date, $place, $title, $makerSigPath, $cleanParticipants) {
            $created = TrainingEvent::create([
                'title' => $title,
                'document_number' => $docNumber,
                'document_title' => $validated['document_title'] ?: 'DAFTAR HADIR PESERTA',
                'activity_subtitle' => $validated['activity_subtitle'],
                'institution_name' => $validated['institution_name'] ?: 'BPS Kabupaten Kolaka Utara Tahun ' . now()->year,
                'start_date' => $date,
                'end_date' => $date,
                'location' => $place,
                'document_place' => $place,
                'maker_name' => $validated['maker_name'] ?: (Auth::user()?->name ?? 'Penanggung Jawab Kegiatan'),
                'maker_nip' => $validated['maker_nip'] ?? null,
                'maker_signature_path' => $makerSigPath,
                'notes' => $validated['notes'] ?? null,
                'quota' => max(count($cleanParticipants), 1),
                'status' => 'completed',
                'assignment_id' => $validated['assignment_id'] ?? null,
                'created_by' => Auth::id(),
            ]);

            foreach ($cleanParticipants as $cp) {
                $created->participants()->create($cp);
            }

            return $created;
        });

        ActivityLog::log(
            'create',
            'trainings',
            "Menerbitkan dokumen Daftar Hadir nomor {$training->document_number} ({$training->title}) dengan " . count($cleanParticipants) . " peserta."
        );

        return redirect()->route('admin.trainings.index')
            ->with('success', "Dokumen Daftar Hadir '{$training->document_number}' berhasil dibuat dan siap diunduh / dicetak!");
    }

    /**
     * Tampilan Detail Presensi / Peserta Dokumen.
     */
    public function show(TrainingEvent $training)
    {
        $training->load(['creator', 'assignment', 'participants']);

        $participants = $training->participants()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        $presentCount = $training->participants()->where('attendance_status', 'present')->count();
        $absentCount = $training->participants()->where('attendance_status', 'absent')->count();
        $excusedCount = $training->participants()->where('attendance_status', 'excused')->count();

        $availableCategories = $training->participants()->pluck('category')->filter()->unique()->values();

        $assignments = Assignment::with(['officers' => fn ($q) => $q->orderBy('name')])->orderBy('title')->get();
        $officers = User::fieldStaff()->where('status', 'active')->orderBy('name')->get();

        return view('admin.trainings.show', compact(
            'training',
            'participants',
            'presentCount',
            'absentCount',
            'excusedCount',
            'availableCategories',
            'assignments',
            'officers'
        ));
    }

    /**
     * Perbarui Dokumen Daftar Hadir Beserta Peserta.
     */
    public function update(Request $request, TrainingEvent $training)
    {
        $validated = $request->validate([
            'document_number' => ['nullable', 'string', 'max:100'],
            'document_title' => ['nullable', 'string', 'max:255'],
            'activity_subtitle' => ['required', 'string', 'max:255'],
            'institution_name' => ['nullable', 'string', 'max:255'],
            'document_place' => ['nullable', 'string', 'max:255'],
            'attendance_date' => ['nullable', 'date'],
            'assignment_id' => ['nullable', 'exists:assignments,id'],
            'maker_name' => ['nullable', 'string', 'max:255'],
            'maker_nip' => ['nullable', 'string', 'max:100'],
            'maker_signature_data' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'participants' => ['nullable', 'array'],
        ], [
            'activity_subtitle.required' => 'Subjudul Kegiatan / Pelatihan wajib diisi.',
        ]);

        $date = !empty($validated['attendance_date']) ? $validated['attendance_date'] : $training->start_date->toDateString();
        $place = !empty($validated['document_place']) ? trim($validated['document_place']) : ($training->location ?: 'Kantor BPS Kabupaten Kolaka Utara');
        $title = !empty($validated['activity_subtitle']) ? trim($validated['activity_subtitle']) : $training->title;

        // Cek tanda tangan baru
        $makerSigPath = $training->maker_signature_path;
        if ($newSig = $this->saveSignatureData($request->input('maker_signature_data'))) {
            $makerSigPath = $newSig;
        }

        $rawParticipants = $request->input('participants', []);
        $cleanParticipants = [];
        $idx = 0;
        foreach ($rawParticipants as $p) {
            $name = trim($p['name'] ?? '');
            if ($name === '') {
                continue;
            }
            $cleanParticipants[] = [
                'id' => $p['id'] ?? null,
                'name' => $name,
                'category' => trim($p['category'] ?? 'Petugas Lapangan'),
                'agency' => trim($p['agency'] ?? 'Kabupaten Kolaka Utara'),
                'nip_or_id' => trim($p['nip_or_id'] ?? ''),
                'attendance_status' => $p['attendance_status'] ?? 'present',
                'sort_order' => $idx++,
            ];
        }

        DB::transaction(function () use ($training, $validated, $date, $place, $title, $makerSigPath, $cleanParticipants) {
            $training->update([
                'title' => $title,
                'document_number' => $validated['document_number'] ?: $training->document_number,
                'document_title' => $validated['document_title'] ?: 'DAFTAR HADIR PESERTA',
                'activity_subtitle' => $validated['activity_subtitle'],
                'institution_name' => $validated['institution_name'] ?: $training->institution_name,
                'start_date' => $date,
                'end_date' => $date,
                'location' => $place,
                'document_place' => $place,
                'maker_name' => $validated['maker_name'] ?: $training->maker_name,
                'maker_nip' => $validated['maker_nip'] ?? $training->maker_nip,
                'maker_signature_path' => $makerSigPath,
                'notes' => $validated['notes'] ?? $training->notes,
                'assignment_id' => $validated['assignment_id'] ?? $training->assignment_id,
            ]);

            // Sync peserta dan pertahankan tanda tangan yang sudah ada
            $existingParticipants = $training->participants()->get()->keyBy('id');
            $keptIds = [];

            foreach ($cleanParticipants as $cp) {
                if (!empty($cp['id']) && $existingParticipants->has($cp['id'])) {
                    $existing = $existingParticipants->get($cp['id']);
                    $existing->update([
                        'name' => $cp['name'],
                        'category' => $cp['category'],
                        'agency' => $cp['agency'],
                        'nip_or_id' => $cp['nip_or_id'],
                        'sort_order' => $cp['sort_order'],
                    ]);
                    $keptIds[] = $existing->id;
                } else {
                    $newP = $training->participants()->create([
                        'name' => $cp['name'],
                        'category' => $cp['category'],
                        'agency' => $cp['agency'],
                        'nip_or_id' => $cp['nip_or_id'],
                        'attendance_status' => 'present',
                        'sort_order' => $cp['sort_order'],
                    ]);
                    $keptIds[] = $newP->id;
                }
            }

            // Hapus peserta yang dibuang dari form
            $training->participants()->whereNotIn('id', $keptIds)->delete();
        });

        ActivityLog::log(
            'update',
            'trainings',
            "Memperbarui dokumen Daftar Hadir: {$training->document_number} ({$training->title})."
        );

        return redirect()->route('admin.trainings.index')
            ->with('success', "Dokumen Daftar Hadir '{$training->document_number}' berhasil diperbarui!");
    }

    /**
     * Hapus Dokumen Daftar Hadir.
     */
    public function destroy(TrainingEvent $training)
    {
        $doc = $training->document_number ?: $training->title;
        DB::transaction(function () use ($training) {
            $training->participants()->delete();
            $training->delete();
        });

        ActivityLog::log(
            'delete',
            'trainings',
            "Menghapus dokumen daftar hadir: {$doc}."
        );

        return redirect()->route('admin.trainings.index')
            ->with('success', "Dokumen Daftar Hadir '{$doc}' berhasil dihapus.");
    }

    /**
     * Preview Dokumen Daftar Hadir PDF secara interaktif di browser.
     */
    public function previewAttendancePdf(TrainingEvent $training)
    {
        $training->load(['creator', 'assignment', 'participants']);

        $pdf = Pdf::loadView('pdf.training_attendance', compact('training'))
                  ->setPaper('a4', 'portrait');

        $filename = 'Preview-Daftar-Hadir-' . \Illuminate\Support\Str::slug($training->title) . '.pdf';

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Unduh File Dokumen Daftar Hadir PDF Resmi.
     */
    public function exportAttendancePdf(TrainingEvent $training)
    {
        $training->load(['creator', 'assignment', 'participants']);

        $pdf = Pdf::loadView('pdf.training_attendance', compact('training'))
                  ->setPaper('a4', 'portrait');

        $slug = \Illuminate\Support\Str::slug($training->document_number ?: $training->title);
        $filename = 'Daftar-Hadir-' . $slug . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Parse File Excel / CSV Peserta Daftar Hadir secara instan via AJAX.
     */
    public function parseExcelParticipants(Request $request)
    {
        $request->validate([
            'excel_file' => ['required', 'file', 'extensions:xlsx,xls,csv,txt', 'max:10240'],
        ], [
            'excel_file.required' => 'Pilih berkas Excel (.xlsx) atau CSV peserta.',
            'excel_file.extensions' => 'Format berkas harus berupa .xlsx, .xls, atau .csv.',
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

        $participants = [];
        foreach ($parsedRows as $row) {
            $name = $this->findColValue($row, ['namapeserta', 'namapetugas', 'namalengkap', 'nama', 'peserta', 'petugas']);
            if (empty($name)) {
                continue;
            }

            $category = $this->findColValue($row, ['jabatanunitkerja', 'jabatan', 'unitkerja', 'peran', 'posisi', 'penugasan', 'kategori'], 'Petugas Lapangan');
            $agency = $this->findColValue($row, ['asalinstansi', 'wilayahtugas', 'asal', 'instansi', 'wilayah', 'kecamatan', 'desa'], 'Kabupaten Kolaka Utara');
            $nip = $this->findColValue($row, ['nip', 'nik', 'idsobat', 'sobatid', 'nomorid'], '');

            $participants[] = [
                'name' => trim($name),
                'category' => trim($category),
                'agency' => trim($agency),
                'nip_or_id' => trim($nip),
            ];
        }

        if (empty($participants)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ditemukan baris nama peserta yang valid. Pastikan ada kolom Nama.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'count' => count($participants),
            'participants' => $participants,
            'message' => 'Berhasil membaca ' . count($participants) . ' peserta dari berkas Excel.',
        ]);
    }

    /**
     * Download Template Excel / CSV Peserta Daftar Hadir.
     */
    public function downloadTemplate(Request $request)
    {
        $format = $request->query('format', 'xlsx');
        $headersList = ['NO', 'Nama Peserta', 'Jabatan / Unit Kerja', 'Asal / Instansi'];
        $sampleRows = [
            ['1', 'Evayana', 'Petugas Pendataan Lapangan', 'Kecamatan Ranteangin'],
            ['2', 'Nurfadillah NB', 'Petugas Pendataan Lapangan', 'Kecamatan Ranteangin'],
            ['3', 'Nurul Hidayah', 'Petugas Pendataan Lapangan', 'Kecamatan Lambai'],
        ];

        if ($format === 'xlsx') {
            $tmpPath = tempnam(sys_get_temp_dir(), 'tpl_dh_') . '.xlsx';
            $this->createCleanParticipantsXlsxTemplate($tmpPath, $headersList, $sampleRows);

            return response()->stream(function () use ($tmpPath) {
                readfile($tmpPath);
                @unlink($tmpPath);
            }, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename=Template_Daftar_Hadir_Peserta_BPS.xlsx',
            ]);
        }

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename=Template_Daftar_Hadir_Peserta_BPS.csv',
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
     * Tambah Peserta Tunggal (Backward Compatibility).
     */
    public function storeParticipant(Request $request, TrainingEvent $training)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nip_or_id' => ['nullable', 'string', 'max:50'],
            'category' => ['required', 'string', 'max:100'],
            'agency' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:25'],
            'email' => ['nullable', 'email', 'max:255'],
            'attendance_status' => ['required', Rule::in(['present', 'absent', 'excused'])],
            'certificate_number' => ['nullable', 'string', 'max:100'],
        ]);

        $participant = $training->participants()->create($validated);

        return back()->with('success', "Peserta '{$participant->name}' berhasil ditambahkan ke daftar hadir!");
    }

    /**
     * Update Peserta Tunggal (Backward Compatibility).
     */
    public function updateParticipant(Request $request, TrainingEvent $training, TrainingParticipant $participant)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nip_or_id' => ['nullable', 'string', 'max:50'],
            'category' => ['required', 'string', 'max:100'],
            'agency' => ['nullable', 'string', 'max:255'],
            'attendance_status' => ['required', Rule::in(['present', 'absent', 'excused'])],
        ]);

        $participant->update($validated);

        return back()->with('success', "Data peserta '{$participant->name}' berhasil diperbarui.");
    }

    /**
     * Hapus Peserta Tunggal (Backward Compatibility).
     */
    public function destroyParticipant(TrainingEvent $training, TrainingParticipant $participant)
    {
        $name = $participant->name;
        $participant->delete();

        return back()->with('success', "Peserta '{$name}' telah dihapus dari kegiatan.");
    }

    /**
     * Simpan file base64 tanda tangan digital canvas dengan pemotongan otomatis (auto-trim).
     */
    protected function saveSignatureData(?string $sigData): ?string
    {
        if (empty($sigData) || !str_starts_with($sigData, 'data:image')) {
            return null;
        }

        $data = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $sigData));
        if (empty($data)) {
            return null;
        }

        if (function_exists('imagecreatefromstring') && function_exists('imagecrop')) {
            $im = @imagecreatefromstring($data);
            if ($im) {
                $w = imagesx($im);
                $h = imagesy($im);
                $minX = $w; $minY = $h; $maxX = 0; $maxY = 0;
                $found = false;

                for ($y = 0; $y < $h; $y++) {
                    for ($x = 0; $x < $w; $x++) {
                        $rgba = imagecolorat($im, $x, $y);
                        $alpha = ($rgba >> 24) & 0x7F;
                        if ($alpha < 120) {
                            $minX = min($minX, $x);
                            $minY = min($minY, $y);
                            $maxX = max($maxX, $x);
                            $maxY = max($maxY, $y);
                            $found = true;
                        }
                    }
                }

                if ($found && $maxX > $minX && $maxY > $minY) {
                    $pad = 6;
                    $cropX = max(0, $minX - $pad);
                    $cropY = max(0, $minY - $pad);
                    $cropW = min($w - $cropX, ($maxX - $minX + 1) + ($pad * 2));
                    $cropH = min($h - $cropY, ($maxY - $minY + 1) + ($pad * 2));

                    $cropped = imagecrop($im, ['x' => $cropX, 'y' => $cropY, 'width' => $cropW, 'height' => $cropH]);
                    if ($cropped) {
                        imagealphablending($cropped, false);
                        imagesavealpha($cropped, true);
                        ob_start();
                        imagepng($cropped);
                        $data = ob_get_clean();
                        imagedestroy($cropped);
                    }
                }
                imagedestroy($im);
            }
        }

        $filename = 'signatures/maker_dh_' . uniqid() . '_' . time() . '.png';
        Storage::disk('public')->put($filename, $data);

        return $filename;
    }

    /**
     * Helper pencarian kolom dinamis dari row Excel.
     */
    protected function findColValue(array $row, array $possibleKeywords, $default = null)
    {
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
     * Helper pembuatan file Excel template peserta.
     */
    protected function createCleanParticipantsXlsxTemplate(string $outputPath, array $headers, array $rows): void
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
    <sheet name="Daftar Hadir Peserta" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>');

        $flatStrings = [];
        foreach ($headers as $h) {
            $flatStrings[] = (string) $h;
        }
        foreach ($rows as $r) {
            foreach ($r as $c) {
                $flatStrings[] = (string) $c;
            }
        }

        $ssXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="'.count($flatStrings).'" uniqueCount="'.count($flatStrings).'">';
        foreach ($flatStrings as $s) {
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
    <col min="4" max="4" width="28" customWidth="1"/>
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
}
