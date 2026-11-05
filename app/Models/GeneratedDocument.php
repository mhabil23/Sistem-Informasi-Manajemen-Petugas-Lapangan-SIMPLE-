<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class GeneratedDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_number',
        'title',
        'document_type',
        'user_id',
        'documentable_type',
        'documentable_id',
        'file_path',
        'notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }
}

