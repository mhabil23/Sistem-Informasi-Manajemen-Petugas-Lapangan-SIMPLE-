<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AdminNotification;
use App\Models\Assignment;
use App\Models\OfficerReport;
use App\Models\User;
use App\Services\GoogleSheetService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $query = OfficerReport::with(['user', 'assignment', 'reviewer', 'attachments']);

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('report_number', 'like', "%{$search}%")
                  ->orWhere('activity_title', 'like', "%{$search}%")
                  ->orWhere('work_description', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        // Status Filter
        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        // Officer Filter
        if ($officerId = $request->input('officer_id')) {
            $query->where('user_id', $officerId);
        }

        // Assignment Filter
        if ($assignmentId = $request->input('assignment_id')) {
            $query->where('assignment_id', $assignmentId);
        }

        // Date Filter
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('activity_date', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('activity_date', '<=', $dateTo);
        }

        // Counters
        $totalReports = OfficerReport::count();
        $draftCount = OfficerReport::where('status', 'draft')->count();
        $submittedCount = OfficerReport::where('status', 'submitted')->count();
        $reviewingCount = OfficerReport::where('status', 'reviewing')->count();
        $revisionNeededCount = OfficerReport::where('status', 'revision_needed')->count();
        $approvedCount = OfficerReport::where('status', 'approved')->count();
        $unreadNotifCount = AdminNotification::unread()->count();

        // Deep link auto-open when Admin clicks a notification / Dashboard quick action
        if ($notifId = $request->input('notif_id')) {
            AdminNotification::where('id', (int) $notifId)
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);
        }

        $autoOpenReport = null;
        $autoOpenMode = null;
        if ($editId = $request->input('edit')) {
            $autoOpenReport = OfficerReport::with(['user', 'assignment', 'reviewer', 'attachments'])->find((int) $editId);
            $autoOpenMode = 'edit';
            if (Auth::user()?->isAdmin()) {
                AdminNotification::markReadForReport((int) $editId);
            }
        } elseif ($inspectId = $request->input('inspect')) {
            $autoOpenReport = OfficerReport::with(['user', 'assignment', 'reviewer', 'attachments'])->find((int) $inspectId);
            $autoOpenMode = 'view';
            if (Auth::user()?->isAdmin()) {
                AdminNotification::markReadForReport((int) $inspectId);
            }
        }

        $reports = $query->latest('activity_date')->latest('id')->paginate(10)->withQueryString();

        $officers = User::fieldStaff()->where('status', 'active')->orderBy('name')->get();
        $assignments = Assignment::orderBy('title')->get();
        $basisSettings = OfficerReport::getMasterBasisSettings();

        return view('admin.reports.index', compact(
            'reports',
            'totalReports',
            'draftCount',
            'submittedCount',
            'reviewingCount',
            'revisionNeededCount',
            'approvedCount',
            'unreadNotifCount',
            'autoOpenReport',
            'autoOpenMode',
            'officers',
            'assignments',
            'basisSettings'
        ));
    }

    public function updateBasisSettings(Request $request)
    {
        $validated = $request->validate([
            'basis_dipa' => ['required', 'string', 'max:1000'],
            'st_number' => ['required', 'string', 'max:150'],
            'st_date' => ['required', 'date'],
            'basis_st' => ['nullable', 'string', 'max:1000'],
        ], [
            'basis_dipa.required' => '1. DIPA Satuan Kerja wajib diisi.',
            'st_number.required' => '2a. Nomor Surat Tugas wajib diisi.',
            'st_date.required' => '2b. Tanggal Surat Tugas wajib diisi.',
        ]);

        OfficerReport::saveMasterBasisSettings($validated);

        ActivityLog::log(
            'update',
            'reports',
            "Admin memperbarui pengaturan default B. Dasar Pelaksanaan (ST: {$validated['st_number']})."
        );

        return back()->with('success', 'Pengaturan default B. Dasar Pelaksanaan (DIPA & Surat Tugas) berhasil disimpan dan otomatis diterapkan pada pengisian laporan petugas!');
    }

    public function show(OfficerReport $report)
    {
        $report->load(['user', 'assignment', 'reviewer', 'attachments']);

        return response()->json([
            'success' => true,
            'report' => $report,
        ]);
    }

    public function downloadPdf(OfficerReport $report)
    {
        $report->load(['user', 'assignment', 'attachments']);

        $pdf = Pdf::loadView('pdf.officer_report', compact('report'))->setPaper('a4', 'portrait');
        $filename = 'Laporan-Perjalanan-Dinas-' . str_replace(['/', '\\'], '-', $report->report_number ?: ('LAP-' . $report->id)) . '.pdf';

        return $pdf->download($filename);
    }

    public function previewPdf(OfficerReport $report)
    {
        $report->load(['user', 'assignment', 'attachments']);

        $pdf = Pdf::loadView('pdf.officer_report', compact('report'))->setPaper('a4', 'portrait');
        $filename = 'Laporan-Perjalanan-Dinas-' . str_replace(['/', '\\'], '-', $report->report_number ?: ('LAP-' . $report->id)) . '.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Admin mengedit seluruh inputan laporan perjadin milik petugas (dan opsional langsung ACC).
     */
    public function update(Request $request, OfficerReport $report)
    {
        $validated = $request->validate([
            'officer_name' => ['required', 'string', 'max:255'],
            'id_type' => ['nullable', 'string', 'in:NIK,NIP'],
            'officer_nip' => ['nullable', 'string', 'max:100'],
            'officer_rank' => ['nullable', 'string', 'max:100'],
            'officer_position' => ['nullable', 'string', 'max:150'],
            'basis_dipa' => ['nullable', 'string', 'max:1000'],
            'st_number' => ['nullable', 'string', 'max:150'],
            'st_date' => ['nullable', 'date'],
            'basis_st' => ['nullable', 'string', 'max:1000'],
            'assignment_id' => ['nullable', 'exists:assignments,id'],
            'activity_title' => ['required', 'string', 'max:255'],
            'activity_location' => ['nullable', 'string', 'max:255'],
            'destination_purpose' => ['nullable', 'string', 'max:500'],
            'duration_text' => ['nullable', 'string', 'max:150'],
            'activity_date' => ['required', 'date'],
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
            'delete_attachment_ids' => ['nullable', 'array'],
            'delete_attachment_ids.*' => ['integer'],
            'after_action' => ['nullable', Rule::in(['save', 'approve'])],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ], [
            'officer_name.required' => 'Nama pelaksana wajib diisi.',
            'activity_title.required' => 'Judul kegiatan wajib diisi.',
            'activity_date.required' => 'Tanggal pelaksanaan wajib diisi.',
            'work_description.required' => 'Uraian hasil perjalanan dinas wajib diisi.',
        ]);

        $stNumber = trim((string) ($validated['st_number'] ?? '')) ?: $report->st_number;
        $stDate = !empty($validated['st_date']) ? $validated['st_date'] : ($report->st_date ? $report->st_date->format('Y-m-d') : null);
        $basisSt = trim((string) ($validated['basis_st'] ?? ''));
        if ($basisSt === '' && !empty($stNumber)) {
            $basisSt = OfficerReport::formatBasisSt($stNumber, $stDate);
        }

        $expenseItems = OfficerReport::buildExpenseItems(
            (array) $request->input('expense_descriptions', []),
            (array) $request->input('expense_amounts', [])
        );

        $report->update([
            'officer_name' => $validated['officer_name'],
            'id_type' => $validated['id_type'] ?? ($report->id_type ?? 'NIK'),
            'officer_nip' => $validated['officer_nip'] ?? $report->officer_nip,
            'officer_rank' => $validated['officer_rank'] ?? $report->officer_rank,
            'officer_position' => $validated['officer_position'] ?? $report->officer_position,
            'basis_dipa' => trim((string) ($validated['basis_dipa'] ?? '')) ?: $report->basis_dipa,
            'st_number' => $stNumber,
            'st_date' => $stDate,
            'basis_st' => $basisSt ?: $report->basis_st,
            'assignment_id' => !empty($validated['assignment_id']) ? (int) $validated['assignment_id'] : null,
            'activity_title' => $validated['activity_title'],
            'activity_location' => $validated['activity_location'] ?? $report->activity_location,
            'destination_purpose' => $validated['destination_purpose'] ?? $report->destination_purpose,
            'duration_text' => !empty($validated['duration_text']) ? $validated['duration_text'] : $report->duration_text,
            'activity_date' => $validated['activity_date'],
            'work_description' => $validated['work_description'],
            'obstacles' => $validated['obstacles'] ?? $report->obstacles,
            'solutions' => $validated['solutions'] ?? $report->solutions,
            'recommendations' => $validated['recommendations'] ?? ($report->recommendations ?? '-'),
            'signing_city' => !empty($validated['signing_city']) ? $validated['signing_city'] : ($report->signing_city ?? 'Lasusua'),
            'signing_date' => !empty($validated['signing_date']) ? $validated['signing_date'] : $report->resolved_signing_date,
            'include_vehicle_statement' => $request->boolean('include_vehicle_statement'),
            'expense_items' => !empty($expenseItems) ? $expenseItems : $report->expense_items,
            'ppk_name' => !empty($validated['ppk_name']) ? $validated['ppk_name'] : ($report->ppk_name ?? 'Hidayatullah, SE'),
            'ppk_nip' => !empty($validated['ppk_nip']) ? $validated['ppk_nip'] : ($report->ppk_nip ?? '198510292011011009'),
        ]);

        $report->deleteAttachmentsByIds((array) $request->input('delete_attachment_ids', []));

        if (($validated['after_action'] ?? 'save') === 'approve') {
            $this->applyApproval($report, $validated['admin_notes'] ?? null, true);

            return back()->with('success', "Inputan laporan '{$report->activity_title}' berhasil diperbarui dan langsung di-ACC! Petugas kini dapat mengunduh PDF.");
        }

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->update('officer_reports', $report->id, [
                'activity_date' => $report->activity_date->toDateString(),
                'activity_title' => $report->activity_title,
                'work_description' => $report->work_description,
                'status' => $report->status,
                'updated_at' => now()->toISOString(),
            ]);
        }

        $officerName = $report->user ? $report->user->name : ($report->officer_name ?? '-');
        ActivityLog::log(
            'update',
            'reports',
            "Admin mengedit inputan laporan '{$report->activity_title}' milik {$officerName}."
        );

        return back()->with('success', "Inputan laporan '{$report->activity_title}' berhasil diperbarui oleh Admin!");
    }

    /**
     * Quick 1-click ACC / Persetujuan Laporan Perjadin oleh Admin.
     */
    public function approve(Request $request, OfficerReport $report)
    {
        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->applyApproval($report, $validated['admin_notes'] ?? null);

        return back()->with('success', "Laporan '{$report->activity_title}' telah di-ACC (Disetujui). Petugas sekarang dapat mengunduh PDF resmi.");
    }

    public function review(Request $request, OfficerReport $report)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['reviewing', 'revision_needed', 'approved'])],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
            'basis_dipa' => ['nullable', 'string', 'max:1000'],
            'st_number' => ['nullable', 'string', 'max:150'],
            'st_date' => ['nullable', 'date'],
            'basis_st' => ['nullable', 'string', 'max:1000'],
        ], [
            'status.required' => 'Status verifikasi laporan wajib dipilih.',
        ]);

        $stNumber = array_key_exists('st_number', $validated) ? trim((string) $validated['st_number']) : $report->st_number;
        $stDate = array_key_exists('st_date', $validated) ? $validated['st_date'] : $report->st_date;
        $basisSt = array_key_exists('basis_st', $validated) ? trim((string) $validated['basis_st']) : $report->basis_st;
        if (empty($basisSt) && !empty($stNumber)) {
            $basisSt = OfficerReport::formatBasisSt($stNumber, $stDate ? substr((string) $stDate, 0, 10) : null);
        }

        $report->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? null,
            'basis_dipa' => array_key_exists('basis_dipa', $validated) && trim((string) $validated['basis_dipa']) !== ''
                ? trim((string) $validated['basis_dipa'])
                : $report->basis_dipa,
            'st_number' => $stNumber ?: $report->st_number,
            'st_date' => $stDate ?: $report->st_date,
            'basis_st' => $basisSt ?: $report->basis_st,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->update('officer_reports', $report->id, [
                'status' => $report->status,
                'admin_notes' => $report->admin_notes ?? '',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
            ]);
        }

        $statusText = match ($validated['status']) {
            'approved' => 'menyetujui (ACC)',
            'revision_needed' => 'mengembalikan dengan catatan revisi',
            'reviewing' => 'memindahkan ke tahap pemeriksaan',
            default => 'memperbarui status',
        };

        if (in_array($validated['status'], ['approved', 'revision_needed'], true)) {
            AdminNotification::markReadForReport($report->id);
            $report->notifyOfficerDecision(Auth::id());
        }

        ActivityLog::log(
            'review',
            'reports',
            "Admin {$statusText} laporan '{$report->activity_title}' milik " . ($report->user ? $report->user->name : '-') . '.'
        );

        return back()->with('success', "Status laporan berhasil diperbarui menjadi {$report->status_label}!");
    }

    public function destroy(OfficerReport $report)
    {
        $title = $report->activity_title;
        $id = $report->id;
        AdminNotification::where('report_id', $id)->delete();
        $report->delete();

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->delete('officer_reports', $id, true);
        }

        ActivityLog::log(
            'delete',
            'reports',
            "Menghapus laporan kegiatan: {$title}."
        );

        return back()->with('success', "Laporan '{$title}' berhasil dihapus.");
    }

    private function applyApproval(OfficerReport $report, ?string $adminNotes = null, bool $alsoEdited = false): void
    {
        $previousStatus = $report->status;
        $finalNotes = $adminNotes !== null && trim($adminNotes) !== ''
            ? trim($adminNotes)
            : ($previousStatus === 'revision_needed' ? null : $report->admin_notes);

        $report->update([
            'status' => 'approved',
            'admin_notes' => $finalNotes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        AdminNotification::markReadForReport($report->id);
        $report->notifyOfficerDecision(Auth::id());

        if ($this->sheetService->isConfigured()) {
            $this->sheetService->update('officer_reports', $report->id, [
                'status' => $report->status,
                'admin_notes' => $report->admin_notes ?? '',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
            ]);
        }

        $officerName = $report->user ? $report->user->name : ($report->officer_name ?? '-');
        $actionLabel = $alsoEdited ? 'mengedit dan menyetujui (ACC)' : 'menyetujui (ACC)';
        ActivityLog::log(
            'review',
            'reports',
            "Admin {$actionLabel} laporan '{$report->activity_title}' milik {$officerName}."
        );
    }
}

