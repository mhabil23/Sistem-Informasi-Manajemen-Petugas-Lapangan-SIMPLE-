<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\OfficerNotice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoticeController extends Controller
{
    /**
     * Tandai pesan telah dibaca oleh petugas
     */
    public function markAsRead(Request $request, OfficerNotice $notice)
    {
        if ($notice->user_id !== Auth::id()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $notice->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pesan ditandai telah dibaca.',
            ]);
        }

        return back()->with('success', 'Pemberitahuan ditandai telah dibaca.');
    }

    /**
     * Hapus pesan dari papan informasi petugas
     */
    public function destroy(Request $request, OfficerNotice $notice)
    {
        if ($notice->user_id !== Auth::id()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $notice->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pesan telah dihapus dari papan informasi.',
            ]);
        }

        return back()->with('success', 'Pesan telah dihapus dari papan informasi.');
    }
}
