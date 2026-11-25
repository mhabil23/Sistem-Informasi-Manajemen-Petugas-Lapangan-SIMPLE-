<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingEvent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'document_number',
        'document_title',
        'activity_subtitle',
        'institution_name',
        'description',
        'start_date',
        'end_date',
        'location',
        'document_place',
        'maker_name',
        'maker_nip',
        'maker_signature_path',
        'notes',
        'quota',
        'status',
        'public_token',
        'assignment_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'quota' => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    private static ?bool $hasPublicTokenColumn = null;

    public static function hasPublicTokenColumn(): bool
    {
        if (self::$hasPublicTokenColumn !== null) {
            return self::$hasPublicTokenColumn;
        }

        try {
            return self::$hasPublicTokenColumn = \Illuminate\Support\Facades\Schema::hasColumn('training_events', 'public_token');
        } catch (\Throwable $e) {
            return self::$hasPublicTokenColumn = false;
        }
    }

    protected static function booted(): void
    {
        static::creating(function ($event) {
            if (self::hasPublicTokenColumn() && empty($event->public_token)) {
                $event->public_token = \Illuminate\Support\Str::random(24);
            }
        });
    }

    public function ensurePublicToken(): string
    {
        if (!empty($this->public_token)) {
            return $this->public_token;
        }

        if (!self::hasPublicTokenColumn()) {
            return 'unmigrated-' . ($this->id ?? '0');
        }

        $this->public_token = \Illuminate\Support\Str::random(24);
        try {
            $this->save();
        } catch (\Throwable $e) {
            // Hindari HTTP 500 saat getter diakses dalam kondisi database belum tersinkronisasi
        }

        return $this->public_token;
    }

    public function getPublicAttendanceUrlAttribute(): string
    {
        return route('attendance.public.show', $this->ensurePublicToken());
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class, 'assignment_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(TrainingParticipant::class, 'training_event_id')->orderBy('sort_order')->orderBy('id');
    }

    public function signedParticipantsCount(): int
    {
        return $this->participants->filter(fn ($p) => !empty($p->signature_path) || $p->attendance_status === 'present')->count();
    }

    public static function generateDocumentNumber(): string
    {
        $year = now()->year;
        $monthRoman = self::getRomanMonth(now()->month);
        $countThisYear = self::whereYear('created_at', $year)->count() + 1;
        $seq = str_pad($countThisYear, 4, '0', STR_PAD_LEFT);

        return "DH-BPS/{$year}/{$monthRoman}/{$seq}";
    }

    private static function getRomanMonth(int $month): string
    {
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];

        return $map[$month] ?? 'I';
    }
}

