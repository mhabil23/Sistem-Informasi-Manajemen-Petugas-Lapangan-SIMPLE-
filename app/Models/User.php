<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Role & Status Checks
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPetugas(): bool
    {
        return $this->role === 'petugas';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // Relationships
    public function createdAssignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'created_by');
    }

    public function assignedTasks(): BelongsToMany
    {
        return $this->belongsToMany(Assignment::class, 'assignment_officers', 'user_id', 'assignment_id')
                    ->withPivot(['id', 'specific_task', 'status', 'progress_notes'])
                    ->withTimestamps();
    }

    public function assignmentOfficers(): HasMany
    {
        return $this->hasMany(AssignmentOfficer::class, 'user_id');
    }

    public function respondents(): HasMany
    {
        return $this->hasMany(Respondent::class, 'officer_id');
    }

    public function respondentBasts(): HasMany
    {
        return $this->hasMany(RespondentBast::class, 'officer_id');
    }

    public function officerBasts(): HasMany
    {
        return $this->hasMany(OfficerBast::class, 'officer_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(OfficerReport::class, 'user_id');
    }

    public function reviewedReports(): HasMany
    {
        return $this->hasMany(OfficerReport::class, 'reviewed_by');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'officer_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }
}
