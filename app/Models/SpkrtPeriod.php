<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpkrtPeriod extends Model
{
    use HasFactory;

    protected $table = 'spkrt_periods';

    protected $fillable = [
        'activity_name',
        'start_date',
        'end_date',
        'is_active',
        'description',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Determine computed status code based on dates and active flag:
     * - 'not_started': Hari ini sebelum tanggal mulai
     * - 'closed': Hari ini setelah tanggal selesai atau dinonaktifkan
     * - 'active': Berada di rentang tanggal dan is_active bernilai true
     */
    public function currentStatus(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }

        $today = Carbon::today();
        $start = $this->start_date ? $this->start_date->copy()->startOfDay() : null;
        $end = $this->end_date ? $this->end_date->copy()->endOfDay() : null;

        if ($start && $today->lt($start)) {
            return 'not_started';
        }

        if ($end && $today->gt($end)) {
            return 'closed';
        }

        return 'active';
    }

    /**
     * Whether Petugas is allowed to fill SPKRT under this period.
     * System automatically locks the form if end_date has passed (Req 8).
     */
    public function isOpen(): bool
    {
        return $this->is_active && $this->currentStatus() === 'active';
    }

    /**
     * Standard human-readable status message as required:
     * - "Pengisian belum dibuka" (Req 4)
     * - "Periode pengisian telah ditutup" (Req 5)
     * - "Pengisian SPKRT Dibuka" (Req 6)
     */
    public function getStatusMessageAttribute(): string
    {
        $status = $this->currentStatus();

        return match ($status) {
            'not_started' => 'Pengisian belum dibuka',
            'active' => 'Pengisian SPKRT Dibuka',
            'closed', 'inactive' => 'Periode pengisian telah ditutup',
            default => 'Periode pengisian telah ditutup',
        };
    }

    /**
     * Get badge styling class for UI.
     */
    public function getBadgeClassAttribute(): string
    {
        $status = $this->currentStatus();

        return match ($status) {
            'active' => 'bg-success text-white',
            'not_started' => 'bg-warning text-dark',
            'closed' => 'bg-danger text-white',
            'inactive' => 'bg-secondary text-white',
            default => 'bg-secondary text-white',
        };
    }

    /**
     * Get the latest active period, or fallback to the latest period.
     */
    public static function currentPeriod(): ?self
    {
        return static::where('is_active', true)
            ->latest('id')
            ->first() ?? static::latest('id')->first();
    }
}
