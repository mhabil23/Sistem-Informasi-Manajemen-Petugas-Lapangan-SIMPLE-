<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return Auth::user()->canAccessMonitoring()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('petugas.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle authentication attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'Username atau Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $loginInput = trim($credentials['login']);
        $password = (string) $credentials['password'];
        $remember = $request->boolean('remember');

        // Cari pengguna secara fleksibel: cocokkan email ATAU username (case-insensitive & trim)
        $user = User::where(function ($q) use ($loginInput) {
            $q->where('email', $loginInput)
              ->orWhere('username', $loginInput)
              ->orWhereRaw('LOWER(email) = ?', [strtolower($loginInput)])
              ->orWhereRaw('LOWER(username) = ?', [strtolower($loginInput)]);
        })->first();

        // Validasi kecocokan password:
        // 1. Password sesuai hash di database
        // 2. Untuk role petugas/pegawai yang memakai password default (bps7408 atau password), keduanya diterima
        $isPasswordValid = false;
        if ($user) {
            if (Hash::check($password, $user->password)) {
                $isPasswordValid = true;
            } elseif ($user->isFieldStaff() && in_array($password, ['bps7408', 'password'], true)) {
                if (Hash::check('bps7408', $user->password) || Hash::check('password', $user->password)) {
                    $isPasswordValid = true;
                }
            }
        }

        if ($user && $isPasswordValid) {
            if (!$user->isActive()) {
                return back()->withInput($request->only('login', 'remember'))->withErrors([
                    'login' => 'Akun Anda berstatus nonaktif. Silakan hubungi Administrator SIMPLE.',
                ]);
            }

            Auth::login($user, $remember);
            $request->session()->regenerate();

            ActivityLog::log(
                'login',
                'auth',
                "Pengguna {$user->name} ({$user->role}) berhasil masuk ke sistem."
            );

            $notification = 'Selamat datang kembali, ' . $user->name . '!';

            if ($user->canAccessMonitoring()) {
                return redirect()->intended(route('admin.dashboard'))->with('success', $notification);
            }

            return redirect()->intended(route('petugas.dashboard'))->with('success', $notification);
        }

        return back()->withInput($request->only('login', 'remember'))->withErrors([
            'login' => 'Username/Email atau password yang Anda masukkan salah.',
        ]);
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            ActivityLog::log(
                'logout',
                'auth',
                "Pengguna {$user->name} keluar dari sistem."
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem SIMPLE.');
    }
}

