<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'officer_id',
        'type',
        'title',
        'message',
        'is_read',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(OfficerReport::class, 'report_id');
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Record a notification for Admin when a petugas creates/submits/updates a Perjadin report.
     */
    public static function notifyReportEvent(OfficerReport $report, User $officer, string $event = 'created'): self
    {
        $statusText = $report->status === 'submitted' ? 'Diajukan (Menunggu ACC)' : 'Draft Baru';
        $title = match ($event) {
            'resubmitted' => "Revisi / Pembaruan Laporan Perjadin dari {$officer->name}",
            default => "Laporan Perjadin Baru dari {$officer->name}",
        };

        $message = "{$officer->name} membuat/mengirimkan laporan '{$report->activity_title}' ({$report->report_number}) dengan status: {$statusText}. Silakan lihat, edit jika perlu, atau ACC laporan.";

        return self::create([
            'report_id' => $report->id,
            'officer_id' => $officer->id,
            'type' => "report_{$event}",
            'title' => $title,
            'message' => $message,
            'is_read' => false,
        ]);
    }

    public static function markReadForReport(int $reportId): void
    {
        self::where('report_id', $reportId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }
}

