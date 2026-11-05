<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\OfficerBast;
use App\Models\OfficerReport;
use App\Models\Respondent;
use App\Models\RespondentBast;
use App\Models\TrainingEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Filter Parameters
        $selectedAssignmentId = $request->input('assignment_id');
        $selectedDistrict = $request->input('district');
        $selectedOfficerId = $request->input('officer_id');
        $progressFilter = $request->input('progress_status', 'all'); // all, slow, on_track, completed
        $reportFilter = $request->input('report_status', 'all'); // all, submitted, not_submitted

        // 2. Base Query for Respondents according to active filters
        $respondentQuery = Respondent::query();
        if ($selectedAssignmentId) {
            $respondentQuery->where('assignment_id', $selectedAssignmentId);
        }
        if ($selectedDistrict) {
            $respondentQuery->where('district', $selectedDistrict);
        }
        if ($selectedOfficerId) {
            $respondentQuery->where('officer_id', $selectedOfficerId);
        }

        $bastTargetCount = (clone $respondentQuery)->count();
        $bastCompletedCount = (clone $respondentQuery)->has('basts')->count();
        $bastPendingCount = max(0, $bastTargetCount - $bastCompletedCount);
        $bastPercentage = $bastTargetCount > 0 ? round(($bastCompletedCount / $bastTargetCount) * 100, 1) : 0;

        // Global Statistics
        $totalOfficers = User::where('role', 'petugas')->count();
        $totalRespondentsAll = Respondent::count();
        $totalAssignments = Assignment::count();
        $totalBastRespondents = RespondentBast::count();
        $totalBastOfficers = OfficerBast::count();
        $totalBasts = $totalBastRespondents + $totalBastOfficers;

        // 3. Progres per Kecamatan (Breakdown Kecamatan)
        $districtStatsRaw = Respondent::select('district', DB::raw('count(*) as total'))
            ->when($selectedAssignmentId, fn($q) => $q->where('assignment_id', $selectedAssignmentId))
            ->whereNotNull('district')
            ->where('district', '!=', '')
            ->groupBy('district')
            ->get();

        $districtCompletedRaw = Respondent::has('basts')
            ->select('district', DB::raw('count(*) as completed'))
            ->when($selectedAssignmentId, fn($q) => $q->where('assignment_id', $selectedAssignmentId))
            ->whereNotNull('district')
            ->where('district', '!=', '')
            ->groupBy('district')
            ->pluck('completed', 'district');

        $districtSummaries = [];
        foreach ($districtStatsRaw as $dStat) {
            $dName = $dStat->district;
            $dTotal = $dStat->total;
            $dCompleted = $districtCompletedRaw[$dName] ?? 0;
            $dPending = max(0, $dTotal - $dCompleted);
            $dPercent = $dTotal > 0 ? round(($dCompleted / $dTotal) * 100, 1) : 0;

            $districtSummaries[] = [
                'name' => $dName,
                'total' => $dTotal,
                'completed' => $dCompleted,
                'pending' => $dPending,
                'percentage' => $dPercent,
            ];
        }

        // Urutkan kecamatan berdasarkan persentase capaian
        usort($districtSummaries, fn($a, $b) => $b['percentage'] <=> $a['percentage']);

        // 4. Monitoring Kinerja Petugas & Identifikasi Lambat / Perlu Follow-Up
        $officersQuery = User::where('role', 'petugas')
            ->where('status', 'active')
            ->withCount([
                'respondents' => function ($q) use ($selectedAssignmentId, $selectedDistrict) {
                    if ($selectedAssignmentId) $q->where('assignment_id', $selectedAssignmentId);
                    if ($selectedDistrict) $q->where('district', $selectedDistrict);
                },
                'respondents as completed_basts_count' => function ($q) use ($selectedAssignmentId, $selectedDistrict) {
                    $q->has('basts');
                    if ($selectedAssignmentId) $q->where('assignment_id', $selectedAssignmentId);
                    if ($selectedDistrict) $q->where('district', $selectedDistrict);
                },
                'reports' => function ($q) use ($selectedAssignmentId) {
                    if ($selectedAssignmentId) $q->where('assignment_id', $selectedAssignmentId);
                }
            ]);

        if ($selectedOfficerId) {
            $officersQuery->where('id', $selectedOfficerId);
        }

        $allOfficersList = $officersQuery->orderBy('name')->get();

        $officerMonitoringList = [];
        $slowOfficersCount = 0;
        $completedOfficersCount = 0;
        $onTrackOfficersCount = 0;

        foreach ($allOfficersList as $off) {
            $target = $off->respondents_count;
            $completed = $off->completed_basts_count;
            $pending = max(0, $target - $completed);
            $pct = $target > 0 ? round(($completed / $target) * 100, 1) : 0;
            $hasReport = $off->reports_count > 0;

            // Kategori status progres
            if ($target === 0) {
                $category = 'no_target';
                $badgeClass = 'bg-secondary';
                $statusText = 'Belum Ada Target';
            } elseif ($pct >= 100) {
                $category = 'completed';
                $badgeClass = 'bg-success';
                $statusText = 'Selesai (100%)';
                $completedOfficersCount++;
            } elseif ($pct >= 50) {
                $category = 'on_track';
                $badgeClass = 'bg-warning text-dark';
                $statusText = 'On Track (' . $pct . '%)';
                $onTrackOfficersCount++;
            } else {
                $category = 'slow';
                $badgeClass = 'bg-danger';
                $statusText = 'Lambat / Perlu Follow-Up (' . $pct . '%)';
                $slowOfficersCount++;
            }

            // Generate link WhatsApp reminder otomatis
            $cleanPhone = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $off->phone ?? ''));
            $waMessage = urlencode(
                "Halo Rekan {$off->name},\n\n" .
                "Mengingatkan terkait progres penugasan SIMPLE Anda saat ini:\n" .
                "• Target Responden: {$target}\n" .
                "• BAST Selesai: {$completed} ({$pct}%)\n" .
                "• Sisa Belum BAST: {$pending}\n" .
                "• Status Laporan Kegiatan: " . ($hasReport ? 'SUDAH DISERAHKAN (' . $off->reports_count . ')' : 'BELUM DISERAHKAN') . "\n\n" .
                "Mohon untuk segera menyelesaikan kunjungan responden & penginputan BAST serta melengkapi Laporan Perjalanan Dinas. Terima kasih."
            );
            $waUrl = !empty($cleanPhone) ? "https://wa.me/{$cleanPhone}?text={$waMessage}" : null;

            // Dapatkan kecamatan dominan petugas ini jika ada
            $dominantDistrict = Respondent::where('officer_id', $off->id)
                ->whereNotNull('district')
                ->value('district') ?? 'Umum';

            $officerMonitoringList[] = (object) [
                'id' => $off->id,
                'name' => $off->name,
                'email' => $off->email,
                'phone' => $off->phone,
                'dominant_district' => $dominantDistrict,
                'target' => $target,
                'completed' => $completed,
                'pending' => $pending,
                'percentage' => $pct,
                'category' => $category,
                'badge_class' => $badgeClass,
                'status_text' => $statusText,
                'has_report' => $hasReport,
                'reports_count' => $off->reports_count,
                'wa_url' => $waUrl,
            ];
        }

        // Terapkan filter progres & filter laporan pada list monitoring
        $filteredOfficersList = collect($officerMonitoringList);
        if ($progressFilter === 'slow') {
            $filteredOfficersList = $filteredOfficersList->where('category', 'slow');
        } elseif ($progressFilter === 'on_track') {
            $filteredOfficersList = $filteredOfficersList->where('category', 'on_track');
        } elseif ($progressFilter === 'completed') {
            $filteredOfficersList = $filteredOfficersList->where('category', 'completed');
        }

        if ($reportFilter === 'submitted') {
            $filteredOfficersList = $filteredOfficersList->where('has_report', true);
        } elseif ($reportFilter === 'not_submitted') {
            $filteredOfficersList = $filteredOfficersList->where('has_report', false);
        }

        // Default: urutkan yang lambat / persentase terendah di bagian paling atas
        $filteredOfficersList = $filteredOfficersList->sortBy([
            ['percentage', 'asc'],
            ['pending', 'desc']
        ])->values();

        // 5. Monitoring Pengumpulan Laporan Petugas
        $totalOfficersCount = $allOfficersList->count();
        $officersWithReportCount = $allOfficersList->where('reports_count', '>', 0)->count();
        $officersWithoutReportCount = max(0, $totalOfficersCount - $officersWithReportCount);
        $reportSubmissionRate = $totalOfficersCount > 0 ? round(($officersWithReportCount / $totalOfficersCount) * 100, 1) : 0;

        // Daftar petugas yang belum membuat laporan
        $officersWithoutReports = collect($officerMonitoringList)->where('has_report', false)->values();

        // Report statuses
        $reportsPending = OfficerReport::whereIn('status', ['submitted', 'reviewing'])->count();
        $reportsApproved = OfficerReport::where('status', 'approved')->count();
        $reportsRevision = OfficerReport::where('status', 'revision_needed')->count();

        // Data dropdown filter
        $assignmentsList = Assignment::orderBy('title')->get();
        $districtsList = Respondent::whereNotNull('district')->where('district', '!=', '')->distinct()->orderBy('district')->pluck('district');
        $allOfficersDropdown = User::where('role', 'petugas')->where('status', 'active')->orderBy('name')->get();

        // Data aktivitas & laporan terkini
        $recentActivities = ActivityLog::with('user')->latest()->take(6)->get();
        $recentReports = OfficerReport::with(['user', 'assignment'])->latest()->take(5)->get();
        $recentAssignments = Assignment::withCount('officers', 'respondents')->latest()->take(4)->get();

        // Data Grafik Tren Bulanan
        $monthlyLabels = [];
        $monthlyRespondents = [];
        $monthlyBasts = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyLabels[] = $month->format('M Y');
            $monthlyRespondents[] = Respondent::whereYear('input_date', $month->year)->whereMonth('input_date', $month->month)->count();
            $monthlyBasts[] = RespondentBast::whereYear('bast_date', $month->year)->whereMonth('bast_date', $month->month)->count();
        }

        return view('admin.dashboard', compact(
            'totalOfficers',
            'totalRespondentsAll',
            'totalAssignments',
            'totalBasts',
            'totalBastRespondents',
            'totalBastOfficers',
            'bastTargetCount',
            'bastCompletedCount',
            'bastPendingCount',
            'bastPercentage',
            'districtSummaries',
            'filteredOfficersList',
            'slowOfficersCount',
            'completedOfficersCount',
            'onTrackOfficersCount',
            'totalOfficersCount',
            'officersWithReportCount',
            'officersWithoutReportCount',
            'reportSubmissionRate',
            'officersWithoutReports',
            'reportsPending',
            'reportsApproved',
            'reportsRevision',
            'assignmentsList',
            'districtsList',
            'allOfficersDropdown',
            'selectedAssignmentId',
            'selectedDistrict',
            'selectedOfficerId',
            'progressFilter',
            'reportFilter',
            'recentActivities',
            'recentReports',
            'recentAssignments',
            'monthlyLabels',
            'monthlyRespondents',
            'monthlyBasts'
        ));
    }
}
