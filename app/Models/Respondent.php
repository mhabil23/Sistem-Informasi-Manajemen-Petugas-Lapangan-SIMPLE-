<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Respondent extends Model
{
    use HasFactory, SoftDeletes;

    public const DISTRICTS = [
        'Batu Putih',
        'Katoi',
        'Kodeoha',
        'Lambai',
        'Lasusua',
        'Ngapa',
        'Pakue',
        'Pakue Tengah',
        'Pakue Utara',
        'Porehu',
        'Ranteangin',
        'Tiwu',
        'Tolala',
        'Wawo',
        'Watunohu',
    ];

    public const STATUS_BELUM_DIDATA = 'belum_didata';
    public const STATUS_SEDANG_DIDATA = 'sedang_didata';
    public const STATUS_SELESAI = 'selesai';

    protected $fillable = [
        'name',
        'family_head_name',
        'informant_name',
        'respondent_status',
        'address',
        'district',
        'phone',
        'agency',
        'purpose',
        'progress_status',
        'assignment_id',
        'officer_id',
        'input_date',
    ];

    public function getProgressStatusLabelAttribute(): string
    {
        return match ($this->progress_status) {
            'sedang_didata' => 'Sedang Didata',
            'selesai' => 'Selesai Didata',
            default => 'Belum Didata',
        };
    }

    public function getProgressStatusBadgeAttribute(): string
    {
        return match ($this->progress_status) {
            'sedang_didata' => '<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Sedang Didata</span>',
            'selesai' => '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Selesai Didata</span>',
            default => '<span class="badge bg-secondary"><i class="bi bi-clock me-1"></i>Belum Didata</span>',
        };
    }

    public function getHeadOfFamilyAttribute(): string
    {
        return $this->family_head_name ?: $this->name;
    }

    public function getInformantDisplayNameAttribute(): string
    {
        return $this->informant_name ?: $this->name;
    }

    protected function casts(): array
    {
        return [
            'input_date' => 'date',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class, 'assignment_id');
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id')->withTrashed();
    }

    public function basts(): HasMany
    {
        return $this->hasMany(RespondentBast::class, 'respondent_id');
    }
}

