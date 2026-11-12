<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\AssignmentOfficer;
use App\Models\OfficerReport;
use App\Models\User;
use App\Services\GoogleSheetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AssignmentController extends Controller
{
    protected GoogleSheetService $sheetService;

    public function __construct(GoogleSheetService $sheetService)
    {
        $this->sheetService = $sheetService;
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

        // Stats
        $totalAssignments = Assignment::count();
        $totalOngoing = Assignment::where('status', 'ongoing')->count();
        $totalCompleted = Assignment::where('status', 'completed')->count();
        $totalDraft = Assignment::where('status', 'draft')->count();

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
            ->with('success', "Kegiatan '{$assignment->title}' berhasil dibuat! Silakan tentukan pembagian tugas masing-masing petugas.");
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

        $officerRecord = AssignmentOfficer::create([
            'assignment_id' => $assignment->id,
            'user_id' => $validated['user_id'],
            'specific_task' => $validated['specific_task'],
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
}

