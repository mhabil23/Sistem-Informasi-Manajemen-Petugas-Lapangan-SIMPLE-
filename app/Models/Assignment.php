<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'location',
        'target_respondents',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'target_respondents' => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function officers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'assignment_officers', 'assignment_id', 'user_id')
                    ->withPivot(['id', 'specific_task', 'status', 'progress_notes'])
                    ->withTimestamps();
    }

    public function assignmentOfficers(): HasMany
    {
        return $this->hasMany(AssignmentOfficer::class, 'assignment_id');
    }

    public function respondents(): HasMany
    {
        return $this->hasMany(Respondent::class, 'assignment_id');
    }

    public function respondentBasts(): HasMany
    {
        return $this->hasMany(RespondentBast::class, 'assignment_id');
    }

    public function officerBasts(): HasMany
    {
        return $this->hasMany(OfficerBast::class, 'assignment_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(OfficerReport::class, 'assignment_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'assignment_id');
    }
}

