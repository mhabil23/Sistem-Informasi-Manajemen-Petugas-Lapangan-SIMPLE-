<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'nik' => ['nullable', 'string', 'max:32'],
            'rank' => ['nullable', 'string', 'max:100'],
            'position' => ['nullable', 'string', 'max:150'],
        ];

        if ($user->isAdmin()) {
            $rules['email'] = ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)];
        }

        $validated = $request->validate($rules, [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain.',
        ]);

        // Pastikan petugas dan pegawai tidak dapat mengubah email maupun username
        if (! $user->isAdmin()) {
            unset($validated['email'], $validated['username']);
        }

        $user->update($validated);

        ActivityLog::log(
            'update',
            'profile',
            "Pengguna {$user->name} memperbarui data profil akun."
        );

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        // Petugas dan Pegawai tidak diizinkan mengubah kata sandi mandiri
        if (! $user->isAdmin()) {
            return back()->with('error', 'Petugas dan Pegawai tidak diizinkan mengubah kata sandi mandiri. Silakan hubungi Administrator.');
        }

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Kata sandi lama wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini tidak cocok.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        ActivityLog::log(
            'update',
            'profile',
            "Pengguna {$user->name} berhasil mengubah kata sandi akun."
        );

        return back()->with('success', 'Kata sandi Anda berhasil diperbarui.');
    }
}

