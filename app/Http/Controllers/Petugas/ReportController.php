<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\AssignmentOfficer;
use App\Models\OfficerReport;
use App\Models\ReportAttachment;
use App\Services\GoogleSheetService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    protected GoogleSheetService $sheetService;

    public function __construct(GoogleSheetService $sheetService)
    {
        $this->sheetService = $sheetService;
    }

    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = OfficerReport::with(['assignment', 'reviewer', 'attachments'])
            ->where('user_id', $userId);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('report_number', 'like', "%{$search}%")
                  ->orWhere('activity_title', 'like', "%{$search}%")
                  ->orWhere('work_description', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        $totalReports = OfficerReport::where('user_id', $userId)->count();
        $draftCount = OfficerReport::where('user_id', $userId)->where('status', 'draft')->count();
        $submittedCount = OfficerReport::where('user_id', $userId)->whereIn('status', ['submitted', 'reviewing'])->count();
        $revisionCount = OfficerReport::where('user_id', $userId)->where('status', 'revision_needed')->count();
        $approvedCount = OfficerReport::where('user_id', $userId)->where('status', 'approved')->count();

        $reports = $query->latest('activity_date')->latest('id')->paginate(10)->withQueryString();

        $myAssignmentIds = AssignmentOfficer::where('user_id', $userId)->pluck('assignment_id');
        $myAssignments = Assignment::whereIn('id', $myAssignmentIds)->get();

        return view('petugas.reports.index', compact(
            'reports',
            'totalReports',
            'draftCount',
            'submittedCount',
            'revisionCount',
            'approvedCount',
            'myAssignments'
        ));
    }

    public function store(Request $request)
    {
        $validated = $this->validateReportRequest($request);
        $user = Auth::user();

        $signaturePath = $this->resolveSignaturePath($request);
        $this->syncUserIdentityIfMissing($validated);

        $officerName = !empty($validated['officer_name']) ? $validated['officer_name'] : $user->name;
        $officerNik = !empty($validated['officer_nip']) ? $validated['officer_nip'] : ($user->nik ?: '-');
        $officerRank = !empty($validated['officer_rank']) ? $validated['officer_rank'] : ($user->rank ?: '-');
        $officerPosition = !empty($validated['officer_position']) ? $validated['officer_position'] : ($user->position ?: 'Mitra BPS Kabupaten Kolaka Utara');

        $expenseItems = $this->buildExpenseItems($request);
        $basisSt = $validated['basis_st'] ?? null;
        if (empty($basisSt) && !empty($validated['st_number'])) {
            $stDateStr = !empty($validated['st_date'])
                ? \Carbon\Carbon::parse($validated['st_date'])->translatedFormat('d F Y')
                : \Carbon\Carbon::parse($validated['activity_date'])->translatedFormat('d F Y');
            $basisSt = "Surat Tugas Kepala Badan Pusat Statistik Kabupaten Kolaka Utara Nomor {$validated['st_number']} Tanggal {$stDateStr}.";
        }

        $year = now()->format('Y');
        $count = OfficerReport::whereYear('created_at', now()->year)->count() + 1;
        $reportNumber = sprintf('LAP/%s/%04d', $year, $count);

        $report = OfficerReport::create([
            'report_number' => $reportNumber,
            'user_id' => $user->id,
            'assignment_id' => $validated['assignment_id'] ?? null,
            'officer_name' => $officerName,
            'officer_nip' => $officerNik,
            'id_type' => $validated['id_type'] ?? 'NIK',
            'officer_rank' => $officerRank,
            'officer_position' => $officerPosition,
            'basis_dipa' => $validated['basis_dipa'] ?? null,
            'basis_st' => $basisSt,
            'st_number' => $validated['st_number'] ?? null,
            'st_date' => $validated['st_date'] ?? null,
            'destination_purpose' => $validated['destination_purpose'] ?? null,
            'duration_text' => !empty($validated['duration_text']) ? $validated['duration_text'] : 'Selama 1 (satu) Hari',
            'activity_date' => $validated['activity_date'],
            'activity_title' => $validated['activity_title'],
            'activity_location' => $validated['activity_location'] ?? null,
            'work_description' => $validated['work_description'],
            'obstacles' => $validated['obstacles'] ?? null,
            'solutions' => $validated['solutions'] ?? null,
            'recommendations' => $validated['recommendations'] ?? '-',
            'signing_city' => !empty($validated['signing_city']) ? $validated['signing_city'] : 'Lasusua',
            'signing_date' => !empty($validated['signing_date']) ? $validated['signing_date'] : $validated['activity_date'],
            'signature_path' => $signaturePath,
            'include_vehicle_statement' => $request->boolean('include_vehicle_statement'),
            'expense_items' => $expenseItems,
            'ppk_name' => !empty($validated['ppk_name']) ? $validated['ppk_name'] : 'Hidayatullah, SE',
            'ppk_nip' => !empty($validated['ppk_nip']) ? $validated['ppk_nip'] : '198510292011011009',
            'status' => $validated['action_type'],
        ]);

        // Handle File Attachments
        $this->savePhotoAttachments($request, $report);

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->insert('officer_reports', [
                'id' => $report->id,
                'report_number' => $report->report_number,
                'user_id' => $report->user_id,
                'assignment_id' => $report->assignment_id ?? '',
                'activity_date' => $report->activity_date->toDateString(),
                'activity_title' => $report->activity_title,
                'work_description' => $report->work_description,
                'status' => $report->status,
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
            ]);
        }

        $msg = $report->status === 'submitted'
            ? "Laporan Perjadin & Daftar Pengeluaran Riil '{$report->activity_title}' berhasil diajukan dan siap dicetak menjadi 1 PDF!"
            : "Laporan Perjadin & Daftar Pengeluaran Riil '{$report->activity_title}' berhasil disimpan sebagai draft.";

        ActivityLog::log(
            'create',
            'reports',
            "Petugas membuat laporan perjadin '{$report->activity_title}' (Status: {$report->status})."
        );

        return redirect()->route('petugas.reports.index')
            ->with('success', $msg)
            ->with('created_report_id', $report->id);
    }

    public function show(OfficerReport $report)
    {
        abort_if($report->user_id !== Auth::id(), 403);
        $report->load(['assignment', 'reviewer', 'attachments']);

        return response()->json([
            'success' => true,
            'report' => $report,
        ]);
    }

    public function downloadPdf(OfficerReport $report)
    {
        abort_if($report->user_id !== Auth::id(), 403);
        $report->load(['user', 'assignment', 'attachments']);

        $pdf = Pdf::loadView('pdf.officer_report', compact('report'))->setPaper('a4', 'portrait');
        $filename = 'Laporan-Perjalanan-Dinas-' . str_replace(['/', '\\'], '-', $report->report_number ?: ('LAP-' . $report->id)) . '.pdf';

        return $pdf->download($filename);
    }

    public function previewPdf(OfficerReport $report)
    {
        abort_if($report->user_id !== Auth::id(), 403);
        $report->load(['user', 'assignment', 'attachments']);

        $pdf = Pdf::loadView('pdf.officer_report', compact('report'))->setPaper('a4', 'portrait');
        $filename = 'Laporan-Perjalanan-Dinas-' . str_replace(['/', '\\'], '-', $report->report_number ?: ('LAP-' . $report->id)) . '.pdf';

        return $pdf->stream($filename);
    }

    public function update(Request $request, OfficerReport $report)
    {
        abort_if($report->user_id !== Auth::id(), 403);

        if (!in_array($report->status, ['draft', 'revision_needed'])) {
            return back()->with('error', 'Laporan yang sedang diperiksa atau telah disetujui tidak dapat diubah.');
        }

        $validated = $this->validateReportRequest($request);
        $signaturePath = $this->resolveSignaturePath($request, $report->signature_path);
        $this->syncUserIdentityIfMissing($validated);

        $expenseItems = $this->buildExpenseItems($request);
        $basisSt = $validated['basis_st'] ?? $report->basis_st;
        if (empty($basisSt) && !empty($validated['st_number'])) {
            $stDateStr = !empty($validated['st_date'])
                ? \Carbon\Carbon::parse($validated['st_date'])->translatedFormat('d F Y')
                : \Carbon\Carbon::parse($validated['activity_date'])->translatedFormat('d F Y');
            $basisSt = "Surat Tugas Kepala Badan Pusat Statistik Kabupaten Kolaka Utara Nomor {$validated['st_number']} Tanggal {$stDateStr}.";
        }

        $report->update([
            'activity_date' => $validated['activity_date'],
            'activity_title' => $validated['activity_title'],
            'activity_location' => $validated['activity_location'] ?? $report->activity_location,
            'assignment_id' => $validated['assignment_id'] ?? null,
            'officer_name' => !empty($validated['officer_name']) ? $validated['officer_name'] : $report->officer_name,
            'officer_nip' => !empty($validated['officer_nip']) ? $validated['officer_nip'] : $report->officer_nip,
            'id_type' => $validated['id_type'] ?? ($report->id_type ?? 'NIK'),
            'officer_rank' => !empty($validated['officer_rank']) ? $validated['officer_rank'] : $report->officer_rank,
            'officer_position' => !empty($validated['officer_position']) ? $validated['officer_position'] : $report->officer_position,
            'basis_dipa' => $validated['basis_dipa'] ?? $report->basis_dipa,
            'basis_st' => $basisSt,
            'st_number' => $validated['st_number'] ?? $report->st_number,
            'st_date' => $validated['st_date'] ?? $report->st_date,
            'destination_purpose' => $validated['destination_purpose'] ?? $report->destination_purpose,
            'duration_text' => !empty($validated['duration_text']) ? $validated['duration_text'] : $report->duration_text,
            'work_description' => $validated['work_description'],
            'obstacles' => $validated['obstacles'] ?? $report->obstacles,
            'solutions' => $validated['solutions'] ?? $report->solutions,
            'recommendations' => $validated['recommendations'] ?? ($report->recommendations ?? '-'),
            'signing_city' => !empty($validated['signing_city']) ? $validated['signing_city'] : $report->signing_city,
            'signing_date' => !empty($validated['signing_date']) ? $validated['signing_date'] : $report->signing_date,
            'signature_path' => $signaturePath,
            'include_vehicle_statement' => $request->boolean('include_vehicle_statement'),
            'expense_items' => !empty($expenseItems) ? $expenseItems : $report->expense_items,
            'ppk_name' => !empty($validated['ppk_name']) ? $validated['ppk_name'] : ($report->ppk_name ?? 'Hidayatullah, SE'),
            'ppk_nip' => !empty($validated['ppk_nip']) ? $validated['ppk_nip'] : ($report->ppk_nip ?? '198510292011011009'),
            'status' => $validated['action_type'],
        ]);

        $this->savePhotoAttachments($request, $report);

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->update('officer_reports', $report->id, [
                'activity_date' => $report->activity_date->toDateString(),
                'activity_title' => $report->activity_title,
                'work_description' => $report->work_description,
                'status' => $report->status,
                'updated_at' => now()->toISOString(),
            ]);
        }

        $msg = $report->status === 'submitted'
            ? "Laporan '{$report->activity_title}' berhasil diajukan kembali ke Admin!"
            : "Laporan '{$report->activity_title}' berhasil diperbarui.";

        ActivityLog::log(
            'update',
            'reports',
            "Petugas memperbarui laporan '{$report->activity_title}'."
        );

        return redirect()->route('petugas.reports.index')
            ->with('success', $msg)
            ->with('created_report_id', $report->id);
    }

    public function destroy(OfficerReport $report)
    {
        abort_if($report->user_id !== Auth::id(), 403);

        if ($report->status !== 'draft') {
            return back()->with('error', 'Hanya laporan berstatus draft yang dapat dihapus.');
        }

        $title = $report->activity_title;
        $id = $report->id;
        $report->delete();

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->delete('officer_reports', $id, true);
        }

        return back()->with('success', "Draft laporan '{$title}' berhasil dihapus.");
    }

    private function validateReportRequest(Request $request): array
    {
        return $request->validate([
            'activity_date' => ['required', 'date'],
            'activity_title' => ['required', 'string', 'max:255'],
            'activity_location' => ['nullable', 'string', 'max:255'],
            'assignment_id' => ['nullable', 'exists:assignments,id'],
            'officer_name' => ['nullable', 'string', 'max:255'],
            'officer_nip' => ['nullable', 'string', 'max:100'],
            'id_type' => ['nullable', 'string', 'in:NIK,NIP'],
            'officer_rank' => ['nullable', 'string', 'max:100'],
            'officer_position' => ['nullable', 'string', 'max:150'],
            'basis_dipa' => ['nullable', 'string', 'max:500'],
            'basis_st' => ['nullable', 'string', 'max:500'],
            'st_number' => ['nullable', 'string', 'max:150'],
            'st_date' => ['nullable', 'date'],
            'destination_purpose' => ['nullable', 'string', 'max:500'],
            'duration_text' => ['nullable', 'string', 'max:150'],
            'work_description' => ['required', 'string'],
            'obstacles' => ['nullable', 'string'],
            'solutions' => ['nullable', 'string'],
            'recommendations' => ['nullable', 'string'],
            'signing_city' => ['nullable', 'string', 'max:100'],
            'signing_date' => ['nullable', 'date'],
            'include_vehicle_statement' => ['nullable'],
            'expense_descriptions.*' => ['nullable', 'string', 'max:500'],
            'expense_amounts.*' => ['nullable', 'numeric', 'min:0'],
            'ppk_name' => ['nullable', 'string', 'max:150'],
            'ppk_nip' => ['nullable', 'string', 'max:50'],
            'signature_file' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:3072'],
            'signature_data' => ['nullable', 'string'],
            'action_type' => ['required', Rule::in(['draft', 'submitted'])],
            'photos.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'activity_date.required' => 'Tanggal kegiatan wajib diisi.',
            'activity_title.required' => 'Judul kegiatan wajib diisi.',
            'work_description.required' => 'Uraian pekerjaan / laporan wajib diisi.',
        ]);
    }

    private function resolveSignaturePath(Request $request, ?string $existingPath = null): ?string
    {
        if ($request->hasFile('signature_file')) {
            return $request->file('signature_file')->store('signatures', 'public');
        }

        $sigData = $request->input('signature_data');
        if (!empty($sigData) && str_starts_with($sigData, 'data:image')) {
            $data = substr($sigData, strpos($sigData, ',') + 1);
            $decoded = base64_decode($data);
            if ($decoded !== false) {
                $fileName = 'signatures/' . uniqid('sign_') . '.png';
                Storage::disk('public')->put($fileName, $decoded);
                return $fileName;
            }
        }

        return $existingPath;
    }

    private function buildExpenseItems(Request $request): array
    {
        $descriptions = $request->input('expense_descriptions', []);
        $amounts = $request->input('expense_amounts', []);
        $items = [];

        foreach ($descriptions as $idx => $desc) {
            $cleanDesc = trim((string) $desc);
            $amount = isset($amounts[$idx]) ? (int) preg_replace('/[^0-9]/', '', (string) $amounts[$idx]) : 0;
            if ($cleanDesc !== '' || $amount > 0) {
                $items[] = [
                    'description' => $cleanDesc ?: 'Biaya transport perjalanan dinas',
                    'amount' => $amount,
                ];
            }
        }

        return $items;
    }

    private function syncUserIdentityIfMissing(array $validated): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $updates = [];
        if (empty($user->nik) && !empty($validated['officer_nip']) && $validated['officer_nip'] !== '-') {
            $updates['nik'] = $validated['officer_nip'];
        }
        if (empty($user->rank) && !empty($validated['officer_rank'])) {
            $updates['rank'] = $validated['officer_rank'];
        }
        if (empty($user->position) && !empty($validated['officer_position'])) {
            $updates['position'] = $validated['officer_position'];
        }

        if (!empty($updates)) {
            $user->update($updates);
        }
    }

    private function savePhotoAttachments(Request $request, OfficerReport $report): void
    {
        if (!$request->hasFile('photos')) {
            return;
        }

        foreach ($request->file('photos') as $file) {
            $path = $file->store('reports', 'public');
            ReportAttachment::create([
                'report_id' => $report->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'caption' => 'Dokumentasi ' . $report->activity_title,
            ]);
        }
    }
}

