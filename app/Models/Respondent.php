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

    public const PROCESSING_BELUM_DITERIMA = 'belum_diterima';
    public const PROCESSING_RECEIVING = 'receiving';
    public const PROCESSING_EDITING_CODING = 'editing_coding';
    public const PROCESSING_ENTRY = 'entry';
    public const PROCESSING_CLEAN = 'clean';

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
        'processing_status',
        'received_at',
        'edited_at',
        'entered_at',
        'cleaned_at',
        'assignment_id',
        'officer_id',
        'input_date',
    ];

    protected $casts = [
        'input_date' => 'date',
        'received_at' => 'datetime',
        'edited_at' => 'datetime',
        'entered_at' => 'datetime',
        'cleaned_at' => 'datetime',
    ];

    public function getProcessingStatusLabelAttribute(): string
    {
        return match ($this->processing_status) {
            'receiving' => 'Penerimaan (Receiving)',
            'editing_coding' => 'Editing & Coding (E/C)',
            'entry' => 'Perekaman Data (Entry)',
            'clean' => 'Validasi Bersih (Clean Data)',
            default => 'Belum Diterima Kantor',
        };
    }

    public function getProcessingStatusBadgeAttribute(): string
    {
        return match ($this->processing_status) {
            'receiving' => '<span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-0.5"><i class="bi bi-inbox-fill me-1"></i>Receiving</span>',
            'editing_coding' => '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5"><i class="bi bi-pencil-fill me-1"></i>Editing &amp; Coding</span>',
            'entry' => '<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5"><i class="bi bi-laptop me-1"></i>Data Entry</span>',
            'clean' => '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5"><i class="bi bi-patch-check-fill me-1"></i>Clean Data</span>',
            default => '<span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5"><i class="bi bi-clock me-1"></i>Belum Diterima</span>',
        };
    }

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
            'sedang_didata' => '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5 text-nowrap" style="font-size: 0.68rem;"><i class="bi bi-hourglass-split me-1"></i>Sedang Didata</span>',
            'selesai' => '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 text-nowrap" style="font-size: 0.68rem;"><i class="bi bi-check2-circle me-1"></i>Selesai Didata</span>',
            default => '<span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5 text-nowrap" style="font-size: 0.68rem;"><i class="bi bi-clock me-1"></i>Belum Didata</span>',
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

    /**
     * Dapatkan pengawas / PML yang membawahi penugasan / wilayah responden ini.
     */
    public function getPmlOfficerAttribute(): ?User
    {
        // 1. Jika petugas responden itu sendiri adalah pegawai / PML
        if ($this->officer && ($this->officer->role === 'pegawai' || $this->officer->field_role === 'PML')) {
            return $this->officer;
        }

        // 2. Ambil dari kegiatan terkait (AssignmentOfficer)
        if ($this->assignment) {
            $assignmentOfficers = $this->assignment->relationLoaded('assignmentOfficers')
                ? $this->assignment->assignmentOfficers
                : $this->assignment->assignmentOfficers()->with('user')->get();

            $pmls = $assignmentOfficers->filter(function ($ao) {
                return $ao->activity_role === 'PML' || ($ao->user && $ao->user->role === 'pegawai');
            });

            if ($pmls->isNotEmpty()) {
                // Cari PML dengan wilayah/kecamatan yang cocok dengan responden
                if ($this->district) {
                    $matched = $pmls->first(function ($ao) {
                        return $ao->user && !empty($ao->user->district) && (
                            strcasecmp($ao->user->district, $this->district) === 0 ||
                            stripos($this->district, $ao->user->district) !== false ||
                            stripos($ao->user->district, $this->district) !== false
                        );
                    });
                    if ($matched && $matched->user) {
                        return $matched->user;
                    }
                }

                // Default ke PML pertama pada kegiatan tersebut
                $first = $pmls->first();
                if ($first && $first->user) {
                    return $first->user;
                }
            }
        }

        // 3. Fallback: jika penugasan belum memiliki PML, cari pegawai pembina wilayah kecamatan responden
        if ($this->district) {
            $districtPml = User::where('role', 'pegawai')
                ->where(function ($q) {
                    $q->where('district', $this->district)
                      ->orWhere('district', 'like', "%{$this->district}%");
                })
                ->first();
            if ($districtPml) {
                return $districtPml;
            }
        }

        return null;
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

