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
        'assignment_id',
        'officer_id',
        'input_date',
    ];

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
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function basts(): HasMany
    {
        return $this->hasMany(RespondentBast::class, 'respondent_id');
    }
}

