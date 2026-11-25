<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\BerandaMenuVisibilityService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BerandaMenuController extends Controller
{
    public function __construct(
        private readonly BerandaMenuVisibilityService $visibilityService
    ) {
    }

    public function index()
    {
        $berandaState = $this->visibilityService->getState();

        return view('admin.beranda_menus.index', compact('berandaState'));
    }

    public function toggle(Request $request)
    {
        $menuKey = $request->input('menu_key');

        if ($menuKey !== null && $menuKey !== '') {
            $request->validate([
                'menu_key' => ['required', 'string', Rule::in(BerandaMenuVisibilityService::menuKeys())],
            ]);

            $nextState = $request->has('enabled')
                ? $request->boolean('enabled')
                : !$this->visibilityService->isMenuManuallyEnabled($menuKey);

            $this->visibilityService->setMenuEnabled($menuKey, $nextState);

            $menuLabel = BerandaMenuVisibilityService::menuDefinitions()[$menuKey]['label'] ?? $menuKey;
            $stateText = $nextState ? 'dinyalakan' : 'dimatikan';

            if (class_exists(ActivityLog::class)) {
                ActivityLog::log('toggle_status', 'beranda_menus', "Menu '{$menuLabel}' pada Beranda Petugas {$stateText} secara manual oleh Admin");
            }

            return redirect()->back()
                ->with('success', "Menu '{$menuLabel}' berhasil {$stateText}.");
        }

        $nextState = $request->has('enabled')
            ? $request->boolean('enabled')
            : !$this->visibilityService->isManuallyEnabled();

        $this->visibilityService->setManuallyEnabled($nextState);
        $stateText = $nextState ? 'dinyalakan semua' : 'dimatikan semua';

        if (class_exists(ActivityLog::class)) {
            ActivityLog::log('toggle_status', 'beranda_menus', "Seluruh Menu Beranda Petugas {$stateText} secara manual oleh Admin");
        }

        return redirect()->back()
            ->with('success', "Seluruh Menu Beranda Petugas berhasil {$stateText}.");
    }

    public function updatePeriod(Request $request)
    {
        $menuKey = $request->input('menu_key');

        if ($menuKey !== null && $menuKey !== '') {
            $request->validate([
                'menu_key' => ['required', 'string', Rule::in(BerandaMenuVisibilityService::menuKeys())],
            ]);
        }

        $menuLabel = $menuKey
            ? (BerandaMenuVisibilityService::menuDefinitions()[$menuKey]['label'] ?? $menuKey)
            : 'Seluruh Menu Beranda Petugas';

        if ($request->boolean('clear_period')) {
            if ($menuKey) {
                $this->visibilityService->setMenuPeriod($menuKey, null, null);
            } else {
                $this->visibilityService->setPeriod(null, null);
            }

            if (class_exists(ActivityLog::class)) {
                ActivityLog::log('update', 'beranda_menus', "Periode jadwal '{$menuLabel}' dikosongkan");
            }

            return redirect()->back()
                ->with('success', "Periode jadwal '{$menuLabel}' berhasil direset.");
        }

        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ], [
            'start_date.required' => 'Tanggal mulai periode wajib diisi.',
            'end_date.required' => 'Tanggal selesai periode wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
        ]);

        if ($menuKey) {
            $this->visibilityService->setMenuPeriod($menuKey, $validated['start_date'], $validated['end_date']);
        } else {
            $this->visibilityService->setPeriod($validated['start_date'], $validated['end_date']);
        }

        if (class_exists(ActivityLog::class)) {
            ActivityLog::log(
                'update',
                'beranda_menus',
                "Mengatur periode '{$menuLabel}': {$validated['start_date']} s.d. {$validated['end_date']}"
            );
        }

        return redirect()->back()
            ->with('success', "Periode tampilan '{$menuLabel}' berhasil disimpan.");
    }
}
