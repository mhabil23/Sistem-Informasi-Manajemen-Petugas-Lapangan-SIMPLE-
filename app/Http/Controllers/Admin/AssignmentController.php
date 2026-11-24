<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\AssignmentOfficer;
use App\Models\OfficerReport;
use App\Models\Respondent;
use App\Models\User;
use App\Services\GoogleSheetService;
use App\Services\SimpleExcelParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AssignmentController extends Controller
{
    protected GoogleSheetService $sheetService;
    protected SimpleExcelParser $excelParser;

    public function __construct(GoogleSheetService $sheetService, SimpleExcelParser $excelParser)
    {
        $this->sheetService = $sheetService;
        $this->excelParser = $excelParser;
    }

    /**
     * Display a listing of assignments.
     */
    public function index(Request $request)
    {
        $query = Assignment::withCount(['officers', 'respondents'])
            ->with('creator');

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter visibility status (Aktif Manual (Admin), Aktif Periode, Nonaktif, Berakhir)
        if ($visStatus = $request->input('visibility_status')) {
            $todayStr = now()->toDateString();
            if ($visStatus === 'manual_active') {
                $query->where('status', '!=', 'cancelled')
                      ->where('is_enabled_by_admin', true);
            } elseif ($visStatus === 'period_active') {
                $query->where('status', '!=', 'cancelled')
                      ->where('is_enabled_by_admin', false)
                      ->whereDate('start_date', '<=', $todayStr)
                      ->whereDate('end_date', '>=', $todayStr);
            } elseif ($visStatus === 'ended') {
                $query->where('status', '!=', 'cancelled')
                      ->where('is_enabled_by_admin', false)
                      ->whereDate('end_date', '<', $todayStr);
            } elseif ($visStatus === 'inactive') {
                $query->where(function ($iq) use ($todayStr) {
                    $iq->where('status', 'cancelled')
                       ->orWhere(function ($sub) use ($todayStr) {
                           $sub->where('is_enabled_by_admin', false)
                               ->whereDate('start_date', '>', $todayStr);
                       });
                });
            }
        }

        // Stats
        $totalAssignments = Assignment::count();
        $totalOngoing = Assignment::where('status', 'ongoing')->count();
        $totalCompleted = Assignment::where('status', 'completed')->count();
        $totalDraft = Assignment::where('status', 'draft')->count();
        $globalAssignmentEnabled = Assignment::isGlobalPortalEnabled();

        $allForVis = Assignment::all();
        $totalManualActive = $allForVis->filter(fn($a) => $a->visibilityStatus() === 'manual_active')->count();
        $totalPeriodActive = $allForVis->filter(fn($a) => $a->visibilityStatus() === 'period_active')->count();
        $totalVisibleToOfficers = $allForVis->filter(fn($a) => $a->shouldBeShownToOfficer())->count();

        $assignments = $query->latest()->paginate(10)->withQueryString();

        // Get list of officers for quick assignment modals
        $officers = User::fieldStaff()->where('status', 'active')->orderBy('name')->get();
        $basisDefaults = OfficerReport::getMasterBasisSettings();

        return view('admin.assignments.index', compact(
            'assignments',
            'totalAssignments',
            'totalOngoing',
            'totalCompleted',
            'totalDraft',
            'globalAssignmentEnabled',
            'totalManualActive',
            'totalPeriodActive',
            'totalVisibleToOfficers',
            'officers',
            'basisDefaults'
        ));
    }

    /**
     * Store a newly created assignment.
     */
    public function store(Request $request)
    {
        $validated = $this->validateAssignmentRequest($request, true);
        $basis = $this->buildAssignmentBasisPayload($validated);

        $assignment = Assignment::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'location' => $validated['location'],
            'basis_dipa' => $basis['basis_dipa'],
            'st_number' => $basis['st_number'],
            'st_date' => $basis['st_date'],
            'basis_st' => $basis['basis_st'],
            'target_respondents' => $validated['target_respondents'] ?? 0,
            'status' => $validated['status'],
            'is_enabled_by_admin' => $request->boolean('is_enabled_by_admin', false),
            'created_by' => Auth::id(),
        ]);

        // Attach initial officers if selected
        if (!empty($validated['officer_ids'])) {
            foreach ($validated['officer_ids'] as $officerId) {
                AssignmentOfficer::create([
                    'assignment_id' => $assignment->id,
                    'user_id' => $officerId,
                    'specific_task' => 'Pelaksana pendataan dan verifikasi lapangan.',
                    'status' => 'pending',
                ]);
            }
        }

        // If excel file was uploaded directly in the form, process & allocate respondents & officers
        $extraMsg = '';
        if ($request->hasFile('excel_file')) {
            $file = $request->file('excel_file');
            $ext = $file->getClientOriginalExtension();
            try {
                $parsedRows = $this->excelParser->parse($file->getRealPath(), $ext);
                $counts = $this->processParsedRowsForAssignment($assignment, $parsedRows);
                if ($counts['respondents'] > 0) {
                    $extraMsg = " Sebanyak {$counts['respondents']} sampel responden dan {$counts['officers']} petugas berhasil dialokasikan dari berkas Excel.";
                }
            } catch (\Throwable $e) {
                // Log and continue
            }
        }

        // Sync to Google Sheet if configured
        if ($this->sheetService->isConfigured()) {
            $this->sheetService->insert('assignments', [
                'id' => $assignment->id,
                'title' => $assignment->title,
                'description' => $assignment->description ?? '',
                'start_date' => $assignment->start_date->toDateString(),
                'end_date' => $assignment->end_date->toDateString(),
                'location' => $assignment->location,
                'status' => $assignment->status,
                'created_by' => $assignment->created_by,
                'created_at' => $assignment->created_at->toISOString(),
                'updated_at' => $assignment->updated_at->toISOString(),
            ]);
        }

        ActivityLog::log(
            'create',
            'assignments',
            "Membuat kegiatan baru: {$assignment->title} di {$assignment->location}."
        );

        return redirect()->route('admin.assignments.show', $assignment)
            ->with('success', "Kegiatan '{$assignment->title}' berhasil dibuat!{$extraMsg} Silakan tentukan pembagian tugas masing-masing petugas.");
    }

    /**
     * Display the specified assignment details with officer assignments.
     */
    public function show(Assignment $assignment)
    {
        $assignment->load([
            'creator',
            'assignmentOfficers.user',
            'respondents.officer',
            'respondentBasts.respondent',
            'officerBasts.officer',
        ]);

        // Available active officers not yet assigned to this activity
        $assignedUserIds = $assignment->assignmentOfficers->pluck('user_id')->toArray();
        $availableOfficers = User::fieldStaff()
            ->where('status', 'active')
            ->whereNotIn('id', $assignedUserIds)
            ->orderBy('name')
            ->get();

        return view('admin.assignments.show', compact('assignment', 'availableOfficers'));
    }

    /**
     * Update the specified assignment.
     */
    public function update(Request $request, Assignment $assignment)
    {
        $validated = $this->validateAssignmentRequest($request, false);
        $basis = $this->buildAssignmentBasisPayload($validated);

        $assignment->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'location' => $validated['location'],
            'basis_dipa' => $basis['basis_dipa'],
            'st_number' => $basis['st_number'],
            'st_date' => $basis['st_date'],
            'basis_st' => $basis['basis_st'],
            'target_respondents' => $validated['target_respondents'] ?? 0,
            'status' => $validated['status'],
            'is_enabled_by_admin' => $request->has('is_enabled_by_admin')
                ? $request->boolean('is_enabled_by_admin')
                : $assignment->is_enabled_by_admin,
        ]);

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->update('assignments', $assignment->id, [
                'title' => $assignment->title,
                'description' => $assignment->description ?? '',
                'start_date' => $assignment->start_date->toDateString(),
                'end_date' => $assignment->end_date->toDateString(),
                'location' => $assignment->location,
                'status' => $assignment->status,
                'updated_at' => now()->toISOString(),
            ]);
        }

        ActivityLog::log(
            'update',
            'assignments',
            "Memperbarui data kegiatan: {$assignment->title}."
        );

        return back()->with('success', "Data kegiatan '{$assignment->title}' berhasil diperbarui!");
    }

    /**
     * Remove the specified assignment from storage.
     */
    public function destroy(Assignment $assignment)
    {
        $title = $assignment->title;
        $id = $assignment->id;
        $assignment->delete();

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->delete('assignments', $id, true);
        }

        ActivityLog::log(
            'delete',
            'assignments',
            "Menghapus kegiatan: {$title}."
        );

        return redirect()->route('admin.assignments.index')->with('success', "Kegiatan '{$title}' berhasil dihapus.");
    }

    /**
     * Aksi massal (pilih lebih dari satu kegiatan via checkbox tabel).
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => ['required', Rule::in(['set_ongoing', 'set_completed', 'set_draft', 'set_cancelled', 'enable_admin', 'disable_admin', 'delete', 'assign_officers'])],
            'assignment_ids' => ['required', 'array', 'min:1'],
            'assignment_ids.*' => ['integer', 'exists:assignments,id'],
            'officer_id' => ['nullable', 'exists:users,id'],
            'officer_ids' => ['nullable', 'array'],
            'officer_ids.*' => ['integer', 'exists:users,id'],
            'specific_task' => ['nullable', 'string', 'max:500'],
        ], [
            'assignment_ids.required' => 'Pilih minimal satu kegiatan untuk melakukan aksi massal.',
            'assignment_ids.min' => 'Pilih minimal satu kegiatan untuk melakukan aksi massal.',
        ]);

        if ($validated['action'] === 'assign_officers' && empty($validated['officer_id']) && empty($validated['officer_ids'])) {
            return back()->with('error', 'Pilih minimal satu petugas atau pegawai yang akan ditugaskan.');
        }

        $action = $validated['action'];
        $assignmentIds = array_unique($validated['assignment_ids']);
        $assignments = Assignment::whereIn('id', $assignmentIds)->get();
        $affectedCount = 0;
        $msg = '';

        switch ($action) {
            case 'set_ongoing':
                foreach ($assignments as $assignment) {
                    $assignment->update(['status' => 'ongoing']);
                    if ($this->sheetService->isConfigured()) {
                        $this->sheetService->update('assignments', $assignment->id, [
                            'status' => 'ongoing',
                            'updated_at' => now()->toISOString(),
                        ]);
                    }
                    $affectedCount++;
                }
                $msg = "Sebanyak {$affectedCount} kegiatan berhasil diubah statusnya menjadi Sedang Berjalan (Aktif).";
                break;

            case 'set_completed':
                foreach ($assignments as $assignment) {
                    $assignment->update(['status' => 'completed']);
                    if ($this->sheetService->isConfigured()) {
                        $this->sheetService->update('assignments', $assignment->id, [
                            'status' => 'completed',
                            'updated_at' => now()->toISOString(),
                        ]);
                    }
                    $affectedCount++;
                }
                $msg = "Sebanyak {$affectedCount} kegiatan berhasil diubah statusnya menjadi Selesai (Tuntas).";
                break;

            case 'set_draft':
                foreach ($assignments as $assignment) {
                    $assignment->update(['status' => 'draft']);
                    if ($this->sheetService->isConfigured()) {
                        $this->sheetService->update('assignments', $assignment->id, [
                            'status' => 'draft',
                            'updated_at' => now()->toISOString(),
                        ]);
                    }
                    $affectedCount++;
                }
                $msg = "Sebanyak {$affectedCount} kegiatan berhasil diubah statusnya menjadi Draft.";
                break;

            case 'set_cancelled':
                foreach ($assignments as $assignment) {
                    $assignment->update(['status' => 'cancelled']);
                    if ($this->sheetService->isConfigured()) {
                        $this->sheetService->update('assignments', $assignment->id, [
                            'status' => 'cancelled',
                            'updated_at' => now()->toISOString(),
                        ]);
                    }
                    $affectedCount++;
                }
                $msg = "Sebanyak {$affectedCount} kegiatan berhasil dibatalkan.";
                break;

            case 'enable_admin':
                foreach ($assignments as $assignment) {
                    $assignment->update(['is_enabled_by_admin' => true]);
                    $affectedCount++;
                }
                $msg = "Sebanyak {$affectedCount} penugasan diaktifkan secara manual oleh Admin (Aktif Manual).";
                break;

            case 'disable_admin':
                foreach ($assignments as $assignment) {
                    $assignment->update(['is_enabled_by_admin' => false]);
                    $affectedCount++;
                }
                $msg = "Aktif manual untuk {$affectedCount} penugasan dinonaktifkan (mengikuti aturan periode tanggal).";
                break;

            case 'delete':
                foreach ($assignments as $assignment) {
                    $id = $assignment->id;
                    $assignment->delete();
                    if ($this->sheetService->isConfigured()) {
                        $this->sheetService->delete('assignments', $id, true);
                    }
                    $affectedCount++;
                }
                $msg = "Sebanyak {$affectedCount} kegiatan berhasil dihapus.";
                break;

            case 'assign_officers':
                $officerIds = [];
                if (!empty($validated['officer_ids'])) {
                    $officerIds = array_merge($officerIds, $validated['officer_ids']);
                }
                if (!empty($validated['officer_id'])) {
                    $officerIds[] = $validated['officer_id'];
                }
                $officerIds = array_unique(array_filter($officerIds));

                $task = !empty($validated['specific_task']) ? $validated['specific_task'] : 'Pencacahan Lapangan';
                $assignedCount = 0;

                foreach ($assignments as $assignment) {
                    foreach ($officerIds as $officerId) {
                        $exists = AssignmentOfficer::where('assignment_id', $assignment->id)
                            ->where('user_id', $officerId)
                            ->exists();

                        if (!$exists) {
                            $activityRole = 'PCL';
                            if (stripos($task, 'pml') !== false || stripos($task, 'pengawas') !== false) {
                                $activityRole = 'PML';
                            } elseif (stripos($task, 'psms') !== false || stripos($task, 'koseka') !== false) {
                                $activityRole = 'PSMS';
                            }

                            $record = AssignmentOfficer::create([
                                'assignment_id' => $assignment->id,
                                'user_id' => $officerId,
                                'specific_task' => $task,
                                'activity_role' => $activityRole,
                                'status' => 'pending',
                            ]);

                            if ($this->sheetService->isConfigured()) {
                                $this->sheetService->insert('assignment_officers', [
                                    'id' => $record->id,
                                    'assignment_id' => $assignment->id,
                                    'user_id' => $officerId,
                                    'specific_task' => $task,
                                    'status' => 'pending',
                                    'progress_notes' => '',
                                    'created_at' => now()->toISOString(),
                                    'updated_at' => now()->toISOString(),
                                ]);
                            }
                            $assignedCount++;
                        }
                    }
                }
                $countPeople = count($officerIds);
                $msg = "Sebanyak {$countPeople} petugas/pegawai berhasil ditugaskan ke {$assignments->count()} kegiatan (total {$assignedCount} alokasi penugasan baru).";
                $affectedCount = $assignedCount;
                break;
        }

        ActivityLog::log(
            'bulk_update',
            'assignments',
            "Melakukan aksi massal '{$action}' pada {$affectedCount} kegiatan."
        );

        return back()->with('success', $msg);
    }

    /**
     * Assign a new officer with a specific task.
     */
    public function assignOfficer(Request $request, Assignment $assignment)
    {
        $validated = $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('assignment_officers', 'user_id')->where('assignment_id', $assignment->id),
            ],
            'specific_task' => ['required', 'string', 'max:500'],
            'progress_notes' => ['nullable', 'string', 'max:500'],
        ], [
            'user_id.required' => 'Petugas wajib dipilih.',
            'user_id.unique' => 'Petugas ini sudah ditugaskan pada kegiatan ini.',
            'specific_task.required' => 'Deskripsi tugas spesifik wajib diisi.',
        ]);

        $task = $validated['specific_task'];
        $activityRole = $request->input('activity_role');
        if (empty($activityRole)) {
            if (stripos($task, 'pml') !== false || stripos($task, 'pengawas') !== false) {
                $activityRole = 'PML';
            } elseif (stripos($task, 'psms') !== false || stripos($task, 'koseka') !== false) {
                $activityRole = 'PSMS';
            } else {
                $activityRole = 'PCL';
            }
        }

        $officerRecord = AssignmentOfficer::create([
            'assignment_id' => $assignment->id,
            'user_id' => $validated['user_id'],
            'specific_task' => $validated['specific_task'],
            'activity_role' => $activityRole,
            'status' => 'pending',
            'progress_notes' => $validated['progress_notes'] ?? null,
        ]);

        $user = User::find($validated['user_id']);

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->insert('assignment_officers', [
                'id' => $officerRecord->id,
                'assignment_id' => $assignment->id,
                'user_id' => $user->id,
                'specific_task' => $officerRecord->specific_task,
                'status' => $officerRecord->status,
                'progress_notes' => $officerRecord->progress_notes ?? '',
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
            ]);
        }

        ActivityLog::log(
            'create',
            'assignments',
            "Menugaskan petugas {$user->name} pada kegiatan {$assignment->title}."
        );

        return back()->with('success', "Petugas {$user->name} berhasil ditugaskan!");
    }

    /**
     * Update an officer's specific task, status, or progress notes.
     */
    public function updateOfficerTask(Request $request, Assignment $assignment, AssignmentOfficer $assignmentOfficer)
    {
        $validated = $request->validate([
            'specific_task' => ['required', 'string', 'max:500'],
            'status' => ['required', Rule::in(['pending', 'in_progress', 'completed'])],
            'progress_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $assignmentOfficer->update($validated);

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->update('assignment_officers', $assignmentOfficer->id, [
                'specific_task' => $assignmentOfficer->specific_task,
                'status' => $assignmentOfficer->status,
                'progress_notes' => $assignmentOfficer->progress_notes ?? '',
                'updated_at' => now()->toISOString(),
            ]);
        }

        ActivityLog::log(
            'update',
            'assignments',
            "Memperbarui tugas petugas {$assignmentOfficer->user->name} pada kegiatan {$assignment->title}."
        );

        return back()->with('success', 'Rincian tugas dan status petugas berhasil diperbarui!');
    }

    /**
     * Remove an officer from an assignment.
     */
    public function removeOfficer(Assignment $assignment, AssignmentOfficer $assignmentOfficer)
    {
        $officerName = $assignmentOfficer->user->name;
        $id = $assignmentOfficer->id;
        $assignmentOfficer->delete();

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->delete('assignment_officers', $id, false);
        }

        ActivityLog::log(
            'delete',
            'assignments',
            "Membatalkan penugasan petugas {$officerName} dari kegiatan {$assignment->title}."
        );

        return back()->with('success', "Penugasan untuk {$officerName} telah dihapus.");
    }

    private function validateAssignmentRequest(Request $request, bool $includeOfficers): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'location' => ['required', 'string', 'max:255'],
            'basis_dipa' => ['nullable', 'string', 'max:1000'],
            'st_number' => ['nullable', 'string', 'max:150'],
            'st_date' => ['nullable', 'date'],
            'basis_st' => ['nullable', 'string', 'max:1000'],
            'target_respondents' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['draft', 'ongoing', 'completed', 'cancelled'])],
            'is_enabled_by_admin' => ['nullable', 'boolean'],
            'excel_file' => ['nullable', 'file', 'extensions:xlsx,xls,csv,txt', 'max:10240'],
        ];

        if ($includeOfficers) {
            $rules['officer_ids'] = ['nullable', 'array'];
            $rules['officer_ids.*'] = ['exists:users,id'];
        }

        return $request->validate($rules, [
            'title.required' => 'Judul kegiatan wajib diisi.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'location.required' => 'Lokasi kegiatan wajib diisi.',
            'target_respondents.integer' => 'Target responden harus berupa angka bulat positif.',
            'target_respondents.min' => 'Target responden tidak boleh bernilai negatif.',
        ]);
    }

    private function buildAssignmentBasisPayload(array $validated): array
    {
        $defaults = OfficerReport::getMasterBasisSettings();
        $basisDipa = trim((string) ($validated['basis_dipa'] ?? '')) ?: $defaults['basis_dipa'];
        $stNumber = trim((string) ($validated['st_number'] ?? '')) ?: $defaults['st_number'];
        $stDate = !empty($validated['st_date']) ? $validated['st_date'] : $defaults['st_date'];
        $basisSt = trim((string) ($validated['basis_st'] ?? ''));

        if ($basisSt === '') {
            $basisSt = OfficerReport::formatBasisSt($stNumber, $stDate);
        }

        return [
            'basis_dipa' => $basisDipa,
            'st_number' => $stNumber,
            'st_date' => $stDate,
            'basis_st' => $basisSt,
        ];
    }

    /**
     * Download CSV/Excel template for bulk activity & respondent samples assignment.
     */
    public function downloadTemplate(Request $request)
    {
        // Headers matching the official BPS field officers spreadsheet: NO, Nama, Jenis Kelamin, Penugasan, Kecamatan Asal, Kelas, BANK
        $headersList = [
            'NO',
            'Nama',
            'Jenis Kelamin',
            'Penugasan',
            'Kecamatan Asal',
            'Kelas',
            'BANK',
        ];

        if ($request->query('format') === 'xlsx') {
            $tmpPath = tempnam(sys_get_temp_dir(), 'tpl_xlsx') . '.xlsx';
            $this->createCleanXlsxTemplate($tmpPath, $headersList);

            return response()->stream(function () use ($tmpPath) {
                readfile($tmpPath);
                @unlink($tmpPath);
            }, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename=Template_Kegiatan_dan_Sampel_SIMPEL.xlsx',
            ]);
        }

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename=Template_Kegiatan_dan_Sampel_SIMPEL.csv',
        ];

        $callback = function () use ($headersList) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM so Excel opens with proper encoding
            fputs($handle, "\xEF\xBB\xBF");

            // Header matching the template exactly with empty data rows
            fputcsv($handle, $headersList);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate an empty XLSX template file matching the exact BPS field officers spreadsheet format.
     */
    protected function createCleanXlsxTemplate(string $outputPath, array $headers): void
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
    <sheet name="Daftar Petugas" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>');

        $ssXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="'.count($headers).'" uniqueCount="'.count($headers).'">';
        foreach ($headers as $h) {
            $ssXml .= '<si><t>' . htmlspecialchars($h, ENT_XML1) . '</t></si>';
        }
        $ssXml .= '</sst>';
        $zip->addFromString('xl/sharedStrings.xml', $ssXml);

        $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <cols>
    <col min="1" max="1" width="8" customWidth="1"/>
    <col min="2" max="2" width="28" customWidth="1"/>
    <col min="3" max="3" width="18" customWidth="1"/>
    <col min="4" max="4" width="30" customWidth="1"/>
    <col min="5" max="5" width="22" customWidth="1"/>
    <col min="6" max="6" width="24" customWidth="1"/>
    <col min="7" max="7" width="18" customWidth="1"/>
  </cols>
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
  </sheetData>
</worksheet>';
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
        $zip->close();
    }

    /**
     * Preview and parse Excel/CSV content via AJAX to auto-fill the assignment creation form.
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
                'message' => 'Berkas Excel kosong atau struktur kolom tidak sesuai dengan template.',
            ], 422);
        }

        $firstRow = $parsedRows[0];
        $title = trim($firstRow['nama_kegiatan'] ?? '');
        $dipa = trim($firstRow['pembebanan_anggaran'] ?? '');

        $districts = array_values(array_unique(array_filter(array_map(fn($r) => trim($r['wilayah_tugas'] ?? ''), $parsedRows))));
        $location = !empty($districts)
            ? 'Kecamatan ' . implode(', ', array_slice($districts, 0, 4)) . (count($districts) > 4 ? ' dll' : '')
            : 'Kabupaten Kolaka Utara';

        $officerSummary = [];
        $sampleCount = 0;

        foreach ($parsedRows as $row) {
            $pName = trim($row['nama_petugas'] ?? '');
            $sName = trim($row['nama_sampel'] ?? '');
            if (!empty($sName)) {
                $sampleCount++;
            }

            if (!empty($pName)) {
                if (!isset($officerSummary[$pName])) {
                    $officerSummary[$pName] = [
                        'name' => $pName,
                        'id_sobat' => trim((string) ($row['id_sobat'] ?? '')),
                        'task' => trim($row['penugasan'] ?? '') ?: 'Petugas Pendataan Lapangan',
                        'district' => trim($row['wilayah_tugas'] ?? '') ?: 'Lasusua',
                        'gender' => trim($row['jenis_kelamin'] ?? ''),
                        'samples' => 0,
                    ];
                }
                if (!empty($sName)) {
                    $officerSummary[$pName]['samples']++;
                }
            }
        }

        $allOfficers = User::fieldStaff()->get();
        $matchedIds = [];
        $unmatchedOfficers = [];

        foreach ($officerSummary as $name => $info) {
            $matchedUser = null;
            if (!empty($info['id_sobat'])) {
                $matchedUser = $allOfficers->firstWhere('sobat_id', $info['id_sobat']);
            }
            if (!$matchedUser) {
                $matchedUser = $allOfficers->first(function ($u) use ($name) {
                    return strtolower(trim($u->name)) === strtolower(trim($name));
                });
            }

            if ($matchedUser) {
                $matchedIds[] = $matchedUser->id;
            } else {
                $unmatchedOfficers[] = $name;
            }
        }

        $officerCount = count($officerSummary);
        $message = ($sampleCount > 0)
            ? "Berhasil membaca berkas! Ditemukan kegiatan '{$title}', {$officerCount} petugas, dan {$sampleCount} sampel responden."
            : "Berhasil membaca berkas! Ditemukan {$officerCount} petugas lapangan di " . count($districts) . " kecamatan asal.";

        return response()->json([
            'success' => true,
            'title' => $title,
            'basis_dipa' => $dipa,
            'location' => $location,
            'districts' => $districts,
            'total_samples' => $sampleCount,
            'officers' => array_values($officerSummary),
            'matched_officer_ids' => array_values(array_unique($matchedIds)),
            'unmatched_officers' => $unmatchedOfficers,
            'message' => $message,
        ]);
    }

    /**
     * Process parsed Excel/CSV rows and attach officers and respondents to an assignment.
     */
    public function processParsedRowsForAssignment(Assignment $assignment, array $parsedRows): array
    {
        $createdOfficers = 0;
        $assignedOfficers = 0;
        $createdRespondents = 0;

        foreach ($parsedRows as $row) {
            $namaPetugas = trim($row['nama_petugas'] ?? '');
            $idSobat = trim((string) ($row['id_sobat'] ?? ''));
            $penugasan = trim($row['penugasan'] ?? '') ?: 'Petugas Pendataan Lapangan';
            $wilayah = trim($row['wilayah_tugas'] ?? '') ?: 'Lasusua';
            $namaSampel = trim($row['nama_sampel'] ?? '');

            if (empty($namaPetugas) && empty($namaSampel)) {
                continue;
            }

            // 1. Find or create officer
            $officer = null;
            if (!empty($idSobat)) {
                $officer = User::where('sobat_id', $idSobat)->first();
            }
            if (!$officer && !empty($namaPetugas)) {
                $officer = User::whereRaw('LOWER(name) = ?', [strtolower($namaPetugas)])->first();
            }

            if (!$officer && !empty($namaPetugas)) {
                $slugBase = Str::slug($namaPetugas, '');
                if (empty($slugBase)) {
                    $slugBase = 'petugas' . rand(100, 999);
                }
                $cleanUsername = $slugBase;
                $counter = 1;
                while (User::where('username', $cleanUsername)->exists()) {
                    $cleanUsername = $slugBase . $counter++;
                }

                $cleanEmail = Str::slug($namaPetugas, '.') . '@bps.go.id';
                $emailCounter = 1;
                while (User::where('email', $cleanEmail)->exists()) {
                    $cleanEmail = Str::slug($namaPetugas, '.') . $emailCounter++ . '@bps.go.id';
                }

                $gender = trim($row['jenis_kelamin'] ?? '');
                $genderVal = null;
                if (!empty($gender)) {
                    $genderVal = (stripos($gender, 'p') === 0 || stripos($gender, 'wanita') !== false) ? 'Perempuan' : 'Laki-Laki';
                }

                $officer = User::create([
                    'name' => $namaPetugas,
                    'sobat_id' => $idSobat ?: null,
                    'username' => $cleanUsername,
                    'email' => $cleanEmail,
                    'password' => Hash::make('bps7408'),
                    'role' => (stripos($penugasan, 'pml') !== false || stripos($penugasan, 'pengawas') !== false || stripos($penugasan, 'pemeriksa') !== false) ? 'pegawai' : 'petugas',
                    'status' => 'active',
                    'district' => $wilayah,
                    'gender' => $genderVal,
                ]);
                $createdOfficers++;
            }

            // 2. Assign officer to assignment
            if ($officer) {
                $activityRole = 'PCL';
                if (stripos($penugasan, 'pml') !== false || stripos($penugasan, 'pengawas') !== false) {
                    $activityRole = 'PML';
                } elseif (stripos($penugasan, 'psms') !== false || stripos($penugasan, 'koseka') !== false) {
                    $activityRole = 'PSMS';
                } elseif (stripos($penugasan, 'pcl') !== false || stripos($penugasan, 'pencacah') !== false) {
                    $activityRole = 'PCL';
                }

                $link = AssignmentOfficer::firstOrCreate(
                    [
                        'assignment_id' => $assignment->id,
                        'user_id' => $officer->id,
                    ],
                    [
                        'specific_task' => $penugasan,
                        'activity_role' => $activityRole,
                        'status' => 'pending',
                    ]
                );
                if ($link->wasRecentlyCreated) {
                    $assignedOfficers++;
                } elseif (empty($link->activity_role) || $link->activity_role === 'PCL') {
                    $link->update(['activity_role' => $activityRole]);
                }
            }

            // 3. Create Sample Respondent
            if (!empty($namaSampel)) {
                $exists = Respondent::where('assignment_id', $assignment->id)
                    ->where('officer_id', $officer?->id)
                    ->where('name', $namaSampel)
                    ->exists();

                if (!$exists) {
                    Respondent::create([
                        'name' => $namaSampel,
                        'family_head_name' => $namaSampel,
                        'informant_name' => $namaSampel,
                        'agency' => 'Rumah Tangga / KRT',
                        'purpose' => $assignment->title,
                        'district' => $wilayah,
                        'address' => 'Kecamatan ' . $wilayah,
                        'assignment_id' => $assignment->id,
                        'officer_id' => $officer?->id,
                        'respondent_status' => 'Belum Terdata',
                        'progress_status' => 'belum_didata',
                        'input_date' => now()->toDateString(),
                    ]);
                    $createdRespondents++;
                }
            }
        }

        // Update total target respondent if respondents were imported
        $respCount = $assignment->respondents()->count();
        if ($respCount > 0) {
            $assignment->update([
                'target_respondents' => $respCount,
            ]);
        }

        return [
            'officers' => $assignedOfficers,
            'new_officers' => $createdOfficers,
            'respondents' => $createdRespondents,
        ];
    }

    /**
     * Bulk import assignments, officer allocations, and sample respondents from Excel / CSV.
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => ['required', 'file', 'extensions:xlsx,xls,csv,txt', 'max:10240'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'st_number' => ['nullable', 'string', 'max:100'],
            'st_date' => ['nullable', 'date'],
        ], [
            'excel_file.required' => 'Pilih berkas Excel (.xlsx) atau CSV yang akan diunggah.',
            'excel_file.extensions' => 'Format berkas harus berupa .xlsx, .xls, atau .csv.',
            'excel_file.max' => 'Ukuran berkas maksimal adalah 10 MB.',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ]);

        $file = $request->file('excel_file');
        $ext = $file->getClientOriginalExtension();

        try {
            $parsedRows = $this->excelParser->parse($file->getRealPath(), $ext);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memproses berkas Excel: ' . $e->getMessage());
        }

        if (empty($parsedRows)) {
            return back()->with('error', 'Berkas Excel kosong atau kolom template tidak sesuai.');
        }

        $startDate = $request->input('start_date') ?: now()->toDateString();
        $endDate = $request->input('end_date') ?: now()->addDays(14)->toDateString();
        $stNumber = trim((string) $request->input('st_number'));
        $stDate = $request->input('st_date');

        $createdAssignments = 0;
        $totalOfficers = 0;
        $totalNewOfficers = 0;
        $totalRespondents = 0;
        $activityTitles = [];

        DB::beginTransaction();
        try {
            $groupedByActivity = [];
            foreach ($parsedRows as $row) {
                $actTitle = trim($row['nama_kegiatan'] ?? '') ?: 'Kegiatan Pendataan Lapangan ' . now()->year;
                $groupedByActivity[$actTitle][] = $row;
            }

            foreach ($groupedByActivity as $title => $rows) {
                $firstRow = $rows[0];
                $dipa = trim($firstRow['pembebanan_anggaran'] ?? '') ?: 'DIPA BPS Kabupaten Kolaka Utara';
                $districts = array_unique(array_filter(array_map(fn($r) => trim($r['wilayah_tugas'] ?? ''), $rows)));
                $location = !empty($districts)
                    ? 'Kecamatan ' . implode(', ', array_slice($districts, 0, 4)) . (count($districts) > 4 ? ' dll' : '')
                    : 'Kabupaten Kolaka Utara';

                $assignment = Assignment::firstOrCreate(
                    ['title' => $title],
                    [
                        'description' => 'Kegiatan dan alokasi sampel diimpor secara otomatis dari berkas Excel/Spreadsheet.',
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'location' => $location,
                        'basis_dipa' => $dipa,
                        'st_number' => $stNumber ?: null,
                        'st_date' => $stDate ?: null,
                        'basis_st' => $stNumber ? OfficerReport::formatBasisSt($stNumber, $stDate) : null,
                        'target_respondents' => 0,
                        'status' => 'ongoing',
                        'created_by' => Auth::id(),
                    ]
                );

                if ($assignment->wasRecentlyCreated) {
                    $createdAssignments++;
                }

                $activityTitles[] = $assignment->title;

                $counts = $this->processParsedRowsForAssignment($assignment, $rows);
                $totalOfficers += $counts['officers'];
                $totalNewOfficers += $counts['new_officers'];
                $totalRespondents += $counts['respondents'];
            }

            DB::commit();

            ActivityLog::log(
                'import',
                'assignment',
                "Administrator mengimpor template Excel: {$totalRespondents} sampel responden, {$totalOfficers} penugasan petugas pada " . implode(', ', array_unique($activityTitles)) . "."
            );

            $msg = "Import berhasil! {$totalRespondents} sampel responden berhasil dialokasikan kepada petugas lapangan.";
            if ($totalNewOfficers > 0) {
                $msg .= " ({$totalNewOfficers} akun petugas baru otomatis dibuatkan dengan password default: bps7408).";
            }

            return back()->with('success', $msg);

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses data Excel: ' . $e->getMessage());
        }
    }

    /**
     * Global Admin control: Enable or Disable all assignments visibility for Petugas.
     */
    public function toggleGlobalVisibility(Request $request)
    {
        $newState = $request->has('enabled')
            ? $request->boolean('enabled')
            : !Assignment::isGlobalPortalEnabled();

        Assignment::setGlobalPortalEnabled($newState);

        $label = $newState ? 'DIAKTIFKAN' : 'DINONAKTIFKAN';
        ActivityLog::log(
            'toggle_global',
            'assignments',
            "Administrator mengubah status global Penugasan Lapangan menjadi: {$label}."
        );

        $msg = $newState
            ? 'Penugasan Lapangan berhasil DIAKTIFKAN. Penugasan tampil di dashboard petugas sesuai aturan Admin & Periode.'
            : 'Penugasan Lapangan berhasil DINONAKTIFKAN. Seluruh penugasan kini disembunyikan dari dashboard petugas.';

        return back()->with('success', $msg);
    }

    /**
     * Per-assignment Admin toggle: Aktifkan / Nonaktifkan is_enabled_by_admin.
     */
    public function toggleAdminEnabled(Request $request, Assignment $assignment)
    {
        $newState = $request->has('is_enabled_by_admin')
            ? $request->boolean('is_enabled_by_admin')
            : !$assignment->is_enabled_by_admin;

        $assignment->update(['is_enabled_by_admin' => $newState]);

        $statusDesc = $newState ? 'Aktif Manual (Admin)' : 'Nonaktif Manual (Mengikuti Periode)';
        ActivityLog::log(
            'toggle_admin',
            'assignments',
            "Administrator mengubah kontrol manual penugasan '{$assignment->title}' menjadi: {$statusDesc}."
        );

        $msg = $newState
            ? "Penugasan '{$assignment->title}' diaktifkan secara manual (Aktif Manual Admin)."
            : "Aktif manual untuk penugasan '{$assignment->title}' dinonaktifkan. Status tampil mengikuti periode tanggal.";

        return back()->with('success', $msg);
    }

    /**
     * Update assignment period (start_date, end_date) & optional admin flag.
     */
    public function updatePeriod(Request $request, Assignment $assignment)
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_enabled_by_admin' => ['nullable', 'boolean'],
        ], [
            'start_date.required' => 'Tanggal mulai periode penugasan wajib diisi.',
            'end_date.required' => 'Tanggal selesai periode penugasan wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ]);

        $payload = [
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
        ];

        if ($request->has('is_enabled_by_admin')) {
            $payload['is_enabled_by_admin'] = $request->boolean('is_enabled_by_admin');
        }

        $assignment->update($payload);

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->update('assignments', $assignment->id, [
                'start_date' => $assignment->start_date->toDateString(),
                'end_date' => $assignment->end_date->toDateString(),
                'updated_at' => now()->toISOString(),
            ]);
        }

        ActivityLog::log(
            'update_period',
            'assignments',
            "Memperbarui periode penugasan '{$assignment->title}' menjadi {$assignment->start_date->format('d/m/Y')} s.d. {$assignment->end_date->format('d/m/Y')}."
        );

        return back()->with('success', "Periode penugasan '{$assignment->title}' berhasil diperbarui!");
    }

    /**
     * Assignment History (Riwayat Penugasan & Log Kontrol Admin/Periode).
     */
    public function history(Request $request)
    {
        $todayStr = now()->toDateString();

        $historyQuery = Assignment::withTrashed()
            ->withCount(['officers', 'respondents'])
            ->with('creator')
            ->where(function ($q) use ($todayStr) {
                $q->whereNotNull('deleted_at')
                  ->orWhere('status', 'completed')
                  ->orWhere('status', 'cancelled')
                  ->orWhereDate('end_date', '<', $todayStr);
            });

        if ($search = $request->input('search')) {
            $historyQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $historicalAssignments = $historyQuery->latest('end_date')->paginate(10, ['*'], 'history_page')->withQueryString();

        $activityLogs = ActivityLog::with('user')
            ->whereIn('module', ['assignments', 'assignment'])
            ->latest()
            ->paginate(15, ['*'], 'log_page')
            ->withQueryString();

        $globalAssignmentEnabled = Assignment::isGlobalPortalEnabled();

        return view('admin.assignments.history', compact(
            'historicalAssignments',
            'activityLogs',
            'globalAssignmentEnabled'
        ));
    }
}

