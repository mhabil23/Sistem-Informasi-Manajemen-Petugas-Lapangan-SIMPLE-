<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\AssignmentOfficer;
use App\Models\Respondent;
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
        $userId = Auth::id();

        $query = Respondent::with(['assignment', 'basts'])
            ->where('officer_id', $userId);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('family_head_name', 'like', "%{$search}%")
                  ->orWhere('informant_name', 'like', "%{$search}%")
                  ->orWhere('respondent_status', 'like', "%{$search}%")
                  ->orWhere('agency', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($assignmentId = $request->input('assignment_id')) {
            $query->where('assignment_id', $assignmentId);
        }

        $totalMyRespondents = Respondent::where('officer_id', $userId)->count();
        $thisMonthCount = Respondent::where('officer_id', $userId)
            ->whereMonth('input_date', now()->month)
            ->whereYear('input_date', now()->year)
            ->count();
        $withBastCount = Respondent::where('officer_id', $userId)->has('basts')->count();

        $respondents = $query->latest('input_date')->paginate(10)->withQueryString();

        // Get activities assigned to this officer
        $myAssignmentIds = AssignmentOfficer::where('user_id', $userId)->pluck('assignment_id');
        $myAssignments = Assignment::whereIn('id', $myAssignmentIds)->get();

        // Target Kuota Responden from assigned activities
        $assignedTargetSum = (int) $myAssignments->sum('target_respondents');
        $targetQuota = $assignedTargetSum > 0 ? $assignedTargetSum : 120;
        $remainingTarget = max(0, $targetQuota - $totalMyRespondents);

        return view('petugas.respondents.index', compact(
            'respondents',
            'totalMyRespondents',
            'thisMonthCount',
            'withBastCount',
            'myAssignments',
            'targetQuota',
            'remainingTarget'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'family_head_name' => ['nullable', 'string', 'max:255'],
            'informant_name' => ['nullable', 'string', 'max:255'],
            'respondent_status' => ['nullable', 'string', 'max:100'],
            'address' => ['required', 'string'],
            'district' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:25'],
            'agency' => ['required', 'string', 'max:255'],
            'purpose' => ['required', 'string', 'max:500'],
            'assignment_id' => ['nullable', 'exists:assignments,id'],
            'input_date' => ['required', 'date'],
        ], [
            'address.required' => 'Alamat responden wajib diisi.',
            'agency.required' => 'Instansi / lembaga wajib diisi.',
            'purpose.required' => 'Tujuan kegiatan wajib diisi.',
            'input_date.required' => 'Tanggal input data wajib diisi.',
        ]);

        $headName = trim($request->input('family_head_name') ?: $request->input('name', ''));
        if (empty($headName)) {
            return back()->withErrors(['name' => 'Nama Kepala Keluarga (KRT) wajib diisi.'])->withInput();
        }

        $validated['name'] = $headName;
        $validated['family_head_name'] = $headName;
        $validated['informant_name'] = trim($request->input('informant_name') ?: $headName);
        $validated['respondent_status'] = trim($request->input('respondent_status') ?: 'Kepala Keluarga (KRT)');
        $validated['officer_id'] = Auth::id();

        $respondent = Respondent::create($validated);

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->insert('respondents', [
                'id' => $respondent->id,
                'name' => $respondent->name,
                'family_head_name' => $respondent->family_head_name ?? $respondent->name,
                'informant_name' => $respondent->informant_name ?? $respondent->name,
                'respondent_status' => $respondent->respondent_status ?? '',
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
            "Petugas " . Auth::user()->name . " menginput data responden baru: {$respondent->name} (Pemberi Info: {$respondent->informant_name})."
        );

        return redirect()->route('petugas.respondents.index')->with('success', "Data responden '{$respondent->name}' berhasil disimpan!");
    }

    public function update(Request $request, Respondent $respondent)
    {
        if ($respondent->officer_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki hak akses untuk mengubah data responden ini.');
        }

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'family_head_name' => ['nullable', 'string', 'max:255'],
            'informant_name' => ['nullable', 'string', 'max:255'],
            'respondent_status' => ['nullable', 'string', 'max:100'],
            'address' => ['required', 'string'],
            'district' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:25'],
            'agency' => ['required', 'string', 'max:255'],
            'purpose' => ['required', 'string', 'max:500'],
            'assignment_id' => ['nullable', 'exists:assignments,id'],
            'input_date' => ['required', 'date'],
        ]);

        $headName = trim($request->input('family_head_name') ?: $request->input('name', ''));
        if (empty($headName)) {
            return back()->withErrors(['name' => 'Nama Kepala Keluarga (KRT) wajib diisi.'])->withInput();
        }

        $validated['name'] = $headName;
        $validated['family_head_name'] = $headName;
        $validated['informant_name'] = trim($request->input('informant_name') ?: $headName);
        $validated['respondent_status'] = trim($request->input('respondent_status') ?: 'Kepala Keluarga (KRT)');

        $respondent->update($validated);

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->update('respondents', $respondent->id, [
                'name' => $respondent->name,
                'family_head_name' => $respondent->family_head_name ?? $respondent->name,
                'informant_name' => $respondent->informant_name ?? $respondent->name,
                'respondent_status' => $respondent->respondent_status ?? '',
                'address' => $respondent->address,
                'phone' => $respondent->phone ?? '',
                'agency' => $respondent->agency,
                'purpose' => $respondent->purpose,
                'assignment_id' => $respondent->assignment_id ?? '',
                'input_date' => $respondent->input_date->toDateString(),
                'updated_at' => now()->toISOString(),
            ]);
        }

        ActivityLog::log(
            'update',
            'respondents',
            "Petugas " . Auth::user()->name . " memperbarui data responden: {$respondent->name}."
        );

        return back()->with('success', "Data responden '{$respondent->name}' berhasil diperbarui!");
    }
}

