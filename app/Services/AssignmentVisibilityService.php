<?php

namespace App\Services;

use App\Models\Assignment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AssignmentVisibilityService
{
    public const GLOBAL_SETTING_KEY = 'assignments_portal_enabled';

    public const STATUS_MANUAL_ACTIVE = 'manual_active';
    public const STATUS_PERIOD_ACTIVE = 'period_active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_ENDED = 'ended';

    /**
     * Check whether the global assignment portal control is enabled by Admin.
     * Defaults to true when not yet explicitly set so existing flows remain intact.
     */
    public function isGlobalEnabled(): bool
    {
        try {
            if (!Schema::hasTable('app_settings')) {
                return true;
            }

            $value = DB::table('app_settings')
                ->where('key', self::GLOBAL_SETTING_KEY)
                ->value('value');

            if ($value === null) {
                return true;
            }

            return in_array((string) $value, ['1', 'true', 'on', 'yes'], true);
        } catch (\Throwable $e) {
            return true;
        }
    }

    /**
     * Persist global assignment visibility control in app_settings.
     */
    public function setGlobalEnabled(bool $enabled): void
    {
        if (!Schema::hasTable('app_settings')) {
            return;
        }

        $exists = DB::table('app_settings')->where('key', self::GLOBAL_SETTING_KEY)->exists();
        $payload = [
            'value' => $enabled ? '1' : '0',
            'updated_at' => now(),
        ];

        if ($exists) {
            DB::table('app_settings')->where('key', self::GLOBAL_SETTING_KEY)->update($payload);
        } else {
            DB::table('app_settings')->insert(array_merge($payload, [
                'key' => self::GLOBAL_SETTING_KEY,
                'created_at' => now(),
            ]));
        }
    }

    /**
     * Determine whether an assignment's date range is active on the given date.
     */
    public function isPeriodActive(Assignment $assignment, ?Carbon $date = null): bool
    {
        if (!$assignment->start_date || !$assignment->end_date) {
            return false;
        }

        $current = ($date ?? Carbon::today())->copy()->startOfDay();
        $start = $assignment->start_date->copy()->startOfDay();
        $end = $assignment->end_date->copy()->endOfDay();

        return $current->gte($start) && $current->lte($end);
    }

    /**
     * Evaluate visibility rule:
     * Global ON AND (is_enabled_by_admin == true OR (current_date >= start_date AND current_date <= end_date)).
     */
    public function shouldShowToOfficer(Assignment $assignment, ?Carbon $date = null): bool
    {
        if (!$this->isGlobalEnabled()) {
            return false;
        }

        if ($assignment->status === 'cancelled') {
            return false;
        }

        return (bool) $assignment->is_enabled_by_admin || $this->isPeriodActive($assignment, $date);
    }

    /**
     * Compute status code for Admin & Petugas displays:
     * - manual_active: Aktif Manual (Admin)
     * - period_active: Aktif Periode
     * - ended: Berakhir
     * - inactive: Nonaktif
     */
    public function getVisibilityStatus(Assignment $assignment, ?Carbon $date = null): string
    {
        if ($assignment->status === 'cancelled') {
            return self::STATUS_INACTIVE;
        }

        if ((bool) $assignment->is_enabled_by_admin) {
            return self::STATUS_MANUAL_ACTIVE;
        }

        if ($this->isPeriodActive($assignment, $date)) {
            return self::STATUS_PERIOD_ACTIVE;
        }

        $current = ($date ?? Carbon::today())->copy()->startOfDay();
        if ($assignment->end_date && $current->gt($assignment->end_date->copy()->endOfDay())) {
            return self::STATUS_ENDED;
        }

        return self::STATUS_INACTIVE;
    }

    /**
     * Human-readable status label required by specification.
     */
    public function getVisibilityStatusLabel(Assignment $assignment, ?Carbon $date = null): string
    {
        return match ($this->getVisibilityStatus($assignment, $date)) {
            self::STATUS_MANUAL_ACTIVE => 'Aktif Manual (Admin)',
            self::STATUS_PERIOD_ACTIVE => 'Aktif Periode',
            self::STATUS_ENDED => 'Berakhir',
            default => 'Nonaktif',
        };
    }

    /**
     * Bootstrap badge classes for the computed visibility status.
     */
    public function getVisibilityStatusBadgeClass(Assignment $assignment, ?Carbon $date = null): string
    {
        return match ($this->getVisibilityStatus($assignment, $date)) {
            self::STATUS_MANUAL_ACTIVE => 'bg-success-subtle text-success border border-success-subtle',
            self::STATUS_PERIOD_ACTIVE => 'bg-primary-subtle text-primary border border-primary-subtle',
            self::STATUS_ENDED => 'bg-danger-subtle text-danger border border-danger-subtle',
            default => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
        };
    }

    /**
     * Apply visibility SQL constraints to an Assignment query builder.
     */
    public function applyVisibilityScope(Builder $query, ?Carbon $date = null): Builder
    {
        if (!$this->isGlobalEnabled()) {
            return $query->whereRaw('1 = 0');
        }

        $todayStr = ($date ?? Carbon::today())->toDateString();

        return $query->where('status', '!=', 'cancelled')
            ->where(function (Builder $q) use ($todayStr) {
                $q->where('is_enabled_by_admin', true)
                  ->orWhere(function (Builder $periodQuery) use ($todayStr) {
                      $periodQuery->whereNotNull('start_date')
                                  ->whereNotNull('end_date')
                                  ->whereDate('start_date', '<=', $todayStr)
                                  ->whereDate('end_date', '>=', $todayStr);
                  });
            });
    }

    /**
     * Get the appropriate empty-state message for the Petugas dashboard.
     */
    public function getEmptyMessage(): string
    {
        if (!$this->isGlobalEnabled()) {
            return 'Penugasan lapangan belum dibuka oleh Admin.';
        }

        return 'Belum ada penugasan lapangan yang tersedia saat ini.';
    }
}
