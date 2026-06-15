<?php

return [
    'currency' => 'USD',
    'currency_symbol' => '$',

    'default_consultation_fee' => 75.00,
    'default_legal_advice_fee' => 35.00,

    'membership_plans' => [
        'basic' => [
            'name' => 'Basic',
            'amount' => 49.99,
            'months' => 1,
            'description' => 'List your profile and receive client messages.',
        ],
        'professional' => [
            'name' => 'Professional',
            'amount' => 129.99,
            'months' => 3,
            'description' => 'Priority listing, appointments, and legal advice requests.',
        ],
        'premium' => [
            'name' => 'Premium',
            'amount' => 399.99,
            'months' => 12,
            'description' => 'Full platform access with featured profile placement.',
        ],
    ],
];
