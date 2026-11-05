<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfficerReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'report_number',
        'user_id',
        'officer_name',
        'officer_nip',
        'id_type',
        'officer_rank',
        'officer_position',
        'basis_dipa',
        'basis_st',
        'destination_purpose',
        'duration_text',
        'signing_city',
        'signing_date',
        'signature_path',
        'include_vehicle_statement',
        'assignment_id',
        'activity_date',
        'activity_title',
        'work_description',
        'recommendations',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
            'signing_date' => 'date',
            'reviewed_at' => 'datetime',
            'include_vehicle_statement' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class, 'assignment_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ReportAttachment::class, 'report_id');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'bg-secondary',
            'submitted' => 'bg-info',
            'reviewing' => 'bg-primary',
            'revision_needed' => 'bg-warning text-dark',
            'approved' => 'bg-success',
            default => 'bg-secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'submitted' => 'Diajukan',
            'reviewing' => 'Dalam Pemeriksaan',
            'revision_needed' => 'Perlu Revisi',
            'approved' => 'Disetujui',
            default => ucfirst($this->status),
        };
    }
}

