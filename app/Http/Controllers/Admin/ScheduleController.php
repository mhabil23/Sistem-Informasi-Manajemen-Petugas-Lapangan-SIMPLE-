<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\Schedule;
use App\Models\User;
use App\Services\GoogleSheetService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ScheduleController extends Controller
{
    protected GoogleSheetService $sheetService;

    public function __construct(GoogleSheetService $sheetService)
    {
        $this->sheetService = $sheetService;
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

        return view('admin.schedules.index', compact(
            'schedules',
            'totalSchedules',
            'totalUpcoming',
            'totalInProgress',
            'totalCompleted',
            'assignments',
            'officers'
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
}

