<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'lawyer_id',
        'appointment_id',
        'rating',
        'review_text',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
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

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    protected static function booted(): void
    {
        static::saved(function (Review $review) {
            $review->lawyer->recalculateRating();
        });

        static::deleted(function (Review $review) {
            $review->lawyer->recalculateRating();
        });
    }
}
