<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class RoleSwitchController extends Controller
{
    /**
     * Switch active role for users who have multi-role (dual role) permissions.
     */
    public function switch(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->canSwitchRole()) {
            return back()->with('error', 'Akun Anda tidak memiliki hak akses multi-peran (dual role).');
        }

        $validated = $request->validate([
            'role' => ['required', 'string', 'in:admin,pegawai,petugas'],
        ]);

        $target = $validated['role'];

        if (!in_array($target, ['admin', 'pegawai'], true)) {
            return back()->with('error', 'Peran yang dipilih tidak valid.');
        }

        session(['active_role' => $target]);

        $label = $target === 'admin' ? 'Administrator' : 'Pegawai / Petugas Lapangan';

        ActivityLog::log(
            'update',
            'users',
            "Pengguna {$user->name} beralih ke Mode {$label}."
        );

        if ($target === 'admin') {
            return redirect()->route('admin.dashboard')->with('success', "Berhasil beralih ke Mode {$label}.");
        }

        return redirect()->route('petugas.dashboard')->with('success', "Berhasil beralih ke Mode {$label}.");
    }
}
