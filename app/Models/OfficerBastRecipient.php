<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfficerBastRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'officer_bast_id',
        'user_id',
        'recipient_name',
        'position_unit',
        'origin_role',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function officerBast(): BelongsTo
    {
        return $this->belongsTo(OfficerBast::class, 'officer_bast_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
