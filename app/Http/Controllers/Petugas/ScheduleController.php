<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $schedules = Schedule::with('assignment')
            ->where(function ($q) use ($userId) {
                $q->where('officer_id', $userId)
                  ->orWhereNull('officer_id');
            })
            ->orderBy('start_time', 'asc')
            ->paginate(10);

        $totalSchedules = Schedule::where(function ($q) use ($userId) {
                $q->where('officer_id', $userId)
                  ->orWhereNull('officer_id');
            })->count();

        $upcomingSchedules = Schedule::where(function ($q) use ($userId) {
                $q->where('officer_id', $userId)
                  ->orWhereNull('officer_id');
            })
            ->where('start_time', '>=', now())
            ->count();

        return view('petugas.schedules.index', compact(
            'schedules',
            'totalSchedules',
            'upcomingSchedules'
        ));
    }
}

