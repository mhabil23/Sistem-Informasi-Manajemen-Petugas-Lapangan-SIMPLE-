<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\AssignmentOfficer;
use App\Models\OfficerBast;
use App\Models\Respondent;
use App\Models\RespondentBast;
use App\Services\GoogleSheetService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BastController extends Controller
{
    protected GoogleSheetService $sheetService;

    public function __construct(GoogleSheetService $sheetService)
    {
        $this->sheetService = $sheetService;
    }

    // ==========================================
    // BAST RESPONDEN (PORTAL PETUGAS)
    // ==========================================
    public function respondents(Request $request)
    {
        $userId = Auth::id();

        $basts = RespondentBast::with(['respondent', 'assignment'])
            ->where('officer_id', $userId)
            ->latest('bast_date')
            ->paginate(10);

        $myRespondents = Respondent::where('officer_id', $userId)->orderBy('name')->get();

        return view('petugas.basts.respondents', compact('basts', 'myRespondents'));
    }

    public function storeRespondent(Request $request)
    {
        $validated = $request->validate([
            'respondent_id' => ['required', 'exists:respondents,id'],
            'bast_date' => ['required', 'date'],
            'handover_items' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'respondent_id.required' => 'Responden wajib dipilih.',
            'bast_date.required' => 'Tanggal BAST wajib diisi.',
            'handover_items.required' => 'Rincian berkas wajib diisi.',
        ]);

        $respondent = Respondent::findOrFail($validated['respondent_id']);

        $year = now()->format('Y');
        $romanMonths = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
        $month = $romanMonths[now()->month - 1];
        $count = RespondentBast::whereYear('created_at', now()->year)->count() + 1;
        $bastNumber = sprintf('BAST-RESP/%s/%s/%04d', $year, $month, $count);

        $bast = RespondentBast::create([
            'bast_number' => $bastNumber,
            'bast_date' => $validated['bast_date'],
            'respondent_id' => $respondent->id,
            'assignment_id' => $respondent->assignment_id,
            'officer_id' => Auth::id(),
            'handover_items' => $validated['handover_items'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'finalized',
        ]);

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
            "Petugas " . Auth::user()->name . " menerbitkan BAST Responden {$bast->bast_number} untuk {$respondent->name}."
        );

        return redirect()->route('petugas.basts.respondents.index')
            ->with('success', "Dokumen BAST '{$bast->bast_number}' berhasil dibuat dan siap diunduh!");
    }

    public function downloadRespondentPdf(RespondentBast $bast)
    {
        if ($bast->officer_id !== Auth::id()) {
            abort(403);
        }

        $bast->load(['respondent', 'officer', 'assignment']);
        $pdf = Pdf::loadView('pdf.bast_respondent', compact('bast'))->setPaper('a4', 'portrait');

        return $pdf->download('BAST-Responden-' . str_replace(['/', '\\'], '-', $bast->bast_number) . '.pdf');
    }

    public function previewRespondentPdf(RespondentBast $bast)
    {
        if ($bast->officer_id !== Auth::id()) {
            abort(403);
        }

        $bast->load(['respondent', 'officer', 'assignment']);
        $pdf = Pdf::loadView('pdf.bast_respondent', compact('bast'))->setPaper('a4', 'portrait');

        return $pdf->stream('BAST-Responden-' . str_replace(['/', '\\'], '-', $bast->bast_number) . '.pdf');
    }

    // ==========================================
    // BAST PETUGAS (PORTAL PETUGAS)
    // ==========================================
    public function officers(Request $request)
    {
        $userId = Auth::id();

        $basts = OfficerBast::with(['assignment'])
            ->where('officer_id', $userId)
            ->latest('bast_date')
            ->paginate(10);

        $myAssignmentIds = AssignmentOfficer::where('user_id', $userId)->pluck('assignment_id');
        $myAssignments = Assignment::whereIn('id', $myAssignmentIds)->get();

        return view('petugas.basts.officers', compact('basts', 'myAssignments'));
    }

    public function storeOfficer(Request $request)
    {
        $validated = $request->validate([
            'assignment_id' => ['required', 'exists:assignments,id'],
            'bast_date' => ['required', 'date'],
            'description' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'assignment_id.required' => 'Kegiatan wajib dipilih.',
            'bast_date.required' => 'Tanggal BAST wajib diisi.',
            'description.required' => 'Keterangan penyerahan berkas tugas wajib diisi.',
        ]);

        $year = now()->format('Y');
        $romanMonths = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
        $month = $romanMonths[now()->month - 1];
        $count = OfficerBast::whereYear('created_at', now()->year)->count() + 1;
        $bastNumber = sprintf('BAST-PETUGAS/%s/%s/%04d', $year, $month, $count);

        $bast = OfficerBast::create([
            'bast_number' => $bastNumber,
            'bast_date' => $validated['bast_date'],
            'assignment_id' => $validated['assignment_id'],
            'officer_id' => Auth::id(),
            'description' => $validated['description'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'finalized',
        ]);

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->insert('officer_basts', [
                'id' => $bast->id,
                'bast_number' => $bast->bast_number,
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

        ActivityLog::log(
            'create',
            'basts',
            "Petugas " . Auth::user()->name . " menerbitkan BAST Petugas {$bast->bast_number}."
        );

        return redirect()->route('petugas.basts.officers.index')
            ->with('success', "BAST Petugas '{$bast->bast_number}' berhasil dibuat!");
    }

    public function downloadOfficerPdf(OfficerBast $bast)
    {
        if ($bast->officer_id !== Auth::id()) {
            abort(403);
        }

        $bast->load(['officer', 'assignment']);
        $pdf = Pdf::loadView('pdf.bast_officer', compact('bast'))->setPaper('a4', 'portrait');

        return $pdf->download('BAST-Petugas-' . str_replace(['/', '\\'], '-', $bast->bast_number) . '.pdf');
    }

    public function previewOfficerPdf(OfficerBast $bast)
    {
        if ($bast->officer_id !== Auth::id()) {
            abort(403);
        }

        $bast->load(['officer', 'assignment']);
        $pdf = Pdf::loadView('pdf.bast_officer', compact('bast'))->setPaper('a4', 'portrait');

        return $pdf->stream('BAST-Petugas-' . str_replace(['/', '\\'], '-', $bast->bast_number) . '.pdf');
    }
}

