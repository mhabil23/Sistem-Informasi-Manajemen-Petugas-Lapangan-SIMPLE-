<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class OfficerBast extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'bast_number',
        'bast_date',
        'assignment_id',
        'officer_id',
        'bast_type', // 'kantor_ke_petugas' (Blanko & Perlengkapan) atau 'petugas_ke_kantor' (Dokumen Terisi)
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
}

