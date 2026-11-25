<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentOfficer;
use App\Models\Respondent;
use App\Models\RespondentBast;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FieldProgressController extends Controller
{
    /**
     * Menampilkan Halaman Monitoring Progres Pendataan Lapangan.
     */
    public function index(Request $request)
    {
        $selectedAssignmentId = $request->input('assignment_id');
        $selectedDistrict = $request->input('district');
        $selectedRole = $request->input('officer_role');
        $statusFilter = $request->input('status');
        $search = $request->input('search');

        // 1. Data Dropdown Pilihan
        $assignments = Assignment::orderBy('title')->get();
        $districts = Respondent::DISTRICTS;

        // 2. Base Query Responden untuk Penghitungan Sampel Lapangan
        $baseRespondentQuery = Respondent::query();
        if ($selectedAssignmentId) {
            $baseRespondentQuery->where('assignment_id', $selectedAssignmentId);
        }
        if ($selectedDistrict) {
            $baseRespondentQuery->where('district', $selectedDistrict);
        }

        // 3. Statistik KPI Ringkasan Lapangan
        $totalFieldSamples = (clone $baseRespondentQuery)->count();
        $completedFieldSamples = (clone $baseRespondentQuery)->where(function ($q) {
            $q->where('progress_status', 'selesai')->orHas('basts');
        })->count();
        $inProgressFieldSamples = (clone $baseRespondentQuery)->where('progress_status', 'sedang_didata')->doesntHave('basts')->count();
        $pendingFieldSamples = max(0, $totalFieldSamples - $completedFieldSamples - $inProgressFieldSamples);
        $totalPercentage = $totalFieldSamples > 0 ? min(100, round(($completedFieldSamples / $totalFieldSamples) * 100, 1)) : 0;

        // 4. Data Petugas Lapangan & Perhitungan Capaian per Petugas
        $officersQuery = User::fieldStaff()
            ->where('status', 'active')
            ->with(['assignedTasks' => function ($q) use ($selectedAssignmentId) {
                if ($selectedAssignmentId) {
                    $q->where('assignments.id', $selectedAssignmentId);
                }
            }])
            ->withCount([
                'respondents' => function ($q) use ($selectedAssignmentId, $selectedDistrict) {
                    if ($selectedAssignmentId) $q->where('assignment_id', $selectedAssignmentId);
                    if ($selectedDistrict) $q->where('district', $selectedDistrict);
                },
                'respondents as completed_count' => function ($q) use ($selectedAssignmentId, $selectedDistrict) {
                    $q->where(function ($sq) {
                        $sq->where('progress_status', 'selesai')->orHas('basts');
                    });
                    if ($selectedAssignmentId) $q->where('assignment_id', $selectedAssignmentId);
                    if ($selectedDistrict) $q->where('district', $selectedDistrict);
                },
                'respondents as in_progress_count' => function ($q) use ($selectedAssignmentId, $selectedDistrict) {
                    $q->where('progress_status', 'sedang_didata')->doesntHave('basts');
                    if ($selectedAssignmentId) $q->where('assignment_id', $selectedAssignmentId);
                    if ($selectedDistrict) $q->where('district', $selectedDistrict);
                },
                'respondents as pending_count' => function ($q) use ($selectedAssignmentId, $selectedDistrict) {
                    $q->where(function ($sq) {
                        $sq->where('progress_status', 'belum_didata')->orWhereNull('progress_status');
                    })->doesntHave('basts');
                    if ($selectedAssignmentId) $q->where('assignment_id', $selectedAssignmentId);
                    if ($selectedDistrict) $q->where('district', $selectedDistrict);
                },
            ]);

        // Filter nama/kontak petugas
        if ($search) {
            $officersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nip_nik', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $allOfficers = $officersQuery->orderBy('name')->get();

        // 5. Transformasi Data Capaian per Petugas
        $officerProgressList = [];
        $totalOfficersCount = 0;
        $completedOfficersCount = 0;
        $inProgressOfficersCount = 0;
        $notStartedOfficersCount = 0;
        $slowOfficersCount = 0;

        foreach ($allOfficers as $officer) {
            // Filter Peran Petugas (PCL, PPL, PML) jika dipilih
            $officerRole = $officer->field_role ?? ($officer->role === 'pegawai' ? 'PML' : 'PCL');
            if ($selectedRole && strtoupper($selectedRole) !== strtoupper($officerRole)) {
                continue;
            }

            // Hitung Target Responden dari Penugasan Resmi (AssignmentOfficer) atau Jumlah Responden
            $assignedTargetSum = (int) AssignmentOfficer::where('user_id', $officer->id)
                ->when($selectedAssignmentId, fn($q) => $q->where('assignment_id', $selectedAssignmentId))
                ->join('assignments', 'assignments.id', '=', 'assignment_officers.assignment_id')
                ->sum('assignments.target_respondents');

            $target = $assignedTargetSum > 0 ? $assignedTargetSum : max($officer->respondents_count, 0);
            $completed = (int) $officer->completed_count;
            $inProgress = (int) $officer->in_progress_count;
            $pending = max(0, $target - $completed);
            $percentage = $target > 0 ? min(100, round(($completed / $target) * 100, 1)) : ($completed > 0 ? 100 : 0);

            // Tentukan status performa
            if ($percentage >= 100) {
                $statusCategory = 'completed';
                $completedOfficersCount++;
            } elseif ($percentage > 0) {
                $statusCategory = 'in_progress';
                $inProgressOfficersCount++;
                if ($percentage < 50) {
                    $slowOfficersCount++;
                }
            } else {
                $statusCategory = 'not_started';
                $notStartedOfficersCount++;
                $slowOfficersCount++;
            }

            // Filter status jika dipilih di form filter
            if ($statusFilter) {
                if ($statusFilter === 'completed' && $statusCategory !== 'completed') continue;
                if ($statusFilter === 'in_progress' && $statusCategory !== 'in_progress') continue;
                if ($statusFilter === 'not_started' && $statusCategory !== 'not_started') continue;
                if ($statusFilter === 'slow' && $percentage >= 50) continue;
            }

            // Wilayah kecamatan dominan petugas
            $officerDistricts = Respondent::where('officer_id', $officer->id)
                ->when($selectedAssignmentId, fn($q) => $q->where('assignment_id', $selectedAssignmentId))
                ->whereNotNull('district')
                ->distinct()
                ->pluck('district')
                ->take(3)
                ->toArray();

            // Link WhatsApp pengingat
            $cleanPhone = preg_replace('/[^0-9]/', '', (string)$officer->phone);
            if (str_starts_with($cleanPhone, '0')) {
                $cleanPhone = '62' . substr($cleanPhone, 1);
            }
            $waMessage = rawurlencode("Halo Rekan {$officer->name},\n\nMengingatkan terkait progres pendataan lapangan SIMPEL BPS Kolaka Utara:\n• Target Responden: {$target}\n• Realisasi BAST: {$completed} ({$percentage}%)\n• Sisa Belum Selesai: {$pending}\n\nMohon untuk mempercepat kunjungan responden dan penerbitan formulir tanda terima BAST resmi. Terima kasih.");
            $waUrl = !empty($cleanPhone) ? "https://wa.me/{$cleanPhone}?text={$waMessage}" : null;

            $officerProgressList[] = [
                'officer' => $officer,
                'role' => $officerRole,
                'target' => $target,
                'completed' => $completed,
                'in_progress' => $inProgress,
                'pending' => $pending,
                'percentage' => $percentage,
                'status_category' => $statusCategory,
                'districts' => $officerDistricts,
                'wa_url' => $waUrl,
                'phone' => $officer->phone,
            ];

            $totalOfficersCount++;
        }

        // Urutkan petugas berdasarkan persentase capaian tertinggi (atau bisa diubah sesuai kebutuhan)
        usort($officerProgressList, fn($a, $b) => $b['percentage'] <=> $a['percentage']);

        // 6. Rekapitulasi Progres per 15 Kecamatan
        $districtSummaries = [];
        foreach ($districts as $districtName) {
            $dQuery = Respondent::where('district', $districtName);
            if ($selectedAssignmentId) {
                $dQuery->where('assignment_id', $selectedAssignmentId);
            }

            $dTotal = (clone $dQuery)->count();
            $dCompleted = (clone $dQuery)->where(function ($q) {
                $q->where('progress_status', 'selesai')->orHas('basts');
            })->count();
            $dPending = max(0, $dTotal - $dCompleted);
            $dPercent = $dTotal > 0 ? min(100, round(($dCompleted / $dTotal) * 100, 1)) : 0;

            // Jumlah petugas di kecamatan ini
            $dOfficersCount = (clone $dQuery)->whereNotNull('officer_id')->distinct('officer_id')->count('officer_id');

            $districtSummaries[] = [
                'name' => $districtName,
                'total' => $dTotal,
                'completed' => $dCompleted,
                'pending' => $dPending,
                'percentage' => $dPercent,
                'officers_count' => $dOfficersCount,
            ];
        }

        // Urutkan kecamatan berdasarkan persentase tertinggi
        usort($districtSummaries, fn($a, $b) => $b['percentage'] <=> $a['percentage']);

        // 7. Rekapitulasi Progres per Tim Lapangan (PML & PCL)
        $pmlOfficers = User::where('role', 'pegawai')
            ->orWhereHas('assignmentOfficers', fn($q) => $q->where('activity_role', 'PML'))
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $teamProgressList = [];
        foreach ($pmlOfficers as $pml) {
            $pmlAssignmentIds = AssignmentOfficer::where('user_id', $pml->id)
                ->when($selectedAssignmentId, fn($q) => $q->where('assignment_id', $selectedAssignmentId))
                ->pluck('assignment_id')
                ->toArray();

            $memberQuery = User::where('id', '!=', $pml->id)
                ->whereIn('role', ['petugas', 'pegawai']);

            if (!empty($pmlAssignmentIds)) {
                $memberQuery->whereHas('assignmentOfficers', fn($q) => $q->whereIn('assignment_id', $pmlAssignmentIds));
            }
            if (!empty($pml->district) && $pml->district !== 'match') {
                $memberQuery->where('district', $pml->district);
            }
            if ($selectedDistrict) {
                $memberQuery->where('district', $selectedDistrict);
            }

            $memberOfficers = $memberQuery->get(['id', 'name', 'phone', 'role', 'district']);
            $teamOfficerIds = array_merge([$pml->id], $memberOfficers->pluck('id')->toArray());

            $tQuery = Respondent::whereIn('officer_id', $teamOfficerIds);
            if ($selectedAssignmentId) {
                $tQuery->where('assignment_id', $selectedAssignmentId);
            }
            if ($selectedDistrict) {
                $tQuery->where('district', $selectedDistrict);
            }

            $tTarget = (clone $tQuery)->count();
            $tCompleted = (clone $tQuery)->where(function ($q) {
                $q->where('progress_status', 'selesai')->orHas('basts');
            })->count();
            $tInProgress = (clone $tQuery)->where('progress_status', 'sedang_didata')->count();
            $tPending = max(0, $tTarget - $tCompleted);
            $tPercentage = $tTarget > 0 ? min(100, round(($tCompleted / $tTarget) * 100, 1)) : ($tCompleted > 0 ? 100 : 0);

            if ($tTarget > 0 || count($pmlAssignmentIds) > 0 || $memberOfficers->count() > 0) {
                $teamProgressList[] = [
                    'pml' => $pml,
                    'members' => $memberOfficers,
                    'target' => $tTarget,
                    'completed' => $tCompleted,
                    'in_progress' => $tInProgress,
                    'pending' => $tPending,
                    'percentage' => $tPercentage,
                    'district' => $pml->district ?: '-',
                ];
            }
        }
        usort($teamProgressList, fn($a, $b) => $b['percentage'] <=> $a['percentage']);

        return view('admin.progress.field', compact(
            'assignments',
            'districts',
            'selectedAssignmentId',
            'selectedDistrict',
            'selectedRole',
            'statusFilter',
            'search',
            'totalFieldSamples',
            'completedFieldSamples',
            'inProgressFieldSamples',
            'pendingFieldSamples',
            'totalPercentage',
            'officerProgressList',
            'teamProgressList',
            'districtSummaries',
            'totalOfficersCount',
            'completedOfficersCount',
            'inProgressOfficersCount',
            'notStartedOfficersCount',
            'slowOfficersCount'
        ));
    }

    /**
     * Export data progres pendataan lapangan ke file CSV / Excel.
     */
    public function exportExcel(Request $request)
    {
        $selectedAssignmentId = $request->input('assignment_id');
        $selectedDistrict = $request->input('district');

        $assignmentTitle = 'Semua_Kegiatan';
        if ($selectedAssignmentId) {
            $asgn = Assignment::find($selectedAssignmentId);
            if ($asgn) {
                $assignmentTitle = preg_replace('/[^A-Za-z0-9_\-]/', '_', $asgn->title);
            }
        }

        $filename = "Progres_Pendataan_Lapangan_{$assignmentTitle}_" . date('Ymd_His') . ".csv";

        $officers = User::fieldStaff()
            ->where('status', 'active')
            ->withCount([
                'respondents' => function ($q) use ($selectedAssignmentId, $selectedDistrict) {
                    if ($selectedAssignmentId) $q->where('assignment_id', $selectedAssignmentId);
                    if ($selectedDistrict) $q->where('district', $selectedDistrict);
                },
                'respondents as completed_count' => function ($q) use ($selectedAssignmentId, $selectedDistrict) {
                    $q->where(function ($sq) {
                        $sq->where('progress_status', 'selesai')->orHas('basts');
                    });
                    if ($selectedAssignmentId) $q->where('assignment_id', $selectedAssignmentId);
                    if ($selectedDistrict) $q->where('district', $selectedDistrict);
                },
            ])
            ->orderBy('name')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($officers, $selectedAssignmentId) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

            fputcsv($handle, [
                'NO',
                'NAMA PETUGAS',
                'PERAN (PCL/PML)',
                'NOMOR HP / WA',
                'TARGET RESPONDEN',
                'REALISASI BAST SELESAI',
                'SISA BELUM SELESAI',
                'PERSENTASE CAPAIAN (%)',
                'STATUS KINERJA',
            ]);

            $no = 1;
            foreach ($officers as $off) {
                $role = $off->field_role ?? ($off->role === 'pegawai' ? 'PML' : 'PCL');
                $target = max($off->respondents_count, 0);
                $completed = (int) $off->completed_count;
                $pending = max(0, $target - $completed);
                $percent = $target > 0 ? min(100, round(($completed / $target) * 100, 1)) : 0;
                $status = $percent >= 100 ? 'Selesai 100%' : ($percent > 0 ? 'Sedang Berjalan' : 'Belum Mulai');

                fputcsv($handle, [
                    $no++,
                    $off->name,
                    $role,
                    $off->phone ?? '-',
                    $target,
                    $completed,
                    $pending,
                    $percent . '%',
                    $status,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
