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
        'maker_signature_path',
        'first_party_position',
        'first_party_address',
        'second_party_nik',
        'second_party_address',
        'contract_number',
        'contract_date',
        'doc_count',
        'activity_work_name',
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
            'PELATIHAN', 'PERLENGKAPAN' => 'Perlengkapan Petugas',
            'ATK' => 'ATK Pendataan',
            'KUES' => 'Kuesioner Pendataan',
            'ATK_RESPONDEN', 'RESPONDEN' => 'ATK & Dokumen Responden',
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
            'contract_date' => 'date',
        ];
    }

    public static function terbilang(int $number): string
    {
        $bilangan = [
            '', 'satu', 'dua', 'tiga', 'empat', 'lima',
            'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'
        ];

        if ($number < 12) {
            return $bilangan[$number];
        } elseif ($number < 20) {
            return self::terbilang($number - 10) . ' belas';
        } elseif ($number < 100) {
            $hasil = self::terbilang((int) ($number / 10)) . ' puluh';
            $sisa = $number % 10;
            if ($sisa > 0) {
                $hasil .= ' ' . self::terbilang($sisa);
            }
            return $hasil;
        } elseif ($number < 200) {
            return 'seratus ' . self::terbilang($number - 100);
        } elseif ($number < 1000) {
            $hasil = self::terbilang((int) ($number / 100)) . ' ratus';
            $sisa = $number % 100;
            if ($sisa > 0) {
                $hasil .= ' ' . self::terbilang($sisa);
            }
            return $hasil;
        } elseif ($number < 2000) {
            return 'seribu ' . self::terbilang($number - 1000);
        } elseif ($number < 1000000) {
            $hasil = self::terbilang((int) ($number / 1000)) . ' ribu';
            $sisa = $number % 1000;
            if ($sisa > 0) {
                $hasil .= ' ' . self::terbilang($sisa);
            }
            return $hasil;
        }

        return (string) $number;
    }

    public function getSpelledDatePhraseAttribute(): string
    {
        $date = $this->bast_date ?: now();
        $hari = $date->translatedFormat('l');
        $tgl = self::terbilang((int) $date->format('j'));
        $bln = $date->translatedFormat('F');
        $thn = self::terbilang((int) $date->format('Y'));
        $tempat = $this->document_place ?: 'Badan Pusat Statistik Kabupaten Kolaka Utara';

        return "Pada hari ini {$hari}, tanggal {$tgl}, bulan {$bln}, tahun {$thn}, bertempat di {$tempat}, kami yang bertanda tangan di bawah ini:";
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
        $prefix = $bastType === 'petugas_ke_kantor' ? 'BAST-TERISI' : 'BAST-BLANKO';

        // Cari seluruh nomor yang pernah dibuat (termasuk yang di-soft-delete) untuk prefix dan tahun ini
        $existingNumbers = static::withTrashed()
            ->where('bast_number', 'like', "{$prefix}/{$year}/%")
            ->pluck('bast_number');

        $maxSeq = 0;
        foreach ($existingNumbers as $num) {
            if (preg_match('/\/(\d+)$/', $num, $matches)) {
                $seq = (int) $matches[1];
                if ($seq > $maxSeq) {
                    $maxSeq = $seq;
                }
            }
        }

        // Tentukan nomor berikutnya dan pastikan belum pernah terdaftar sama sekali di database
        $nextSeq = $maxSeq + 1;
        do {
            $candidate = sprintf('%s/%s/%s/%04d', $prefix, $year, $month, $nextSeq);
            $exists = static::withTrashed()->where('bast_number', $candidate)->exists();
            if ($exists) {
                $nextSeq++;
            }
        } while ($exists);

        return $candidate;
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

    public function getParsedItemsDetailedAttribute(): array
    {
        $rawItems = $this->parsed_items;
        $detailed = [];

        foreach ($rawItems as $item) {
            if (preg_match('/^(.*?)\s*\(([0-9]+(?:\.[0-9]+)?)\s*([a-zA-Z\/\.\s]*)\)$/u', $item, $matches)) {
                $detailed[] = [
                    'name' => trim($matches[1]),
                    'qty' => trim($matches[2]),
                    'unit' => trim($matches[3]),
                    'raw' => $item,
                ];
            } elseif (preg_match('/^([0-9]+(?:\.[0-9]+)?)\s*([a-zA-Z\/\.]+)?\s*[-–:]\s*(.*)$/u', $item, $matches)) {
                $detailed[] = [
                    'name' => trim($matches[3]),
                    'qty' => trim($matches[1]),
                    'unit' => trim($matches[2] ?? ''),
                    'raw' => $item,
                ];
            } else {
                $detailed[] = [
                    'name' => $item,
                    'qty' => '1',
                    'unit' => '',
                    'raw' => $item,
                ];
            }
        }

        return $detailed;
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
            $recId = !empty($row['recipient_id']) ? (int) $row['recipient_id'] : (!empty($row['id']) ? (int) $row['id'] : null);

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
                'recipient_id' => $recId,
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
        foreach ($request->input('items', []) as $item) {
            if (is_array($item)) {
                $name = trim((string) ($item['name'] ?? ''));
                $qty = trim((string) ($item['qty'] ?? ''));
                $unit = trim((string) ($item['unit'] ?? ''));

                if ($name === '') {
                    continue;
                }

                $qtyPart = '';
                if ($qty !== '' && $unit !== '') {
                    $qtyPart = " ({$qty} {$unit})";
                } elseif ($qty !== '') {
                    $qtyPart = " ({$qty})";
                } elseif ($unit !== '') {
                    $qtyPart = " ({$unit})";
                }

                $cleanItems[] = "{$name}{$qtyPart}";
            } else {
                $trimmed = trim((string) $item);
                if ($trimmed !== '') {
                    $cleanItems[] = $trimmed;
                }
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
        if (!in_array($category, ['PELATIHAN', 'ATK', 'KUES', 'TIMBANGAN', 'ATK_RESPONDEN', 'RESPONDEN', 'PERLENGKAPAN'], true)) {
            $category = 'PELATIHAN';
        }
        if ($category === 'RESPONDEN') {
            $category = 'ATK_RESPONDEN';
        }
        if ($category === 'PERLENGKAPAN') {
            $category = 'PELATIHAN';
        }

        $defaultTitle = match ($category) {
            'PELATIHAN' => 'Tanda Terima Perlengkapan Petugas',
            'ATK' => 'Tanda Terima ATK Pendataan',
            'KUES' => 'Tanda Terima Kuesioner Pendataan',
            'ATK_RESPONDEN' => 'Tanda Terima ATK dan Dokumen untuk Responden',
            'TIMBANGAN' => 'Tanda Terima Timbangan',
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
        $existingRecipients = $this->recipients()->get();
        $processedIds = [];

        foreach ($recipients as $idx => $row) {
            $recId = !empty($row['recipient_id']) ? (int) $row['recipient_id'] : (!empty($row['id']) ? (int) $row['id'] : null);
            $userId = !empty($row['user_id']) ? (int) $row['user_id'] : null;
            $name = trim((string) ($row['recipient_name'] ?? ''));

            // Match priority:
            // 1. Explicit recipient_id (jika baris tersebut adalah penerima lama)
            // 2. user_id (jika petugas tersebut terdaftar dengan user_id yang sama)
            // 3. recipient_name (pencocokan nama tidak peka huruf besar/kecil)
            $match = null;
            if ($recId) {
                $match = $existingRecipients->first(fn ($r) => (int) $r->id === $recId && !in_array($r->id, $processedIds, true));
            }
            if (!$match && $userId) {
                $match = $existingRecipients->first(fn ($r) => (int) $r->user_id === $userId && !in_array($r->id, $processedIds, true));
            }
            if (!$match && $name !== '') {
                $match = $existingRecipients->first(fn ($r) => strcasecmp(trim($r->recipient_name), $name) === 0 && !in_array($r->id, $processedIds, true));
            }

            $payload = [
                'user_id' => $userId,
                'recipient_name' => $name,
                'position_unit' => trim((string) ($row['position_unit'] ?? '')) ?: 'Mitra',
                'origin_role' => trim((string) ($row['origin_role'] ?? '')) ?: '-',
                'sort_order' => $idx + 1,
            ];

            if ($match) {
                // JANGAN PERNAH MENGHAPUS ATAU ME-RESET TANDA TANGAN YANG SUDAH DIBUBUHKAN!
                // Tanda tangan (signature_path), waktu tanda tangan, dan status verifikasi tetap aman 100%
                $match->update($payload);
                $processedIds[] = $match->id;
            } else {
                $created = $this->recipients()->create($payload);
                $processedIds[] = $created->id;
            }
        }

        // Hapus hanya penerima yang benar-benar dikeluarkan dari daftar
        if (!empty($processedIds)) {
            $this->recipients()->whereNotIn('id', $processedIds)->delete();
        } else {
            $this->recipients()->delete();
        }
    }
}

