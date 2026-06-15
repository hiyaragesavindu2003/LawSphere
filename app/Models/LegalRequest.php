<?php

namespace App\Models;

use App\Enums\LegalRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'lawyer_id',
        'subject',
        'description',
        'status',
        'attachment',
    ];

    protected function casts(): array
    {
        return [
            'status' => LegalRequestStatus::class,
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function lawyer(): BelongsTo
    {
        return $this->belongsTo(Lawyer::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(LegalResponse::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', LegalRequestStatus::Pending);
    }
}
