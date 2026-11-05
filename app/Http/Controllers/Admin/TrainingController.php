<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\TrainingEvent;
use App\Models\TrainingParticipant;
use App\Services\GoogleSheetService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TrainingController extends Controller
{
    protected GoogleSheetService $sheetService;

    public function __construct(GoogleSheetService $sheetService)
    {
        $this->sheetService = $sheetService;
    }

    public function index(Request $request)
    {
        $query = TrainingEvent::withCount('participants')->with('creator');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        $totalEvents = TrainingEvent::count();
        $upcomingCount = TrainingEvent::where('status', 'upcoming')->count();
        $ongoingCount = TrainingEvent::where('status', 'ongoing')->count();
        $completedCount = TrainingEvent::where('status', 'completed')->count();
        $totalParticipants = TrainingParticipant::count();

        $trainings = $query->latest('start_date')->paginate(10)->withQueryString();

        return view('admin.trainings.index', compact(
            'trainings',
            'totalEvents',
            'upcomingCount',
            'ongoingCount',
            'completedCount',
            'totalParticipants'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'location' => ['required', 'string', 'max:255'],
            'quota' => ['required', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['upcoming', 'ongoing', 'completed', 'cancelled'])],
        ], [
            'title.required' => 'Nama kegiatan pelatihan wajib diisi.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'location.required' => 'Lokasi pelatihan wajib diisi.',
        ]);

        $validated['created_by'] = Auth::id();

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
            "Membuat kegiatan pelatihan baru: {$training->title}."
        );

        return redirect()->route('admin.trainings.index')
            ->with('success', "Kegiatan pelatihan '{$training->title}' berhasil dibuat!");
    }

    public function show(Request $request, TrainingEvent $training)
    {
        $training->load('creator');

        $query = $training->participants();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nip_or_id', 'like', "%{$search}%")
                  ->orWhere('agency', 'like', "%{$search}%")
                  ->orWhere('certificate_number', 'like', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            if ($category !== 'all') {
                $query->where('category', $category);
            }
        }

        if ($attendance = $request->input('attendance')) {
            if ($attendance !== 'all') {
                $query->where('attendance_status', $attendance);
            }
        }

        $participants = $query->latest('id')->paginate(15)->withQueryString();

        // Distinct categories for quick filter
        $availableCategories = TrainingParticipant::where('training_event_id', $training->id)
            ->distinct()
            ->pluck('category')
            ->toArray();

        // Default categories if empty
        if (empty($availableCategories)) {
            $availableCategories = ['DPW', 'Magang Mitra', 'Pegawai'];
        }

        $presentCount = $training->participants()->where('attendance_status', 'present')->count();
        $absentCount = $training->participants()->where('attendance_status', 'absent')->count();
        $excusedCount = $training->participants()->where('attendance_status', 'excused')->count();

        return view('admin.trainings.show', compact(
            'training',
            'participants',
            'availableCategories',
            'presentCount',
            'absentCount',
            'excusedCount'
        ));
    }

    public function update(Request $request, TrainingEvent $training)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'location' => ['required', 'string', 'max:255'],
            'quota' => ['required', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['upcoming', 'ongoing', 'completed', 'cancelled'])],
        ]);

        $training->update($validated);

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->update('training_events', $training->id, [
                'title' => $training->title,
                'description' => $training->description ?? '',
                'start_date' => $training->start_date->toDateString(),
                'end_date' => $training->end_date->toDateString(),
                'location' => $training->location,
                'quota' => $training->quota,
                'status' => $training->status,
                'updated_at' => now()->toISOString(),
            ]);
        }

        ActivityLog::log(
            'update',
            'trainings',
            "Memperbarui data kegiatan pelatihan: {$training->title}."
        );

        return back()->with('success', "Kegiatan pelatihan '{$training->title}' berhasil diperbarui.");
    }

    public function destroy(TrainingEvent $training)
    {
        $title = $training->title;
        $id = $training->id;
        $training->delete();

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->delete('training_events', $id, true);
        }

        ActivityLog::log(
            'delete',
            'trainings',
            "Menghapus kegiatan pelatihan: {$title}."
        );

        return redirect()->route('admin.trainings.index')->with('success', "Kegiatan '{$title}' berhasil dihapus.");
    }

    // ==========================================
    // PESERTA PELATIHAN
    // ==========================================
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
        ], [
            'name.required' => 'Nama peserta wajib diisi.',
            'category.required' => 'Kategori peserta (DPW / Pegawai / Magang Mitra) wajib diisi.',
        ]);

        $participant = $training->participants()->create($validated);

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->insert('training_participants', [
                'id' => $participant->id,
                'training_event_id' => $training->id,
                'name' => $participant->name,
                'nip_or_id' => $participant->nip_or_id ?? '',
                'category' => $participant->category,
                'agency' => $participant->agency ?? '',
                'attendance_status' => $participant->attendance_status,
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
            ]);
        }

        ActivityLog::log(
            'create',
            'trainings',
            "Menambahkan peserta {$participant->name} ({$participant->category}) pada pelatihan {$training->title}."
        );

        return back()->with('success', "Peserta '{$participant->name}' berhasil ditambahkan ke daftar hadir!");
    }

    public function updateParticipant(Request $request, TrainingEvent $training, TrainingParticipant $participant)
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

        $participant->update($validated);

        return back()->with('success', "Data peserta '{$participant->name}' berhasil diperbarui.");
    }

    public function destroyParticipant(TrainingEvent $training, TrainingParticipant $participant)
    {
        $name = $participant->name;
        $participant->delete();

        return back()->with('success', "Peserta '{$name}' telah dihapus dari kegiatan.");
    }

    public function exportAttendancePdf(TrainingEvent $training)
    {
        $training->load(['creator', 'participants']);

        $pdf = Pdf::loadView('pdf.training_attendance', compact('training'))
                  ->setPaper('a4', 'portrait');

        $filename = 'Daftar-Hadir-' . \Illuminate\Support\Str::slug($training->title) . '.pdf';

        return $pdf->download($filename);
    }
}

