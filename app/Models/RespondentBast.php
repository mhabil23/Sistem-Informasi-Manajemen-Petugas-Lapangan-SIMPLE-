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
        'notes',
        'file_path',
        'status',
    ];

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

