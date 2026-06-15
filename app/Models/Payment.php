<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_type',
        'payable_type',
        'payable_id',
        'amount',
        'currency',
        'status',
        'transaction_id',
        'description',
        'metadata',
        'payment_method',
        'card_last_four',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => PaymentStatus::class,
            'payment_type' => PaymentType::class,
            'metadata' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function isPaid(): bool
    {
        return $this->status === PaymentStatus::Completed;
    }

    public function getFormattedAmountAttribute(): string
    {
        $symbol = config('lawsphere.currency_symbol', '$');

        return $symbol.number_format((float) $this->amount, 2);
    }
}
