<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\OfficerReport;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function poll()
    {
        $unreadCount = AdminNotification::unread()->count();
        $pendingCount = OfficerReport::whereIn('status', ['submitted', 'reviewing'])->count();
        $draftCount = OfficerReport::where('status', 'draft')->count();

        $notifications = AdminNotification::with(['officer', 'report'])
            ->latest()
            ->take(8)
            ->get()
            ->map(function (AdminNotification $n) {
                return [
                    'id' => $n->id,
                    'report_id' => $n->report_id,
                    'title' => $n->title,
                    'message' => $n->message,
                    'officer_name' => $n->officer?->name ?? 'Petugas',
                    'report_title' => $n->report?->activity_title ?? '-',
                    'report_number' => $n->report?->report_number ?? ($n->report_id ? 'LAP-' . $n->report_id : '-'),
                    'report_status' => $n->report?->status ?? 'submitted',
                    'report_status_label' => $n->report?->status_label ?? 'Diajukan',
                    'is_read' => (bool) $n->is_read,
                    'time_ago' => $n->created_at ? $n->created_at->diffForHumans() : '-',
                    'url' => $n->report_id
                        ? route('admin.reports.index', ['inspect' => $n->report_id, 'notif_id' => $n->id])
                        : route('admin.reports.index'),
                ];
            })
            ->values();

        return response()->json([
            'unread_count' => $unreadCount,
            'pending_count' => $pendingCount,
            'draft_count' => $draftCount,
            'notifications' => $notifications,
        ]);
    }

    public function markAllRead(Request $request)
    {
        AdminNotification::unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Semua notifikasi laporan telah ditandai dibaca.');
    }

    public function markAsRead(Request $request, AdminNotification $notification)
    {
        if (!$notification->is_read) {
            $notification->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back();
    }
}

