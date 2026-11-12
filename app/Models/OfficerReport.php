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

    public const LOCAL_TIMEZONE = 'Asia/Makassar';

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
        'resolved_signing_date',
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

    /**
     * Resolve local date string for automatic report input/submission timestamp.
     */
    public static function currentInputDate(): string
    {
        return now(self::LOCAL_TIMEZONE)->toDateString();
    }

    /**
     * Use explicit signing date if present, otherwise default to the date the report was inputted.
     */
    public function getResolvedSigningDateAttribute(): string
    {
        if ($this->signing_date) {
            return $this->signing_date->toDateString();
        }

        if ($this->created_at) {
            return $this->created_at->copy()->timezone(self::LOCAL_TIMEZONE)->toDateString();
        }

        return self::currentInputDate();
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

    public static function formatBasisSt(?string $stNumber, ?string $stDate): string
    {
        $num = trim((string) $stNumber) ?: ('B-1812/74080/HM.300/' . date('Y'));
        try {
            $dateStr = !empty($stDate)
                ? \Carbon\Carbon::parse($stDate)->translatedFormat('d F Y')
                : now()->translatedFormat('d F Y');
        } catch (\Throwable $e) {
            $dateStr = now()->translatedFormat('d F Y');
        }

        return "Surat Tugas Kepala Badan Pusat Statistik Kabupaten Kolaka Utara Nomor {$num} Tanggal {$dateStr}.";
    }

    public static function getMasterBasisSettings(): array
    {
        $year = date('Y');
        $defaults = [
            'basis_dipa' => "DIPA Satuan Kerja Badan Pusat Statistik Kabupaten Kolaka Utara Tahun Anggaran {$year} Nomor SP DIPA-054.01.2.667477/{$year} tanggal 1 Desember " . ($year - 1) . ".",
            'st_number' => "B-1812/74080/HM.300/{$year}",
            'st_date' => date('Y-m-d'),
            'basis_st' => self::formatBasisSt("B-1812/74080/HM.300/{$year}", date('Y-m-d')),
        ];

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('app_settings')) {
                $raw = \Illuminate\Support\Facades\DB::table('app_settings')
                    ->where('key', 'perjadin_basis_defaults')
                    ->value('value');
                if ($raw) {
                    $decoded = json_decode($raw, true);
                    if (is_array($decoded)) {
                        $merged = array_merge($defaults, array_filter($decoded, fn ($v) => $v !== null && $v !== ''));
                        if (empty($decoded['basis_st']) && (!empty($decoded['st_number']) || !empty($decoded['st_date']))) {
                            $merged['basis_st'] = self::formatBasisSt($merged['st_number'], $merged['st_date']);
                        }
                        return $merged;
                    }
                }
            }
        } catch (\Throwable $e) {
            // Fallback to defaults if migrations are mid-flight
        }

        return $defaults;
    }

    public static function saveMasterBasisSettings(array $data): array
    {
        $payload = [
            'basis_dipa' => trim((string) ($data['basis_dipa'] ?? '')),
            'st_number' => trim((string) ($data['st_number'] ?? '')),
            'st_date' => !empty($data['st_date']) ? substr((string) $data['st_date'], 0, 10) : date('Y-m-d'),
            'basis_st' => trim((string) ($data['basis_st'] ?? '')),
        ];

        if ($payload['basis_st'] === '' && $payload['st_number'] !== '') {
            $payload['basis_st'] = self::formatBasisSt($payload['st_number'], $payload['st_date']);
        }

        \Illuminate\Support\Facades\DB::table('app_settings')->updateOrInsert(
            ['key' => 'perjadin_basis_defaults'],
            [
                'value' => json_encode($payload, JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return $payload;
    }

    public static function resolveAuthoritativeBasis(?Assignment $assignment = null, ?string $fallbackDate = null): array
    {
        $master = self::getMasterBasisSettings();

        $basisDipa = ($assignment && !empty($assignment->basis_dipa))
            ? $assignment->basis_dipa
            : $master['basis_dipa'];

        $stNumber = ($assignment && !empty($assignment->st_number))
            ? $assignment->st_number
            : $master['st_number'];

        $stDate = ($assignment && !empty($assignment->st_date))
            ? $assignment->st_date->format('Y-m-d')
            : (!empty($master['st_date']) ? $master['st_date'] : ($fallbackDate ?: date('Y-m-d')));

        if ($assignment && !empty($assignment->basis_st)) {
            $basisSt = $assignment->basis_st;
        } elseif ($assignment && (!empty($assignment->st_number) || !empty($assignment->st_date))) {
            $basisSt = self::formatBasisSt($stNumber, $stDate);
        } elseif (!empty($master['basis_st'])) {
            $basisSt = $master['basis_st'];
        } else {
            $basisSt = self::formatBasisSt($stNumber, $stDate);
        }

        return [
            'basis_dipa' => $basisDipa,
            'st_number' => $stNumber,
            'st_date' => $stDate,
            'basis_st' => $basisSt,
        ];
    }

    public function canBeDownloadedByOfficer(?User $actor = null): bool
    {
        $owner = $actor ?? $this->user;

        return $this->status === 'approved' || (bool) $owner?->isPegawai();
    }

    public static function buildExpenseItems(array $descriptions, array $amounts): array
    {
        $items = [];

        foreach ($descriptions as $idx => $desc) {
            $cleanDesc = trim((string) $desc);
            $amount = isset($amounts[$idx]) ? (int) preg_replace('/[^0-9]/', '', (string) $amounts[$idx]) : 0;
            if ($cleanDesc !== '' || $amount > 0) {
                $items[] = [
                    'description' => $cleanDesc ?: 'Biaya transport perjalanan dinas',
                    'amount' => $amount,
                ];
            }
        }

        return $items;
    }

    public function deleteAttachmentsByIds(array $ids): void
    {
        $cleanIds = array_filter($ids);
        if (empty($cleanIds)) {
            return;
        }

        $attachments = ReportAttachment::where('report_id', $this->id)
            ->whereIn('id', $cleanIds)
            ->get();

        foreach ($attachments as $att) {
            if ($att->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($att->file_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($att->file_path);
            }
            $att->delete();
        }
    }

    public function notifyOfficerDecision(?int $adminId = null): void
    {
        if (!$this->user_id || !in_array($this->status, ['approved', 'revision_needed'], true)) {
            return;
        }

        $title = $this->status === 'approved'
            ? "Laporan Perjadin Disetujui (ACC): {$this->activity_title}"
            : "Perlu Revisi Laporan Perjadin: {$this->activity_title}";

        $message = $this->status === 'approved'
            ? "Laporan perjalanan dinas '{$this->activity_title}' ({$this->report_number}) telah di-ACC oleh Administrator. File PDF resmi sekarang sudah dapat diunduh."
            : "Laporan perjalanan dinas '{$this->activity_title}' ({$this->report_number}) dikembalikan Administrator untuk diperbaiki."
                . ($this->admin_notes ? " Catatan Admin: {$this->admin_notes}" : '');

        OfficerNotice::create([
            'user_id' => $this->user_id,
            'sender_id' => $adminId ?? \Illuminate\Support\Facades\Auth::id(),
            'type' => $this->status === 'approved' ? 'report_approved' : 'report_revision',
            'title' => $title,
            'message' => $message,
            'is_read' => false,
        ]);
    }
}

