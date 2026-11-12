<?php

namespace App\Providers;

use App\Models\AdminNotification;
use App\Models\OfficerReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            if (Auth::check() && Auth::user()->isAdmin()) {
                $view->with([
                    'adminPendingReportsCount' => OfficerReport::whereIn('status', ['submitted', 'reviewing'])->count(),
                    'adminDraftReportsCount' => OfficerReport::where('status', 'draft')->count(),
                    'adminUnreadNotifCount' => AdminNotification::unread()->count(),
                    'adminRecentNotifs' => AdminNotification::with(['officer', 'report'])->latest()->take(8)->get(),
                ]);
            }
        });
    }
}
