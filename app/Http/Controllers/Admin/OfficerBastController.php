<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\OfficerBast;
use App\Models\User;
use App\Services\GoogleSheetService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OfficerBastController extends Controller
{
    protected GoogleSheetService $sheetService;

    public function __construct(GoogleSheetService $sheetService)
    {
        $this->sheetService = $sheetService;
    }

    public function index(Request $request)
    {
        $query = OfficerBast::with(['officer', 'assignment']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('bast_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('officer', fn($o) => $o->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('assignment', fn($a) => $a->where('title', 'like', "%{$search}%"));
            });
        }

        if ($assignmentId = $request->input('assignment_id')) {
            $query->where('assignment_id', $assignmentId);
        }

        if ($officerId = $request->input('officer_id')) {
            $query->where('officer_id', $officerId);
        }

        if ($bastType = $request->input('bast_type')) {
            if ($bastType === 'kantor_ke_petugas') {
                $query->where(function ($q) {
                    $q->where('bast_type', 'kantor_ke_petugas')
                      ->orWhereNull('bast_type');
                });
            } else {
                $query->where('bast_type', $bastType);
            }
        }

        $totalBasts = OfficerBast::count();
        $thisMonthBasts = OfficerBast::whereMonth('bast_date', now()->month)
                                     ->whereYear('bast_date', now()->year)
                                     ->count();

        $basts = $query->latest('bast_date')->paginate(10)->withQueryString();

        $assignments = Assignment::orderBy('title')->get();
        $officers = User::fieldStaff()->where('status', 'active')->orderBy('name')->get();

        return view('admin.basts.officers.index', compact(
            'basts',
            'totalBasts',
            'thisMonthBasts',
            'assignments',
            'officers'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'assignment_id' => ['required', 'exists:assignments,id'],
            'officer_id' => ['required', 'exists:users,id'],
            'bast_type' => ['nullable', Rule::in(['kantor_ke_petugas', 'petugas_ke_kantor'])],
            'bast_date' => ['required', 'date'],
            'bast_number' => ['nullable', 'string', 'max:100', 'unique:officer_basts,bast_number'],
            'description' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in(['draft', 'finalized', 'archived'])],
        ], [
            'assignment_id.required' => 'Kegiatan wajib dipilih.',
            'officer_id.required' => 'Petugas wajib dipilih.',
            'bast_date.required' => 'Tanggal BAST wajib diisi.',
            'description.required' => 'Rincian serah terima dokumen/perlengkapan wajib diisi.',
        ]);

        $validated['bast_type'] = $validated['bast_type'] ?? 'kantor_ke_petugas';

        if (empty($validated['bast_number'])) {
            $year = now()->format('Y');
            $romanMonths = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
            $month = $romanMonths[now()->month - 1];
            $count = OfficerBast::whereYear('created_at', now()->year)->count() + 1;
            $prefix = $validated['bast_type'] === 'kantor_ke_petugas' ? 'BAST-BLANKO' : 'BAST-TERISI';
            $validated['bast_number'] = sprintf('%s/%s/%s/%04d', $prefix, $year, $month, $count);
        }

        $bast = OfficerBast::create($validated);

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->insert('officer_basts', [
                'id' => $bast->id,
                'bast_number' => $bast->bast_number,
                'bast_type' => $bast->bast_type,
                'bast_date' => $bast->bast_date->toDateString(),
                'assignment_id' => $bast->assignment_id,
                'officer_id' => $bast->officer_id,
                'description' => $bast->description,
                'notes' => $bast->notes ?? '',
                'status' => $bast->status,
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
            ]);
        }

        $officer = User::find($validated['officer_id']);

        ActivityLog::log(
            'create',
            'basts',
            "Menerbitkan BAST Petugas nomor {$bast->bast_number} untuk {$officer->name}."
        );

        return redirect()->route('admin.basts.officers.index')
            ->with('success', "BAST Petugas '{$bast->bast_number}' berhasil dibuat dan siap diunduh!");
    }

    public function downloadPdf(OfficerBast $bast)
    {
        $bast->load(['officer', 'assignment']);

        $pdf = Pdf::loadView('pdf.bast_officer', compact('bast'))
                  ->setPaper('a4', 'portrait');

        $filename = 'BAST-Petugas-' . str_replace(['/', '\\'], '-', $bast->bast_number) . '.pdf';

        return $pdf->download($filename);
    }

    public function previewPdf(OfficerBast $bast)
    {
        $bast->load(['officer', 'assignment']);

        $pdf = Pdf::loadView('pdf.bast_officer', compact('bast'))
                  ->setPaper('a4', 'portrait');

        $filename = 'BAST-Petugas-' . str_replace(['/', '\\'], '-', $bast->bast_number) . '.pdf';

        return $pdf->stream($filename);
    }

    public function destroy(OfficerBast $bast)
    {
        $number = $bast->bast_number;
        $id = $bast->id;
        $bast->delete();

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->delete('officer_basts', $id, true);
        }

        ActivityLog::log(
            'delete',
            'basts',
            "Menghapus dokumen BAST Petugas nomor {$number}."
        );

        return back()->with('success', "Dokumen BAST '{$number}' berhasil dihapus.");
    }
}

