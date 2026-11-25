<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\SpkrtPeriod;
use App\Services\BerandaMenuVisibilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpkrtPeriodController extends Controller
{
    public function index(Request $request)
    {
        $query = SpkrtPeriod::with('creator');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('activity_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $allPeriods = SpkrtPeriod::all();
        $totalPeriods = $allPeriods->count();
        $totalActive = $allPeriods->filter(fn($p) => $p->isOpen())->count();
        $totalNotStarted = $allPeriods->filter(fn($p) => $p->is_active && $p->currentStatus() === 'not_started')->count();
        $totalClosed = $allPeriods->filter(fn($p) => !$p->isOpen() && $p->currentStatus() !== 'not_started')->count();

        if ($statusFilter = $request->input('status')) {
            if ($statusFilter === 'open') {
                $query->where('is_active', true)
                      ->where('start_date', '<=', Carbon::today())
                      ->where('end_date', '>=', Carbon::today());
            } elseif ($statusFilter === 'not_started') {
                $query->where('is_active', true)
                      ->where('start_date', '>', Carbon::today());
            } elseif ($statusFilter === 'closed') {
                $query->where(function ($q) {
                    $q->where('is_active', false)
                      ->orWhere('end_date', '<', Carbon::today());
                });
            }
        }

        $periods = $query->latest('id')->paginate(10)->withQueryString();

        // Optional list of assignments if admin wants to pick an existing activity name
        $assignments = Assignment::orderBy('title')->get(['id', 'title']);
        $berandaState = app(BerandaMenuVisibilityService::class)->getState();

        // Tentukan tab aktif ('beranda' atau 'spkrt')
        $activeTab = $request->input('tab');
        if (!$activeTab) {
            $activeTab = $request->hasAny(['search', 'status', 'page']) ? 'spkrt' : 'beranda';
        }

        return view('admin.spkrt_periods.index', compact(
            'periods',
            'totalPeriods',
            'totalActive',
            'totalNotStarted',
            'totalClosed',
            'assignments',
            'berandaState',
            'activeTab'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_active' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [
            'activity_name.required' => 'Nama kegiatan wajib diisi.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
        ]);

        $period = SpkrtPeriod::create([
            'activity_name' => $validated['activity_name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $request->boolean('is_active', true),
            'description' => $validated['description'] ?? null,
            'created_by' => Auth::id(),
        ]);

        if (class_exists(ActivityLog::class)) {
            ActivityLog::log('create', 'spkrt_periods', "Membuat periode pengisian SPKRT: {$period->activity_name}");
        }

        return redirect()->route('admin.spkrt-periods.index')
            ->with('success', 'Periode pengisian SPKRT berhasil dibuat.');
    }

    public function update(Request $request, SpkrtPeriod $spkrtPeriod)
    {
        $validated = $request->validate([
            'activity_name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_active' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [
            'activity_name.required' => 'Nama kegiatan wajib diisi.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
        ]);

        $spkrtPeriod->update([
            'activity_name' => $validated['activity_name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $spkrtPeriod->is_active,
            'description' => $validated['description'] ?? null,
        ]);

        if (class_exists(ActivityLog::class)) {
            ActivityLog::log('update', 'spkrt_periods', "Memperbarui periode pengisian SPKRT: {$spkrtPeriod->activity_name}");
        }

        return redirect()->route('admin.spkrt-periods.index')
            ->with('success', 'Periode pengisian SPKRT berhasil diperbarui.');
    }

    public function toggleStatus(SpkrtPeriod $spkrtPeriod)
    {
        $newStatus = !$spkrtPeriod->is_active;
        $spkrtPeriod->update(['is_active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';

        if (class_exists(ActivityLog::class)) {
            ActivityLog::log('toggle_status', 'spkrt_periods', "Status periode SPKRT '{$spkrtPeriod->activity_name}' {$statusText}");
        }

        return redirect()->back()
            ->with('success', "Status periode '{$spkrtPeriod->activity_name}' berhasil {$statusText}.");
    }

    public function destroy(SpkrtPeriod $spkrtPeriod)
    {
        $name = $spkrtPeriod->activity_name;
        $spkrtPeriod->delete();

        if (class_exists(ActivityLog::class)) {
            ActivityLog::log('delete', 'spkrt_periods', "Menghapus periode pengisian SPKRT: {$name}");
        }

        return redirect()->route('admin.spkrt-periods.index')
            ->with('success', 'Periode pengisian SPKRT berhasil dihapus.');
    }
}
