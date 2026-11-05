<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RespondentBast extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'bast_number',
        'bast_date',
        'respondent_id',
        'assignment_id',
        'officer_id',
        'handover_items',
        'recipient_type',
        'recipient_name',
        'recipient_relation',
        'signature_path',
        'officer_signature_path',
        'notes',
        'file_path',
        'status',
    ];

    public function getRecipientDisplayNameAttribute(): string
    {
        if ($this->recipient_type === 'representative' && !empty($this->recipient_name)) {
            $rel = !empty($this->recipient_relation) ? ' (' . $this->recipient_relation . ')' : ' (Wakil)';
            return $this->recipient_name . $rel;
        }

        return $this->respondent ? $this->respondent->name . ' (KRT Langsung)' : '-';
    }

    public function getRecipientLabelAttribute(): string
    {
        return $this->recipient_type === 'representative' ? 'Wakil / Anggota Keluarga' : 'Kepala Rumah Tangga (KRT)';
    }

    protected function casts(): array
    {
        return [
            'bast_date' => 'date',
        ];
    }

    public function respondent(): BelongsTo
    {
        return $this->belongsTo(Respondent::class, 'respondent_id');
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

