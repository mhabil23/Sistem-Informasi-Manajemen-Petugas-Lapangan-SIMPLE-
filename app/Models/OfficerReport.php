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
        'st_number',
        'st_date',
        'destination_purpose',
        'duration_text',
        'signing_city',
        'signing_date',
        'signature_path',
        'include_vehicle_statement',
        'expense_items',
        'ppk_name',
        'ppk_nip',
        'assignment_id',
        'activity_date',
        'activity_title',
        'activity_location',
        'work_description',
        'obstacles',
        'solutions',
        'recommendations',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $appends = [
        'status_badge',
        'status_label',
        'display_location',
        'resolved_officer_name',
        'resolved_officer_nik',
        'resolved_st_text',
        'resolved_expense_items',
        'total_expense',
        'total_expense_terbilang',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
            'signing_date' => 'date',
            'st_date' => 'date',
            'reviewed_at' => 'datetime',
            'include_vehicle_statement' => 'boolean',
            'expense_items' => 'array',
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

    public function getDisplayLocationAttribute(): string
    {
        if (!empty($this->activity_location)) {
            return $this->activity_location;
        }

        if ($this->assignment && !empty($this->assignment->location)) {
            return $this->assignment->location;
        }

        return 'Kabupaten Kolaka Utara';
    }

    public function getResolvedOfficerNameAttribute(): string
    {
        return $this->officer_name ?: ($this->user?->name ?: '-');
    }

    public function getResolvedOfficerNikAttribute(): string
    {
        if (!empty($this->officer_nip) && $this->officer_nip !== '-') {
            return $this->officer_nip;
        }

        return $this->user?->nik ?: '-';
    }

    public function getResolvedStTextAttribute(): string
    {
        if (!empty($this->st_number)) {
            $stDateStr = $this->st_date
                ? $this->st_date->translatedFormat('d F Y')
                : ($this->activity_date ? $this->activity_date->translatedFormat('d F Y') : date('d F Y'));

            return "Surat Tugas Nomor {$this->st_number} Tanggal {$stDateStr}";
        }

        if (!empty($this->basis_st)) {
            return rtrim($this->basis_st, '.');
        }

        $year = $this->activity_date ? $this->activity_date->format('Y') : date('Y');
        $dateStr = $this->activity_date ? $this->activity_date->translatedFormat('d F Y') : date('d F Y');

        return "Surat Tugas Nomor B-1812/74080/HM.300/{$year} Tanggal {$dateStr}";
    }

    public function getResolvedExpenseItemsAttribute(): array
    {
        if (!empty($this->expense_items) && is_array($this->expense_items)) {
            $items = array_values(array_filter($this->expense_items, function ($item) {
                return !empty($item['description']) || (isset($item['amount']) && (int) $item['amount'] > 0);
            }));
            if (!empty($items)) {
                return array_map(fn ($item) => [
                    'description' => $item['description'] ?? '-',
                    'amount' => (int) ($item['amount'] ?? 0),
                ], $items);
            }
        }

        $origin = $this->display_location ?: 'Lokasi Tugas';
        return [
            [
                'description' => "Biaya transport dari {$origin} ke Kantor BPS Kabupaten Kolaka Utara (P.P.) selama 1 hari",
                'amount' => 50000,
            ],
        ];
    }

    public function getTotalExpenseAttribute(): int
    {
        return array_sum(array_column($this->resolved_expense_items, 'amount'));
    }

    public function getTotalExpenseTerbilangAttribute(): string
    {
        return self::terbilang($this->total_expense) . ' Rupiah';
    }

    public static function terbilang(int $angka): string
    {
        $angka = abs($angka);
        if ($angka === 0) {
            return 'Nol';
        }

        $bilangan = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];

        if ($angka < 12) {
            return $bilangan[$angka];
        }
        if ($angka < 20) {
            return $bilangan[$angka - 10] . ' Belas';
        }
        if ($angka < 100) {
            $rest = $angka % 10;
            return $bilangan[(int) ($angka / 10)] . ' Puluh' . ($rest ? ' ' . self::terbilang($rest) : '');
        }
        if ($angka < 200) {
            $rest = $angka - 100;
            return 'Seratus' . ($rest ? ' ' . self::terbilang($rest) : '');
        }
        if ($angka < 1000) {
            $rest = $angka % 100;
            return $bilangan[(int) ($angka / 100)] . ' Ratus' . ($rest ? ' ' . self::terbilang($rest) : '');
        }
        if ($angka < 2000) {
            $rest = $angka - 1000;
            return 'Seribu' . ($rest ? ' ' . self::terbilang($rest) : '');
        }
        if ($angka < 1000000) {
            $rest = $angka % 1000;
            return self::terbilang((int) ($angka / 1000)) . ' Ribu' . ($rest ? ' ' . self::terbilang($rest) : '');
        }
        if ($angka < 1000000000) {
            $rest = $angka % 1000000;
            return self::terbilang((int) ($angka / 1000000)) . ' Juta' . ($rest ? ' ' . self::terbilang($rest) : '');
        }

        $rest = $angka % 1000000000;
        return self::terbilang((int) ($angka / 1000000000)) . ' Miliar' . ($rest ? ' ' . self::terbilang($rest) : '');
    }
}

