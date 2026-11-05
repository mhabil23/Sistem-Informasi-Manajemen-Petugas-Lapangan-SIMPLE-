<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\Respondent;
use App\Models\User;
use App\Services\GoogleSheetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RespondentController extends Controller
{
    protected GoogleSheetService $sheetService;

    public function __construct(GoogleSheetService $sheetService)
    {
        $this->sheetService = $sheetService;
    }

    public function index(Request $request)
    {
        $query = Respondent::with(['assignment', 'officer', 'basts']);

        // 1. Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('agency', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('purpose', 'like', "%{$search}%");
            });
        }

        // 2. Filter by Assignment
        if ($assignmentId = $request->input('assignment_id')) {
            $query->where('assignment_id', $assignmentId);
        }

        // 3. Filter by Officer
        if ($officerId = $request->input('officer_id')) {
            $query->where('officer_id', $officerId);
        }

        // 4. Filter by Date
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('input_date', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('input_date', '<=', $dateTo);
        }

        // 5. Statistics
        $totalRespondents = Respondent::count();
        $thisMonthCount = Respondent::whereMonth('input_date', now()->month)
                                    ->whereYear('input_date', now()->year)
                                    ->count();
        $uniqueAgencies = Respondent::distinct('agency')->count('agency');
        $withBastCount = Respondent::has('basts')->count();

        $respondents = $query->latest('input_date')->paginate(10)->withQueryString();

        $assignments = Assignment::orderBy('title')->get();
        $officers = User::where('role', 'petugas')->where('status', 'active')->orderBy('name')->get();

        return view('admin.respondents.index', compact(
            'respondents',
            'totalRespondents',
            'thisMonthCount',
            'uniqueAgencies',
            'withBastCount',
            'assignments',
            'officers'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'district' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:25'],
            'agency' => ['required', 'string', 'max:255'],
            'purpose' => ['required', 'string', 'max:500'],
            'assignment_id' => ['nullable', 'exists:assignments,id'],
            'officer_id' => ['required', 'exists:users,id'],
            'input_date' => ['required', 'date'],
        ], [
            'name.required' => 'Nama responden wajib diisi.',
            'address.required' => 'Alamat responden wajib diisi.',
            'agency.required' => 'Instansi / lembaga responden wajib diisi.',
            'purpose.required' => 'Tujuan kegiatan wajib diisi.',
            'officer_id.required' => 'Petugas penginput wajib dipilih.',
            'input_date.required' => 'Tanggal input data wajib diisi.',
        ]);

        $respondent = Respondent::create($validated);

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->insert('respondents', [
                'id' => $respondent->id,
                'name' => $respondent->name,
                'address' => $respondent->address,
                'phone' => $respondent->phone ?? '',
                'agency' => $respondent->agency,
                'purpose' => $respondent->purpose,
                'assignment_id' => $respondent->assignment_id ?? '',
                'officer_id' => $respondent->officer_id,
                'input_date' => $respondent->input_date->toDateString(),
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
            ]);
        }

        ActivityLog::log(
            'create',
            'respondents',
            "Menambahkan data responden baru: {$respondent->name} ({$respondent->agency})."
        );

        return redirect()->route('admin.respondents.index')->with('success', "Data responden '{$respondent->name}' berhasil ditambahkan!");
    }

    public function show(Respondent $respondent)
    {
        $respondent->load(['assignment', 'officer', 'basts']);

        return response()->json([
            'success' => true,
            'data' => $respondent,
        ]);
    }

    public function update(Request $request, Respondent $respondent)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'district' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:25'],
            'agency' => ['required', 'string', 'max:255'],
            'purpose' => ['required', 'string', 'max:500'],
            'assignment_id' => ['nullable', 'exists:assignments,id'],
            'officer_id' => ['required', 'exists:users,id'],
            'input_date' => ['required', 'date'],
        ]);

        $respondent->update($validated);

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->update('respondents', $respondent->id, [
                'name' => $respondent->name,
                'address' => $respondent->address,
                'phone' => $respondent->phone ?? '',
                'agency' => $respondent->agency,
                'purpose' => $respondent->purpose,
                'assignment_id' => $respondent->assignment_id ?? '',
                'officer_id' => $respondent->officer_id,
                'input_date' => $respondent->input_date->toDateString(),
                'updated_at' => now()->toISOString(),
            ]);
        }

        ActivityLog::log(
            'update',
            'respondents',
            "Memperbarui data responden: {$respondent->name}."
        );

        return back()->with('success', "Data responden '{$respondent->name}' berhasil diperbarui!");
    }

    public function destroy(Respondent $respondent)
    {
        $name = $respondent->name;
        $id = $respondent->id;
        $respondent->delete();

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->delete('respondents', $id, true);
        }

        ActivityLog::log(
            'delete',
            'respondents',
            "Menghapus responden: {$name}."
        );

        return back()->with('success', "Data responden '{$name}' berhasil dihapus.");
    }
}

