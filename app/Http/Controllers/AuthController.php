<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
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

        $loginType = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $attemptData = [
            $loginType => $credentials['login'],
            'password' => $credentials['password'],
        ];

        $remember = $request->boolean('remember');

        if (Auth::attempt($attemptData, $remember)) {
            $user = Auth::user();

            if (!$user->isActive()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withInput($request->only('login', 'remember'))->withErrors([
                    'login' => 'Akun Anda berstatus nonaktif. Silakan hubungi Administrator SIMPLE.',
                ]);
            }

            $request->session()->regenerate();

            ActivityLog::log(
                'login',
                'auth',
                "Pengguna {$user->name} ({$user->role}) berhasil masuk ke sistem."
            );

            $notification = 'Selamat datang kembali, ' . $user->name . '!';

            if ($user->isAdmin()) {
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

