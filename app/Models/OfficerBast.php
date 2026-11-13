<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class OfficerBast extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'bast_number',
        'bast_date',
        'assignment_id',
        'officer_id',
        'bast_type', // 'kantor_ke_petugas' (Tanda Terima Petugas) atau 'petugas_ke_kantor' (BAST Dokumen Terisi)
        'receipt_category', // 'PELATIHAN' | 'ATK' | 'KUES' | 'TIMBANGAN'
        'description',
        'document_title',
        'activity_subtitle',
        'institution_name',
        'document_place',
        'maker_name',
        'maker_nip',
        'notes',
        'file_path',
        'status',
    ];

    public function getTypeLabelAttribute(): string
    {
        return $this->bast_type === 'petugas_ke_kantor'
            ? 'Dokumen Terisi (Petugas ke Kantor)'
            : 'Blanko & Perlengkapan (Kantor ke Petugas)';
    }

    public function getReceiptCategoryLabelAttribute(): string
    {
        return match (strtoupper($this->receipt_category ?? '')) {
            'PELATIHAN' => 'Perlengkapan Pelatihan',
            'ATK' => 'ATK Pendataan',
            'KUES' => 'Kuesioner Pendataan',
            'TIMBANGAN' => 'Timbangan',
            default => 'Tanda Terima Petugas',
        };
    }

    public function isKantorKePetugas(): bool
    {
        return $this->bast_type !== 'petugas_ke_kantor';
    }

    public function isPetugasKeKantor(): bool
    {
        return $this->bast_type === 'petugas_ke_kantor';
    }

    protected function casts(): array
    {
        return [
            'bast_date' => 'date',
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

    public function recipients(): HasMany
    {
        return $this->hasMany(OfficerBastRecipient::class, 'officer_bast_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function generatedDocuments(): MorphMany
    {
        return $this->morphMany(GeneratedDocument::class, 'documentable');
    }

    public function scopeForOfficer($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('officer_id', $userId)
              ->orWhereHas('recipients', fn ($rq) => $rq->where('user_id', $userId));
        });
    }

    public function includesOfficer(int $userId): bool
    {
        if ((int) $this->officer_id === $userId) {
            return true;
        }

        if ($this->relationLoaded('recipients')) {
            return $this->recipients->contains(fn ($r) => (int) $r->user_id === $userId);
        }

        return $this->recipients()->where('user_id', $userId)->exists();
    }

    public function recipientForUser(?int $userId, ?string $userName = null): ?OfficerBastRecipient
    {
        $recipients = $this->relationLoaded('recipients')
            ? $this->recipients
            : $this->recipients()->get();

        if ($userId) {
            $found = $recipients->first(fn ($r) => (int) $r->user_id === $userId);
            if ($found) {
                return $found;
            }
        }

        if (!empty($userName)) {
            $normalized = strtolower(trim($userName));
            $found = $recipients->first(fn ($r) => strtolower(trim($r->recipient_name)) === $normalized);
            if ($found) {
                return $found;
            }
        }

        return null;
    }

    public function isSignedByOfficer(int $userId, ?string $userName = null): bool
    {
        $rec = $this->recipientForUser($userId, $userName);
        return $rec && !empty($rec->signature_path);
    }

    public function signedRecipientsCount(): int
    {
        $recipients = $this->relationLoaded('recipients')
            ? $this->recipients
            : $this->recipients()->get();

        return $recipients->filter(fn ($r) => !empty($r->signature_path))->count();
    }

    public static function generateBastNumber(string $bastType): string
    {
        $year = now()->format('Y');
        $romanMonths = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $month = $romanMonths[now()->month - 1];
        $count = static::whereYear('created_at', now()->year)->count() + 1;
        $prefix = $bastType === 'petugas_ke_kantor' ? 'BAST-TERISI' : 'BAST-BLANKO';

        return sprintf('%s/%s/%s/%04d', $prefix, $year, $month, $count);
    }

    public function resolvedRecipients(): Collection
    {
        $items = $this->relationLoaded('recipients')
            ? $this->recipients
            : $this->recipients()->get();

        if ($items->isNotEmpty()) {
            return $items;
        }

        if ($this->officer) {
            return collect([(object) [
                'user_id' => $this->officer->id,
                'recipient_name' => $this->officer->name,
                'position_unit' => $this->officer->position ?: ($this->officer->isPegawai() ? 'Pegawai BPS' : 'Mitra'),
                'origin_role' => $this->assignment?->location ?? '-',
            ]]);
        }

        return collect();
    }

    public function getParsedItemsAttribute(): array
    {
        $lines = preg_split('/\r\n|\r|\n/', (string) $this->description) ?: [];
        $parsed = [];

        foreach ($lines as $line) {
            $clean = trim($line);
            if ($clean === '') {
                continue;
            }
            $clean = preg_replace('/^(?:(?:\d+[\.\)]\s*)|(?:[-*•]\s*))/', '', $clean);
            $clean = trim((string) $clean);
            if ($clean !== '') {
                $parsed[] = $clean;
            }
        }

        return $parsed;
    }

    public function getPlaceDateLineAttribute(): string
    {
        $place = trim((string) ($this->document_place ?: ($this->assignment?->location ?: 'Lasusua')));
        $dateStr = $this->bast_date
            ? $this->bast_date->translatedFormat('j F Y')
            : now()->translatedFormat('j F Y');

        return $place !== '' ? "{$place}, {$dateStr}" : $dateStr;
    }

    public function getResolvedMakerNipAttribute(): string
    {
        $nip = trim((string) $this->maker_nip);
        if ($nip === '') {
            return '';
        }

        return preg_replace('/^NIP\.?\s*/i', '', $nip) ?: '';
    }

    public function getRecipientSummaryAttribute(): string
    {
        $recipients = $this->resolvedRecipients();
        if ($recipients->isEmpty()) {
            return '-';
        }

        $firstName = $recipients->first()->recipient_name ?? '-';
        $total = $recipients->count();

        return $total > 1 ? "{$firstName} ({$total} penerima)" : $firstName;
    }

    public function pdfViewName(): string
    {
        return $this->isKantorKePetugas() ? 'pdf.bast_blanko' : 'pdf.bast_officer';
    }

    public function pdfFilename(): string
    {
        $prefix = $this->isKantorKePetugas() ? 'BAST-Blanko-' : 'BAST-Petugas-';

        return $prefix . str_replace(['/', '\\'], '-', $this->bast_number) . '.pdf';
    }

    /**
     * Validasi dan normalisasi atribut BAST Blanko & Perlengkapan beserta daftar penerimanya.
     * Digunakan bersama oleh portal Admin dan Petugas/Pegawai.
     */
    public static function prepareCollectivePayload(
        Request $request,
        Assignment $assignment,
        string $bastDate,
        ?User $fallbackOfficer = null,
        string $defaultMakerName = 'Koordinator Logistik BPS',
        string $defaultMakerNip = ''
    ): array {
        $cleanRecipients = [];
        foreach ($request->input('recipients', []) as $row) {
            $name = trim((string) ($row['recipient_name'] ?? ''));
            $userId = !empty($row['user_id']) ? (int) $row['user_id'] : null;

            if ($name === '' && $userId) {
                $user = User::find($userId);
                $name = $user?->name ?? '';
            }
            if ($name === '') {
                continue;
            }

            $pos = trim((string) ($row['position_unit'] ?? ''));
            $origin = trim((string) ($row['origin_role'] ?? ''));

            $cleanRecipients[] = [
                'user_id' => $userId,
                'recipient_name' => $name,
                'position_unit' => $pos !== '' ? $pos : 'Mitra',
                'origin_role' => $origin !== '' ? $origin : ($assignment->location ?? '-'),
                'sort_order' => count($cleanRecipients) + 1,
            ];
        }

        $singleOfficerId = $request->input('officer_id');
        if (empty($cleanRecipients) && $singleOfficerId) {
            $fallbackOfficer = User::find($singleOfficerId);
        }

        if (empty($cleanRecipients) && $fallbackOfficer) {
            $cleanRecipients[] = [
                'user_id' => $fallbackOfficer->id,
                'recipient_name' => $fallbackOfficer->name,
                'position_unit' => $fallbackOfficer->position ?: ($fallbackOfficer->isPegawai() ? 'Pegawai BPS' : 'Mitra'),
                'origin_role' => $assignment->location ?? '-',
                'sort_order' => 1,
            ];
        }

        if (empty($cleanRecipients)) {
            throw ValidationException::withMessages([
                'recipients' => ['Minimal 1 petugas / peserta penerima blanko & perlengkapan wajib ditambahkan.'],
            ]);
        }

        $cleanItems = [];
        foreach ($request->input('items', []) as $itemText) {
            $trimmed = trim((string) $itemText);
            if ($trimmed !== '') {
                $cleanItems[] = $trimmed;
            }
        }

        $descriptionText = !empty($cleanItems)
            ? implode("\n", $cleanItems)
            : trim((string) $request->input('description', ''));

        if ($descriptionText === '') {
            throw ValidationException::withMessages([
                'items' => ['Minimal 1 rincian barang / blanko perlengkapan wajib diisi.'],
            ]);
        }

        $year = date('Y', strtotime($bastDate));
        $category = strtoupper(trim((string) $request->input('receipt_category', 'PELATIHAN')));
        if (!in_array($category, ['PELATIHAN', 'ATK', 'KUES', 'TIMBANGAN'], true)) {
            $category = 'PELATIHAN';
        }

        $defaultTitle = match ($category) {
            'PELATIHAN' => 'Tanda Terima Perlengkapan Pelatihan',
            'ATK' => 'Tanda Terima ATK Pendataan',
            'KUES' => 'Tanda Terima Kuesioner Pendataan',
            'TIMBANGAN' => 'Tanda Terima Timbangan Petugas',
            default => 'Tanda Terima Petugas',
        };

        $bastAttributes = [
            'assignment_id' => $assignment->id,
            'officer_id' => $cleanRecipients[0]['user_id'] ?? null,
            'bast_type' => 'kantor_ke_petugas',
            'receipt_category' => $category,
            'bast_date' => $bastDate,
            'description' => $descriptionText,
            'document_title' => trim((string) $request->input('document_title', '')) ?: $defaultTitle,
            'activity_subtitle' => trim((string) $request->input('activity_subtitle', '')) ?: $assignment->title,
            'institution_name' => trim((string) $request->input('institution_name', '')) ?: "BPS Kabupaten Kolaka Utara Tahun {$year}",
            'document_place' => trim((string) $request->input('document_place', '')) ?: ($assignment->location ?: 'Lasusua'),
            'maker_name' => trim((string) $request->input('maker_name', '')) ?: $defaultMakerName,
            'maker_nip' => trim((string) $request->input('maker_nip', '')) ?: $defaultMakerNip,
        ];

        return [$bastAttributes, $cleanRecipients];
    }

    public function syncRecipients(array $recipients): void
    {
        $this->recipients()->delete();

        if (!empty($recipients)) {
            $this->recipients()->createMany($recipients);
        }
    }
}

