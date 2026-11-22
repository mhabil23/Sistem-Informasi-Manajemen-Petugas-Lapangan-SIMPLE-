<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AssignmentOfficer;
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
     * Display a listing of assigned tasks for the logged in officer.
     */
    public function index()
    {
        $userId = Auth::id();

        $assignedTasks = AssignmentOfficer::with(['assignment.creator'])
            ->where('user_id', $userId)
            ->latest()
            ->paginate(10);

        $totalTasks = AssignmentOfficer::where('user_id', $userId)->count();
        $inProgressTasks = AssignmentOfficer::where('user_id', $userId)->where('status', 'in_progress')->count();
        $completedTasks = AssignmentOfficer::where('user_id', $userId)->where('status', 'completed')->count();
        $pendingTasks = AssignmentOfficer::where('user_id', $userId)->where('status', 'pending')->count();

        return view('petugas.assignments.index', compact(
            'assignedTasks',
            'totalTasks',
            'inProgressTasks',
            'completedTasks',
            'pendingTasks'
        ));
    }

    /**
     * Officer updates their own progress notes and status.
     */
    public function updateProgress(Request $request, AssignmentOfficer $assignmentOfficer)
    {
        // Ensure officer only updates their own task
        if ($assignmentOfficer->user_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki hak untuk memperbarui tugas ini.');
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'in_progress', 'completed'])],
            'progress_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $assignmentOfficer->update($validated);

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->update('assignment_officers', $assignmentOfficer->id, [
                'status' => $assignmentOfficer->status,
                'progress_notes' => $assignmentOfficer->progress_notes ?? '',
                'updated_at' => now()->toISOString(),
            ]);
        }

        ActivityLog::log(
            'update',
            'assignments',
            "Petugas " . Auth::user()->name . " memperbarui progres tugas: '{$assignmentOfficer->assignment->title}' ({$validated['status']})."
        );

        return back()->with('success', 'Perkembangan tugas berhasil disimpan!');
    }

    /**
     * Aksi massal petugas/pegawai memperbarui progres beberapa tugas sekaligus.
     */
    public function bulkProgress(Request $request)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'in_progress', 'completed'])],
            'task_ids' => ['required', 'array', 'min:1'],
            'task_ids.*' => ['integer'],
        ], [
            'task_ids.required' => 'Pilih minimal satu tugas untuk melakukan pembaruan massal.',
            'task_ids.min' => 'Pilih minimal satu tugas untuk melakukan pembaruan massal.',
        ]);

        $userId = Auth::id();
        $status = $validated['status'];
        $taskIds = array_unique($validated['task_ids']);

        $tasks = AssignmentOfficer::where('user_id', $userId)
            ->whereIn('id', $taskIds)
            ->get();

        $affectedCount = 0;
        foreach ($tasks as $task) {
            $task->update(['status' => $status]);
            if ($this->sheetService->isConfigured()) {
                $this->sheetService->update('assignment_officers', $task->id, [
                    'status' => $status,
                    'updated_at' => now()->toISOString(),
                ]);
            }
            $affectedCount++;
        }

        $statusLabels = [
            'pending' => 'Menunggu (Belum Dimulai)',
            'in_progress' => 'Sedang Berjalan',
            'completed' => 'Selesai (Tuntas)',
        ];

        ActivityLog::log(
            'bulk_update',
            'assignments',
            "Petugas " . Auth::user()->name . " memperbarui status massal {$affectedCount} tugas menjadi: {$statusLabels[$status]}."
        );

        return back()->with('success', "Sebanyak {$affectedCount} tugas berhasil diperbarui statusnya menjadi {$statusLabels[$status]}!");
    }
}

