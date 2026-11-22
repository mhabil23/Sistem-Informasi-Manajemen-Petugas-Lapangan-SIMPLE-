<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = auth()->user();

        if (!$user->isActive()) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', 'Akun Anda dinonaktifkan. Hubungi Administrator.');
        }

        $activeRole = $user->activeRole();

        if (!in_array($activeRole, $roles, true)) {
            // If user role is not authorized, redirect to their own role dashboard
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')->with('error', 'Anda sedang dalam Mode Administrator. Silakan beralih ke Mode Pegawai melalui menu profil di kanan atas jika ingin mengakses halaman ini.');
            }

            if ($user->canSwitchRole()) {
                return redirect()->route('petugas.dashboard')->with('error', 'Halaman ini khusus untuk Administrator. Beralihlah ke Mode Administrator melalui menu profil di kanan atas.');
            }

            return redirect()->route('petugas.dashboard')->with('error', 'Halaman ini khusus untuk Administrator.');
        }

        return $next($request);
    }
}

