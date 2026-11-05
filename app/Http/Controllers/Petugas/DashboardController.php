<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\AssignmentOfficer;
use App\Models\OfficerBast;
use App\Models\OfficerReport;
use App\Models\Respondent;
use App\Models\RespondentBast;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Assignment Stats
        $myAssignmentsQuery = AssignmentOfficer::where('user_id', $user->id);
        $totalAssignedTasks = (clone $myAssignmentsQuery)->count();
        $tasksInProgress = (clone $myAssignmentsQuery)->where('status', 'in_progress')->count();
        $tasksCompleted = (clone $myAssignmentsQuery)->where('status', 'completed')->count();

        // 2. Respondents Inputted by This Officer & Target Calculation
        $totalRespondents = Respondent::where('officer_id', $user->id)->count();
        $assignedTargetSum = (int) AssignmentOfficer::where('assignment_officers.user_id', $user->id)
            ->join('assignments', 'assignments.id', '=', 'assignment_officers.assignment_id')
            ->sum('assignments.target_respondents');
        $targetRespondents = $assignedTargetSum > 0 ? $assignedTargetSum : ($totalRespondents > 120 ? ceil($totalRespondents / 50) * 50 : 120);
        $progressPercentage = $targetRespondents > 0 ? min(100, round(($totalRespondents / $targetRespondents) * 100)) : 0;
        $remainingRespondents = max(0, $targetRespondents - $totalRespondents);

        // Nearest Active Task with Deadline
        $nearestTaskDeadline = AssignmentOfficer::with('assignment')
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereHas('assignment', function ($q) {
                $q->whereNotNull('end_date');
            })
            ->get()
            ->sortBy(function ($task) {
                return $task->assignment->end_date;
            })
            ->first();

        $daysUntilDeadline = null;
        $shouldRemindDeadline = false;
        $isUrgentDeadline = false;
        if ($nearestTaskDeadline && $nearestTaskDeadline->assignment && $nearestTaskDeadline->assignment->end_date) {
            $daysUntilDeadline = (int) now()->startOfDay()->diffInDays($nearestTaskDeadline->assignment->end_date->copy()->startOfDay(), false);
            // Validasi: muncul sebagai notifikasi jika masih ada sisa responden yang belum didata
            $shouldRemindDeadline = ($remainingRespondents > 0);
            $isUrgentDeadline = $daysUntilDeadline <= 7;
        }

        // 3. BASTs Created by This Officer
        $totalRespondentBasts = RespondentBast::where('officer_id', $user->id)->count();
        $totalOfficerBasts = OfficerBast::where('officer_id', $user->id)->count();
        $totalBasts = $totalRespondentBasts + $totalOfficerBasts;

        // 4. Report Stats
        $reportsDraft = OfficerReport::where('user_id', $user->id)->where('status', 'draft')->count();
        $reportsSubmitted = OfficerReport::where('user_id', $user->id)->whereIn('status', ['submitted', 'reviewing'])->count();
        $reportsRevision = OfficerReport::where('user_id', $user->id)->where('status', 'revision_needed')->count();
        $reportsApproved = OfficerReport::where('user_id', $user->id)->where('status', 'approved')->count();

        // 5. Nearest Schedules
        $upcomingSchedules = Schedule::where(function ($q) use ($user) {
                $q->where('officer_id', $user->id)
                  ->orWhereNull('officer_id');
            })
            ->where('end_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->take(5)
            ->get();

        // 6. Active Assigned Tasks with Details
        $activeTasks = AssignmentOfficer::with('assignment')
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->latest()
            ->take(5)
            ->get();

        // 7. Recent Reports Status
        $recentReports = OfficerReport::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('petugas.dashboard', compact(
            'user',
            'totalAssignedTasks',
            'tasksInProgress',
            'tasksCompleted',
            'totalRespondents',
            'targetRespondents',
            'progressPercentage',
            'remainingRespondents',
            'nearestTaskDeadline',
            'daysUntilDeadline',
            'shouldRemindDeadline',
            'isUrgentDeadline',
            'totalBasts',
            'totalRespondentBasts',
            'totalOfficerBasts',
            'reportsDraft',
            'reportsSubmitted',
            'reportsRevision',
            'reportsApproved',
            'upcomingSchedules',
            'activeTasks',
            'recentReports'
        ));
    }
}

