<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lawyer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'qualifications',
        'specialization',
        'experience_years',
        'biography',
        'bar_number',
        'is_approved',
        'approved_at',
        'average_rating',
        'total_reviews',
    ];

    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',
            'approved_at' => 'datetime',
            'average_rating' => 'decimal:2',
            'experience_years' => 'integer',
            'total_reviews' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function legalRequests(): HasMany
    {
        return $this->hasMany(LegalRequest::class);
    }

    public function legalResponses(): HasMany
    {
        return $this->hasMany(LegalResponse::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeBySpecialization($query, string $specialization)
    {
        return $query->where('specialization', $specialization);
    }

    public function scopeMinRating($query, float $rating)
    {
        return $query->where('average_rating', '>=', $rating);
    }

    public function recalculateRating(): void
    {
        $this->average_rating = $this->reviews()->avg('rating') ?? 0;
        $this->total_reviews = $this->reviews()->count();
        $this->save();
    }
}
