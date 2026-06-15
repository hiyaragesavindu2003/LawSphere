<?php

namespace App\Enums;

enum PaymentType: string
{
    case Appointment = 'appointment';
    case LegalAdvice = 'legal_advice';
    case Membership = 'membership';

    public function label(): string
    {
        return match ($this) {
            self::Appointment => 'Consultation',
            self::LegalAdvice => 'Legal Advice',
            self::Membership => 'Membership',
        };
    }
}
