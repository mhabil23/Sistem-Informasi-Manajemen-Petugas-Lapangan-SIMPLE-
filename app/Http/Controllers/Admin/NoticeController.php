<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\OfficerNotice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoticeController extends Controller
{
    /**
     * Kirim pesan / notifikasi ke papan informasi petugas
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'string'],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'type' => ['nullable', 'string', 'max:50'],
        ], [
            'user_id.required' => 'Penerima notifikasi wajib ditentukan.',
            'title.required' => 'Judul pesan notifikasi wajib diisi.',
            'message.required' => 'Isi pesan notifikasi wajib diisi.',
        ]);

        $sender = Auth::user();
        $type = $validated['type'] ?? 'report_reminder';

        // 1. Opsi Kirim Massal ke Seluruh Petugas yang Belum Menyerahkan Laporan
        if ($validated['user_id'] === 'all_without_reports') {
            $officersWithoutReports = User::fieldStaff()
                ->where('status', 'active')
                ->whereDoesntHave('reports')
                ->get();

            if ($officersWithoutReports->isEmpty()) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Semua petugas sudah memiliki laporan kegiatan.']);
                }
                return back()->with('info', 'Semua petugas lapangan sudah memiliki laporan kegiatan.');
            }

            foreach ($officersWithoutReports as $officer) {
                OfficerNotice::create([
                    'user_id' => $officer->id,
                    'sender_id' => $sender->id,
                    'type' => $type,
                    'title' => $validated['title'],
                    'message' => $validated['message'],
                    'is_read' => false,
                ]);
            }

            $count = $officersWithoutReports->count();
            ActivityLog::log(
                'create',
                'officer_notices',
                "Admin {$sender->name} mengirimkan pengingat laporan ke papan informasi {$count} petugas lapangan."
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Pesan pengingat berhasil dikirim ke papan informasi {$count} petugas lapangan!",
                ]);
            }

            return back()->with('success', "Pesan pengingat berhasil dikirim ke papan informasi {$count} petugas lapangan!");
        }

        // 2. Kirim ke Satu Petugas Khusus
        $officer = User::fieldStaff()->findOrFail($validated['user_id']);

        $notice = OfficerNotice::create([
            'user_id' => $officer->id,
            'sender_id' => $sender->id,
            'type' => $type,
            'title' => $validated['title'],
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        ActivityLog::log(
            'create',
            'officer_notices',
            "Admin {$sender->name} mengirimkan pesan ke papan informasi petugas {$officer->name}: {$validated['title']}."
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Pesan pengingat berhasil dikirim ke papan informasi {$officer->name}!",
                'notice' => $notice,
            ]);
        }

        return back()->with('success', "Pesan pengingat berhasil dikirim ke papan informasi {$officer->name}!");
    }
}

