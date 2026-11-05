<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if ($module = $request->input('module')) {
            if ($module !== 'all') {
                $query->where('module', $module);
            }
        }

        if ($action = $request->input('action')) {
            if ($action !== 'all') {
                $query->where('action', $action);
            }
        }

        if ($userId = $request->input('user_id')) {
            $query->where('user_id', $userId);
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $totalLogs = ActivityLog::count();
        $todayLogs = ActivityLog::whereDate('created_at', today())->count();
        $loginLogs = ActivityLog::where('action', 'login')->count();
        $mutationLogs = ActivityLog::whereIn('action', ['create', 'update', 'delete'])->count();

        $logs = $query->latest('id')->paginate(15)->withQueryString();

        $users = User::orderBy('name')->get();

        return view('admin.logs.index', compact(
            'logs',
            'totalLogs',
            'todayLogs',
            'loginLogs',
            'mutationLogs',
            'users'
        ));
    }

    public function show(ActivityLog $log)
    {
        $log->load('user');

        return response()->json([
            'success' => true,
            'log' => $log,
        ]);
    }

    public function clearOld(Request $request)
    {
        $days = (int) $request->input('days', 30);
        $deleted = ActivityLog::where('created_at', '<', now()->subDays($days))->delete();

        ActivityLog::log(
            'delete',
            'logs',
            "Membersihkan {$deleted} rekaman log aktivitas yang berumur lebih dari {$days} hari."
        );

        return back()->with('success', "Berhasil membersihkan {$deleted} rekaman audit log lawas.");
    }
}

