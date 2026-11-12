<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
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
        $submittedCount = OfficerReport::where('status', 'submitted')->count();
        $reviewingCount = OfficerReport::where('status', 'reviewing')->count();
        $revisionNeededCount = OfficerReport::where('status', 'revision_needed')->count();
        $approvedCount = OfficerReport::where('status', 'approved')->count();

        $reports = $query->latest('activity_date')->latest('id')->paginate(10)->withQueryString();

        $officers = User::where('role', 'petugas')->where('status', 'active')->orderBy('name')->get();
        $assignments = Assignment::orderBy('title')->get();
        $basisSettings = OfficerReport::getMasterBasisSettings();

        return view('admin.reports.index', compact(
            'reports',
            'totalReports',
            'submittedCount',
            'reviewingCount',
            'revisionNeededCount',
            'approvedCount',
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
            'approved' => 'menyetujui',
            'revision_needed' => 'mengembalikan dengan catatan revisi',
            'reviewing' => 'memindahkan ke tahap pemeriksaan',
            default => 'memperbarui status',
        };

        ActivityLog::log(
            'review',
            'reports',
            "Admin {$statusText} laporan '{$report->activity_title}' milik {$report->user->name}."
        );

        return back()->with('success', "Status laporan berhasil diperbarui menjadi {$report->status_label}!");
    }

    public function destroy(OfficerReport $report)
    {
        $title = $report->activity_title;
        $id = $report->id;
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
}

