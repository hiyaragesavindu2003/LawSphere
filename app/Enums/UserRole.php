<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Lawyer = 'lawyer';
    case Client = 'client';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Lawyer => 'Lawyer',
            self::Client => 'Client',
        };
    }

    public function dashboardRoute(): string
    {
        return match ($this) {
            self::Admin => 'admin.dashboard',
            self::Lawyer => 'lawyer.dashboard',
            self::Client => 'client.dashboard',
        };
    }
}
