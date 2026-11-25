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
        'nik',
        'sobat_id',
        'rank',
        'position',
        'district',
        'village',
        'address',
        'gender',
        'birth_info',
        'education',
        'password',
        'role',
        'can_switch_role',
        'is_master_admin',
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
            'can_switch_role' => 'boolean',
            'is_master_admin' => 'boolean',
        ];
    }

    // Role & Dual Role Capabilities
    public function isMasterAdmin(): bool
    {
        return (bool) ($this->is_master_admin ?? false)
            || $this->username === 'admin'
            || $this->email === 'admin@simple.go.id'
            || $this->name === 'Administrator SIMPLE';
    }

    public function canSwitchRole(): bool
    {
        return $this->role === 'admin' || (bool) $this->can_switch_role;
    }

    public function hasAdminPrivileges(): bool
    {
        return $this->role === 'admin' || (bool) $this->can_switch_role;
    }

    public function activeRole(): string
    {
        if (!$this->canSwitchRole()) {
            return $this->role;
        }

        $sessionRole = session('active_role');
        if ($sessionRole && in_array($sessionRole, ['admin', 'pegawai', 'petugas'], true)) {
            return $sessionRole;
        }

        return $this->role;
    }

    public function isAdmin(): bool
    {
        return $this->activeRole() === 'admin';
    }

    public function isPetugas(): bool
    {
        return $this->activeRole() === 'petugas';
    }

    public function isPegawai(): bool
    {
        return $this->activeRole() === 'pegawai';
    }

    public function isFieldStaff(): bool
    {
        return in_array($this->activeRole(), ['petugas', 'pegawai'], true);
    }

    public function canAccessMonitoring(): bool
    {
        return in_array($this->activeRole(), ['admin', 'pegawai'], true);
    }

    public function roleLabel(): string
    {
        if ($this->isMasterAdmin() && $this->activeRole() === 'admin') {
            return 'Master Admin';
        }

        return match ($this->activeRole()) {
            'admin' => 'Administrator',
            'pegawai' => 'Pegawai / PML',
            default => 'Petugas',
        };
    }

    public function scopeFieldStaff($query)
    {
        return $query->whereIn('role', ['petugas', 'pegawai']);
    }

    public function getFieldRoleAttribute(): string
    {
        if ($this->role === 'pegawai') {
            return 'PML';
        }

        if (!empty($this->position) && (stripos($this->position, 'pemetaan') !== false || stripos($this->position, 'ppl') !== false)) {
            return 'PPL';
        }

        return 'PCL';
    }

    public function getFieldRoleLabelAttribute(): string
    {
        return match ($this->field_role) {
            'PML' => 'PML (Pengawas / Pemeriksa Lapangan)',
            'PPL' => 'PPL (Petugas Pendata Lapangan)',
            default => 'PCL (Petugas Pencacah Lapangan)',
        };
    }

    /**
     * Dapatkan daftar seluruh pegawai BPS / penandatangan berkas lengkap dengan nama, NIP, jabatan, dan pangkat.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getPegawaiSigners(): \Illuminate\Support\Collection
    {
        $list = collect();

        // 1. Baca dari database/data/pegawai_bps.json (data master pegawai BPS Kolut)
        $jsonPath = database_path('data/pegawai_bps.json');
        if (file_exists($jsonPath)) {
            $data = json_decode(file_get_contents($jsonPath), true);
            if (is_array($data)) {
                foreach ($data as $item) {
                    $name = trim($item['name'] ?? '');
                    $nip = trim($item['nip'] ?? '');
                    if ($name !== '') {
                        $list->push([
                            'name' => $name,
                            'nip' => $nip,
                            'position' => $item['position'] ?? 'Pegawai BPS',
                            'rank' => $item['rank'] ?? '-',
                        ]);
                    }
                }
            }
        }

        // 2. Ambil dari database users (role pegawai, admin, atau yang memiliki NIP/NIK panjang)
        $dbUsers = static::where(function ($q) {
                $q->whereIn('role', ['pegawai', 'admin'])
                  ->orWhereNotNull('nik');
            })
            ->where('status', 'active')
            ->get(['name', 'nik', 'position', 'rank']);

        foreach ($dbUsers as $user) {
            $name = trim($user->name);
            $nip = trim($user->nik ?? '');
            $exists = $list->first(function ($item) use ($name, $nip) {
                return strtolower(trim($item['name'])) === strtolower($name)
                    || (!empty($nip) && !empty($item['nip']) && $item['nip'] === $nip);
            });

            if (!$exists && !empty($name)) {
                $list->push([
                    'name' => $name,
                    'nip' => $nip,
                    'position' => $user->position ?? 'Pegawai BPS',
                    'rank' => $user->rank ?? '-',
                ]);
            }
        }

        return $list->sortBy('name')->values();
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

    public function assignments(): BelongsToMany
    {
        return $this->assignedTasks();
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

    public function officerNotices(): HasMany
    {
        return $this->hasMany(OfficerNotice::class, 'user_id');
    }
}
