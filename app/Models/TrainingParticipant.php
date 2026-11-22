<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingParticipant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'training_event_id',
        'name',
        'nip_or_id',
        'category',
        'agency',
        'phone',
        'email',
        'attendance_status',
        'certificate_number',
        'signature_path',
        'signed_at',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'signed_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }

    public function trainingEvent(): BelongsTo
    {
        return $this->belongsTo(TrainingEvent::class, 'training_event_id');
    }
}

