<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'caption',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(OfficerReport::class, 'report_id');
    }

    public function isImage(): bool
    {
        return str_starts_with($this->file_type, 'image/');
    }
}

