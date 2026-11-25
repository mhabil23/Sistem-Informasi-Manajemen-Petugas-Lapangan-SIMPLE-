<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BerandaMenuVisibilityService
{
    public const MENU_TANDA_TERIMA_RESPONDEN = 'tanda_terima_responden';
    public const MENU_TANDA_TERIMA_PETUGAS = 'tanda_terima_petugas';
    public const MENU_LAPORAN_PERJADIN = 'laporan_perjadin';
    public const MENU_JADWAL_AGENDA = 'jadwal_agenda';

    public const STATUS_MANUAL_ACTIVE = 'manual_active';
    public const STATUS_PERIOD_ACTIVE = 'period_active';
    public const STATUS_NOT_STARTED = 'not_started';
    public const STATUS_ENDED = 'ended';
    public const STATUS_INACTIVE = 'inactive';

    /**
     * Canonical definition of the 4 Beranda menus that Admin can control individually.
     *
     * @return array<string, array<string, string>>
     */
    public static function menuDefinitions(): array
    {
        return [
            self::MENU_TANDA_TERIMA_RESPONDEN => [
                'label' => 'Tanda Terima Responden (BAST)',
                'subtitle' => 'Input tanda tangan & serah terima responden',
                'icon' => 'bi-file-earmark-check-fill',
                'color' => '#16A34A',
                'soft_bg' => '#DCFCE7',
            ],
            self::MENU_TANDA_TERIMA_PETUGAS => [
                'label' => 'Tanda Terima & BAST Petugas',
                'subtitle' => 'Tanda terima blanko & BAST dokumen terisi',
                'icon' => 'bi-shield-check',
                'color' => '#0284C7',
                'soft_bg' => '#E0F2FE',
            ],
            self::MENU_LAPORAN_PERJADIN => [
                'label' => 'Laporan Perjadin',
                'subtitle' => 'Ajukan & pantau persetujuan laporan perjadin',
                'icon' => 'bi-clipboard2-check-fill',
                'color' => '#7C3AED',
                'soft_bg' => '#F3E8FF',
            ],
            self::MENU_JADWAL_AGENDA => [
                'label' => 'Jadwal & Agenda BPS',
                'subtitle' => 'Kalender kegiatan & jadwal lapangan',
                'icon' => 'bi-calendar3',
                'color' => '#D97706',
                'soft_bg' => '#FEF3C7',
            ],
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function menuKeys(): array
    {
        return array_keys(self::menuDefinitions());
    }

    private function settingKey(string $menuKey, string $suffix): string
    {
        return "beranda_menu_{$menuKey}_{$suffix}";
    }

    private function getSetting(string $key, ?string $default = null): ?string
    {
        try {
            if (!Schema::hasTable('app_settings')) {
                return $default;
            }

            $value = DB::table('app_settings')->where('key', $key)->value('value');

            return $value === null ? $default : (string) $value;
        } catch (\Throwable $e) {
            return $default;
        }
    }

    private function putSetting(string $key, ?string $value): void
    {
        if (!Schema::hasTable('app_settings')) {
            return;
        }

        $exists = DB::table('app_settings')->where('key', $key)->exists();
        $payload = [
            'value' => $value,
            'updated_at' => now(),
        ];

        if ($exists) {
            DB::table('app_settings')->where('key', $key)->update($payload);
        } else {
            DB::table('app_settings')->insert(array_merge($payload, [
                'key' => $key,
                'created_at' => now(),
            ]));
        }
    }

    private function parseDate(?string $raw): ?Carbon
    {
        if (!$raw) {
            return null;
        }

        try {
            return Carbon::parse($raw)->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function isMenuManuallyEnabled(string $menuKey): bool
    {
        $value = $this->getSetting($this->settingKey($menuKey, 'enabled'), '0');

        return in_array((string) $value, ['1', 'true', 'on', 'yes'], true);
    }

    public function setMenuEnabled(string $menuKey, bool $enabled): void
    {
        if (!array_key_exists($menuKey, self::menuDefinitions())) {
            return;
        }

        $this->putSetting($this->settingKey($menuKey, 'enabled'), $enabled ? '1' : '0');
    }

    public function getMenuStartDate(string $menuKey): ?Carbon
    {
        return $this->parseDate($this->getSetting($this->settingKey($menuKey, 'start_date')));
    }

    public function getMenuEndDate(string $menuKey): ?Carbon
    {
        return $this->parseDate($this->getSetting($this->settingKey($menuKey, 'end_date')));
    }

    public function setMenuPeriod(string $menuKey, ?string $startDate, ?string $endDate): void
    {
        if (!array_key_exists($menuKey, self::menuDefinitions())) {
            return;
        }

        $this->putSetting($this->settingKey($menuKey, 'start_date'), $startDate ?: null);
        $this->putSetting($this->settingKey($menuKey, 'end_date'), $endDate ?: null);
    }

    public function isMenuPeriodActive(string $menuKey, ?Carbon $date = null): bool
    {
        $start = $this->getMenuStartDate($menuKey);
        $end = $this->getMenuEndDate($menuKey);
        if (!$start || !$end) {
            return false;
        }

        $current = ($date ?? Carbon::today())->copy()->startOfDay();

        return $current->gte($start->copy()->startOfDay()) && $current->lte($end->copy()->endOfDay());
    }

    public function shouldShowMenu(string $menuKey, ?Carbon $date = null): bool
    {
        return $this->isMenuManuallyEnabled($menuKey) || $this->isMenuPeriodActive($menuKey, $date);
    }

    public function getMenuStatusCode(string $menuKey, ?Carbon $date = null): string
    {
        if ($this->isMenuManuallyEnabled($menuKey)) {
            return self::STATUS_MANUAL_ACTIVE;
        }

        if ($this->isMenuPeriodActive($menuKey, $date)) {
            return self::STATUS_PERIOD_ACTIVE;
        }

        $current = ($date ?? Carbon::today())->copy()->startOfDay();
        $start = $this->getMenuStartDate($menuKey);
        $end = $this->getMenuEndDate($menuKey);

        if ($start && $current->lt($start)) {
            return self::STATUS_NOT_STARTED;
        }

        if ($end && $current->gt($end)) {
            return self::STATUS_ENDED;
        }

        return self::STATUS_INACTIVE;
    }

    /**
     * Build status metadata for a single menu item.
     *
     * @return array<string, mixed>
     */
    public function getMenuState(string $menuKey, ?Carbon $date = null): array
    {
        $definitions = self::menuDefinitions();
        $def = $definitions[$menuKey] ?? [
            'label' => $menuKey,
            'subtitle' => '',
            'icon' => 'bi-grid',
            'color' => '#64748B',
            'soft_bg' => '#F1F5F9',
        ];

        $statusCode = $this->getMenuStatusCode($menuKey, $date);
        $meta = $this->statusMetaForCode($statusCode);
        $start = $this->getMenuStartDate($menuKey);
        $end = $this->getMenuEndDate($menuKey);

        return [
            'key' => $menuKey,
            'label' => $def['label'],
            'subtitle' => $def['subtitle'],
            'icon' => $def['icon'],
            'color' => $def['color'],
            'soft_bg' => $def['soft_bg'],
            'is_manually_enabled' => $this->isMenuManuallyEnabled($menuKey),
            'is_period_active' => $this->isMenuPeriodActive($menuKey, $date),
            'is_visible' => $this->shouldShowMenu($menuKey, $date),
            'start_date' => $start?->toDateString(),
            'end_date' => $end?->toDateString(),
            'start_date_formatted' => $start?->format('d/m/Y'),
            'end_date_formatted' => $end?->format('d/m/Y'),
            'status_code' => $statusCode,
            'status_label' => $meta['label'],
            'status_badge_class' => $meta['badge_class'],
            'status_description' => $meta['description'],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getMenuStates(?Carbon $date = null): array
    {
        $states = [];
        foreach (self::menuKeys() as $menuKey) {
            $states[$menuKey] = $this->getMenuState($menuKey, $date);
        }

        return $states;
    }

    /**
     * Toggle all 4 menus together (useful for "Nyalakan Semua" / "Matikan Semua").
     */
    public function setManuallyEnabled(bool $enabled): void
    {
        foreach (self::menuKeys() as $menuKey) {
            $this->setMenuEnabled($menuKey, $enabled);
        }
    }

    /**
     * Apply the same period window (or clear it) across all 4 menus.
     */
    public function setPeriod(?string $startDate, ?string $endDate): void
    {
        foreach (self::menuKeys() as $menuKey) {
            $this->setMenuPeriod($menuKey, $startDate, $endDate);
        }
    }

    public function isManuallyEnabled(): bool
    {
        foreach (self::menuKeys() as $menuKey) {
            if ($this->isMenuManuallyEnabled($menuKey)) {
                return true;
            }
        }

        return false;
    }

    public function shouldShowMenus(?Carbon $date = null): bool
    {
        foreach (self::menuKeys() as $menuKey) {
            if ($this->shouldShowMenu($menuKey, $date)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Overall aggregate state plus per-menu state map for Admin & Petugas UI.
     *
     * @return array<string, mixed>
     */
    public function getState(?Carbon $date = null): array
    {
        $menus = $this->getMenuStates($date);
        $totalCount = count($menus);
        $visibleCount = collect($menus)->where('is_visible', true)->count();
        $manualCount = collect($menus)->where('is_manually_enabled', true)->count();
        $allManualOn = $manualCount === $totalCount;

        if ($visibleCount === $totalCount) {
            $summaryLabel = 'Semua Menu Tampil (4/4)';
            $summaryBadgeClass = 'bg-success text-white';
            $summaryDesc = 'Seluruh 4 menu Beranda sedang tampil ke Petugas.';
        } elseif ($visibleCount > 0) {
            $summaryLabel = "{$visibleCount} dari {$totalCount} Menu Tampil";
            $summaryBadgeClass = 'bg-primary text-white';
            $summaryDesc = 'Sebagian menu Beranda aktif dan tampil sesuai pengaturan masing-masing.';
        } else {
            $summaryLabel = 'Semua Nonaktif (Tersembunyi)';
            $summaryBadgeClass = 'bg-secondary text-white';
            $summaryDesc = 'Ke-4 menu disembunyikan dari Beranda sampai masing-masing periode tiba atau dinyalakan oleh Admin.';
        }

        return [
            'is_manually_enabled' => $manualCount > 0,
            'all_manually_enabled' => $allManualOn,
            'manual_count' => $manualCount,
            'visible_count' => $visibleCount,
            'total_count' => $totalCount,
            'is_visible' => $visibleCount > 0,
            'status_label' => $summaryLabel,
            'status_badge_class' => $summaryBadgeClass,
            'status_description' => $summaryDesc,
            'menus' => $menus,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function statusMetaForCode(string $statusCode): array
    {
        return match ($statusCode) {
            self::STATUS_MANUAL_ACTIVE => [
                'label' => 'Aktif Manual (Admin)',
                'badge_class' => 'bg-success text-white',
                'description' => 'Ditampilkan karena tombol menu ini dinyalakan langsung oleh Admin.',
            ],
            self::STATUS_PERIOD_ACTIVE => [
                'label' => 'Aktif Periode',
                'badge_class' => 'bg-primary text-white',
                'description' => 'Tampil otomatis karena tanggal hari ini berada dalam periode menu ini.',
            ],
            self::STATUS_NOT_STARTED => [
                'label' => 'Belum Dimulai',
                'badge_class' => 'bg-warning text-dark',
                'description' => 'Periode menu ini belum dimulai. Tetap tersembunyi sampai waktunya tiba atau dinyalakan Admin.',
            ],
            self::STATUS_ENDED => [
                'label' => 'Periode Berakhir',
                'badge_class' => 'bg-danger text-white',
                'description' => 'Periode menu ini telah berakhir dan otomatis disembunyikan kembali.',
            ],
            default => [
                'label' => 'Nonaktif (Tersembunyi)',
                'badge_class' => 'bg-secondary text-white',
                'description' => 'Disembunyikan di Beranda sampai periode tiba atau dinyalakan Admin.',
            ],
        };
    }
}
