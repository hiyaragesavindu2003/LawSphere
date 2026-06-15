<?php

namespace App\Models;

use App\Enums\LegalRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

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

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function payment(): MorphOne
    {
        return $this->morphOne(Payment::class, 'payable')->latestOfMany();
    }

    public function isPaid(): bool
    {
        return $this->payments()->where('status', \App\Enums\PaymentStatus::Completed)->exists();
    }

    public function adviceAmount(): float
    {
        return (float) $this->lawyer->legal_advice_fee;
    }

    public function scopePending($query)
    {
        return $query->where('status', LegalRequestStatus::Pending);
    }

    public function review(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function canBeReviewed(): bool
    {
        if ($this->review()->exists()) {
            return false;
        }

        if (! in_array($this->status, [LegalRequestStatus::Resolved, LegalRequestStatus::Closed], true)) {
            return false;
        }

        if (! $this->responses()->exists()) {
            return false;
        }

        $fee = (float) $this->lawyer->legal_advice_fee;

        if ($fee > 0 && ! $this->isPaid()) {
            return false;
        }

        return true;
    }
}
