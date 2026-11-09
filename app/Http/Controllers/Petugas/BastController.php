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
use Illuminate\Support\Facades\Storage;
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

        // Load all respondents allocated to this officer with their BASTs
        $myRespondents = Respondent::with('basts')
            ->where('officer_id', $userId)
            ->orderBy('name')
            ->get();

        $completedCount = $myRespondents->filter(fn($r) => $r->basts->isNotEmpty())->count();
        $pendingCount = $myRespondents->filter(fn($r) => $r->basts->isEmpty())->count();

        return view('petugas.basts.respondents', compact('basts', 'myRespondents', 'completedCount', 'pendingCount'));
    }

    public function storeRespondent(Request $request)
    {
        $validated = $request->validate([
            'respondent_id' => ['required', 'exists:respondents,id'],
            'bast_date' => ['required', 'date'],
            'recipient_type' => ['required', Rule::in(['krt', 'representative'])],
            'recipient_name' => ['nullable', 'string', 'max:255'],
            'recipient_relation' => ['nullable', 'string', 'max:100'],
            'handover_items' => ['required', 'string'],
            'signature_data' => ['nullable', 'string'],
            'officer_signature_data' => ['nullable', 'string'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'respondent_id.required' => 'Responden wajib dipilih.',
            'bast_date.required' => 'Tanggal BAST wajib diisi.',
            'recipient_type.required' => 'Pihak penerima wajib dipilih (KRT atau Wakil).',
            'handover_items.required' => 'Rincian berkas wajib diisi.',
        ]);

        $respondent = Respondent::findOrFail($validated['respondent_id']);

        // Jika penerima adalah selain KRT (wakil), pastikan nama dan hubungan terisi
        if ($validated['recipient_type'] === 'representative') {
            if (empty(trim($validated['recipient_name'] ?? ''))) {
                return back()->withInput()->with('error', 'Nama penerima selain KRT (Wakil/Anggota Keluarga) wajib diisi.');
            }
            if (empty(trim($validated['recipient_relation'] ?? ''))) {
                return back()->withInput()->with('error', 'Status hubungan penerima dengan Kepala Keluarga (misal: Pasangan/Anak) wajib diisi.');
            }
        }

        // Simpan Tanda Tangan Digital Penerima / Responden jika ada
        $signaturePath = null;
        if (!empty($request->input('signature_data')) && str_starts_with($request->input('signature_data'), 'data:image')) {
            $data = $request->input('signature_data');
            $data = substr($data, strpos($data, ',') + 1);
            $data = base64_decode($data);
            $fileName = 'signatures/' . uniqid('sign_resp_') . '.png';
            Storage::disk('public')->put($fileName, $data);
            $signaturePath = $fileName;
        }

        // Simpan Tanda Tangan Petugas jika ada
        $officerSignaturePath = null;
        if (!empty($request->input('officer_signature_data')) && str_starts_with($request->input('officer_signature_data'), 'data:image')) {
            $data = $request->input('officer_signature_data');
            $data = substr($data, strpos($data, ',') + 1);
            $data = base64_decode($data);
            $fileName = 'signatures/' . uniqid('sign_off_') . '.png';
            Storage::disk('public')->put($fileName, $data);
            $officerSignaturePath = $fileName;
        }

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
            'recipient_type' => $validated['recipient_type'],
            'recipient_name' => $validated['recipient_type'] === 'representative' ? trim($validated['recipient_name']) : $respondent->name,
            'recipient_relation' => $validated['recipient_type'] === 'representative' ? trim($validated['recipient_relation']) : 'Kepala Rumah Tangga (KRT)',
            'signature_path' => $signaturePath,
            'officer_signature_path' => $officerSignaturePath,
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
        $currentType = $request->query('type'); // 'kantor_ke_petugas', 'petugas_ke_kantor', or null

        $query = OfficerBast::with(['assignment'])
            ->where('officer_id', $userId);

        if ($currentType === 'kantor_ke_petugas') {
            $query->where(function ($q) {
                $q->where('bast_type', 'kantor_ke_petugas')
                  ->orWhereNull('bast_type');
            });
        } elseif ($currentType === 'petugas_ke_kantor') {
            $query->where('bast_type', 'petugas_ke_kantor');
        }

        $basts = $query->latest('bast_date')->paginate(10)->withQueryString();

        $countBlanko = OfficerBast::where('officer_id', $userId)
            ->where(function ($q) {
                $q->where('bast_type', 'kantor_ke_petugas')
                  ->orWhereNull('bast_type');
            })->count();

        $countTerisi = OfficerBast::where('officer_id', $userId)
            ->where('bast_type', 'petugas_ke_kantor')
            ->count();

        $myAssignmentIds = AssignmentOfficer::where('user_id', $userId)->pluck('assignment_id');
        $myAssignments = Assignment::whereIn('id', $myAssignmentIds)->get();

        return view('petugas.basts.officers', compact('basts', 'myAssignments', 'currentType', 'countBlanko', 'countTerisi'));
    }

    public function storeOfficer(Request $request)
    {
        $validated = $request->validate([
            'assignment_id' => ['required', 'exists:assignments,id'],
            'bast_type' => ['required', Rule::in(['kantor_ke_petugas', 'petugas_ke_kantor'])],
            'bast_date' => ['required', 'date'],
            'description' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'assignment_id.required' => 'Kegiatan wajib dipilih.',
            'bast_type.required' => 'Jenis BAST wajib dipilih.',
            'bast_date.required' => 'Tanggal BAST wajib diisi.',
            'description.required' => 'Rincian serah terima dokumen/perlengkapan wajib diisi.',
        ]);

        $year = now()->format('Y');
        $romanMonths = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
        $month = $romanMonths[now()->month - 1];
        $count = OfficerBast::whereYear('created_at', now()->year)->count() + 1;

        $prefix = $validated['bast_type'] === 'kantor_ke_petugas' ? 'BAST-BLANKO' : 'BAST-TERISI';
        $bastNumber = sprintf('%s/%s/%s/%04d', $prefix, $year, $month, $count);

        $bast = OfficerBast::create([
            'bast_number' => $bastNumber,
            'bast_type' => $validated['bast_type'],
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

        $typeText = $bast->bast_type === 'kantor_ke_petugas' ? 'BAST Blanko & Perlengkapan' : 'BAST Dokumen Terisi';

        ActivityLog::log(
            'create',
            'basts',
            "Petugas " . Auth::user()->name . " menerbitkan {$typeText} {$bast->bast_number}."
        );

        return redirect()->route('petugas.basts.officers.index', ['type' => $bast->bast_type])
            ->with('success', "{$typeText} '{$bast->bast_number}' berhasil dibuat!");
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

