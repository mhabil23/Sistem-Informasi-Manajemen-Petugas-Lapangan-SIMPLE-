<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Assignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'location',
        'basis_dipa',
        'st_number',
        'st_date',
        'basis_st',
        'target_respondents',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'st_date' => 'date',
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
                    ->withTrashed()
                    ->withPivot(['id', 'specific_task', 'activity_role', 'status', 'progress_notes'])
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

    /**
     * Get a map of assignment_id => array of active involved officers ['id', 'name'].
     * Combines officers from assignment_officers and respondents table.
     */
    public static function getInvolvedOfficersMap(): array
    {
        $assignments = static::orderBy('title')->get();

        $pivotOfficers = DB::table('assignment_officers')
            ->join('users', 'assignment_officers.user_id', '=', 'users.id')
            ->whereNull('users.deleted_at')
            ->where('users.status', 'active')
            ->select('assignment_officers.assignment_id', 'users.id', 'users.name', 'assignment_officers.activity_role')
            ->get();

        $respOfficers = DB::table('respondents')
            ->join('users', 'respondents.officer_id', '=', 'users.id')
            ->whereNull('users.deleted_at')
            ->where('users.status', 'active')
            ->whereNotNull('respondents.assignment_id')
            ->select('respondents.assignment_id', 'users.id', 'users.name', DB::raw("'PCL' as activity_role"))
            ->distinct()
            ->get();

        $map = [];
        foreach ($assignments as $a) {
            $map[$a->id] = [];
        }

        foreach ($pivotOfficers->concat($respOfficers)->unique(fn($item) => $item->assignment_id . '_' . $item->id)->sortBy('name') as $row) {
            if (isset($map[$row->assignment_id])) {
                $roleLabel = !empty($row->activity_role) ? ' (' . $row->activity_role . ')' : ' (PCL)';
                $map[$row->assignment_id][] = [
                    'id' => (int) $row->id,
                    'name' => $row->name . $roleLabel,
                    'activity_role' => $row->activity_role ?? 'PCL',
                ];
            }
        }

        return $map;
    }
}

