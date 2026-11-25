<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'assignment_id',
        'officer_id',
        'start_time',
        'end_time',
        'location',
        'notes',
        'status',
        'officer_status',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    public function isOfficerActive(): bool
    {
        return ($this->officer_status ?? 'active') === 'active';
    }

    public function getOfficerStatusLabelAttribute(): string
    {
        return ($this->officer_status ?? 'active') === 'active' ? 'Aktif' : 'Tidak Aktif';
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class, 'assignment_id');
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }
}

