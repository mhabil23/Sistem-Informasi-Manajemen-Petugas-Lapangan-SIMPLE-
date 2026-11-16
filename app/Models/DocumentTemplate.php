<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type',
        'content',
        'header_text',
        'footer_text',
        'file_path',
        'is_active',
    ];

    public function hasFile(): bool
    {
        return !empty($this->file_path) && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->file_path);
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}

