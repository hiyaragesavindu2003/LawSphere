<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Failed = 'failed';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Completed => 'Paid',
            self::Failed => 'Failed',
            self::Refunded => 'Refunded',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'bg-warning text-dark',
            self::Completed => 'bg-success',
            self::Failed => 'bg-danger',
            self::Refunded => 'bg-secondary',
        };
    }
}
