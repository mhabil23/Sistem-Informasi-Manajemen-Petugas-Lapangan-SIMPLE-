<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function generatedDocuments(): MorphMany
    {
        return $this->morphMany(GeneratedDocument::class, 'documentable');
    }
}

