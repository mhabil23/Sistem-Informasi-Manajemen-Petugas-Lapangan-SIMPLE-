<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\Respondent;
use App\Models\RespondentBast;
use App\Models\User;
use App\Services\GoogleSheetService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RespondentBastController extends Controller
{
    protected GoogleSheetService $sheetService;

    public function __construct(GoogleSheetService $sheetService)
    {
        $this->sheetService = $sheetService;
    }

    public function index(Request $request)
    {
        $query = RespondentBast::with(['respondent', 'officer', 'assignment']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('bast_number', 'like', "%{$search}%")
                  ->orWhereHas('respondent', fn($r) => $r->where('name', 'like', "%{$search}%")->orWhere('agency', 'like', "%{$search}%"))
                  ->orWhereHas('officer', fn($o) => $o->where('name', 'like', "%{$search}%"));
            });
        }

        if ($assignmentId = $request->input('assignment_id')) {
            $query->where('assignment_id', $assignmentId);
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('bast_date', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('bast_date', '<=', $dateTo);
        }

        $totalBasts = RespondentBast::count();
        $thisMonthBasts = RespondentBast::whereMonth('bast_date', now()->month)
                                        ->whereYear('bast_date', now()->year)
                                        ->count();

        $basts = $query->latest('bast_date')->paginate(10)->withQueryString();

        $respondents = Respondent::with('officer')->orderBy('name')->get();
        $assignments = Assignment::orderBy('title')->get();

        return view('admin.basts.respondents.index', compact(
            'basts',
            'totalBasts',
            'thisMonthBasts',
            'respondents',
            'assignments'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'respondent_id' => ['required', 'exists:respondents,id'],
            'respondent_number' => ['nullable', 'integer', 'min:1'],
            'handover_category' => ['nullable', Rule::in(['ATK', 'KUES'])],
            'bast_date' => ['required', 'date'],
            'bast_number' => ['nullable', 'string', 'max:100', 'unique:respondent_basts,bast_number'],
            'handover_items' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in(['draft', 'finalized', 'archived'])],
        ], [
            'respondent_id.required' => 'Responden wajib dipilih.',
            'bast_date.required' => 'Tanggal BAST wajib diisi.',
            'handover_items.required' => 'Rincian berkas yang diserahterimakan wajib diisi.',
        ]);

        $respondent = Respondent::findOrFail($validated['respondent_id']);

        // Auto-generate BAST number if empty
        if (empty($validated['bast_number'])) {
            $year = now()->format('Y');
            $romanMonths = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
            $month = $romanMonths[now()->month - 1];
            $count = RespondentBast::whereYear('created_at', now()->year)->count() + 1;
            $validated['bast_number'] = sprintf('BAST-RESP/%s/%s/%04d', $year, $month, $count);
        }

        $validated['handover_category'] = strtoupper((string) ($validated['handover_category'] ?? 'KUES'));
        $validated['assignment_id'] = $respondent->assignment_id;
        $validated['officer_id'] = $respondent->officer_id ?? Auth::id();

        $bast = RespondentBast::create($validated);

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->insert('respondent_basts', [
                'id' => $bast->id,
                'bast_number' => $bast->bast_number,
                'bast_date' => $bast->bast_date->toDateString(),
                'respondent_id' => $bast->respondent_id,
                'assignment_id' => $bast->assignment_id ?? '',
                'officer_id' => $bast->officer_id,
                'handover_items' => $bast->handover_items,
                'notes' => $bast->notes ?? '',
                'status' => $bast->status,
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
            ]);
        }

        ActivityLog::log(
            'create',
            'basts',
            "Menerbitkan BAST Responden nomor {$bast->bast_number} untuk {$respondent->name}."
        );

        return redirect()->route('admin.basts.respondents.index')
            ->with('success', "Dokumen BAST '{$bast->bast_number}' berhasil diterbitkan dan siap diunduh!");
    }

    public function downloadPdf(RespondentBast $bast)
    {
        $bast->load(['respondent', 'officer', 'assignment']);

        $pdf = Pdf::loadView('pdf.bast_respondent', compact('bast'))
                  ->setPaper('a4', 'portrait');

        $filename = 'BAST-Responden-' . str_replace(['/', '\\'], '-', $bast->bast_number) . '.pdf';

        return $pdf->download($filename);
    }

    public function previewPdf(RespondentBast $bast)
    {
        $bast->load(['respondent', 'officer', 'assignment']);

        $pdf = Pdf::loadView('pdf.bast_respondent', compact('bast'))
                  ->setPaper('a4', 'portrait');

        $filename = 'BAST-Responden-' . str_replace(['/', '\\'], '-', $bast->bast_number) . '.pdf';

        return $pdf->stream($filename);
    }

    public function destroy(RespondentBast $bast)
    {
        $number = $bast->bast_number;
        $id = $bast->id;
        $bast->delete();

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->delete('respondent_basts', $id, true);
        }

        ActivityLog::log(
            'delete',
            'basts',
            "Menghapus dokumen BAST Responden nomor {$number}."
        );

        return back()->with('success', "Dokumen BAST '{$number}' berhasil dihapus.");
    }
}

