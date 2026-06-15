<?php

namespace App\Services;

use App\Enums\MembershipStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PaymentService
{
    public function create(
        User $user,
        PaymentType $type,
        float $amount,
        string $description,
        ?Model $payable = null,
        ?array $metadata = null,
    ): Payment {
        return Payment::create([
            'user_id' => $user->id,
            'payment_type' => $type,
            'payable_type' => $payable?->getMorphClass(),
            'payable_id' => $payable?->getKey(),
            'amount' => $amount,
            'currency' => config('lawsphere.currency', 'USD'),
            'status' => PaymentStatus::Pending,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }

    public function process(Payment $payment, array $card): bool
    {
        if ($payment->status !== PaymentStatus::Pending) {
            return false;
        }

        if (! $this->validateCard($card)) {
            $payment->update(['status' => PaymentStatus::Failed]);

            return false;
        }

        $payment->update([
            'status' => PaymentStatus::Completed,
            'transaction_id' => 'TXN-'.strtoupper(Str::random(12)),
            'payment_method' => 'card',
            'card_last_four' => substr(preg_replace('/\D/', '', $card['card_number']), -4),
            'paid_at' => now(),
        ]);

        $this->fulfill($payment);

        return true;
    }

    public function fulfill(Payment $payment): void
    {
        if ($payment->payment_type === PaymentType::Membership) {
            $this->activateMembership($payment);
        }
    }

    private function activateMembership(Payment $payment): void
    {
        $metadata = $payment->metadata ?? [];
        $lawyerId = $metadata['lawyer_id'] ?? null;
        $planKey = $metadata['plan_key'] ?? null;
        $plan = $planKey ? config("lawsphere.membership_plans.{$planKey}") : null;

        if (! $lawyerId || ! $plan) {
            return;
        }

        Membership::create([
            'lawyer_id' => $lawyerId,
            'plan_name' => $plan['name'],
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonths($plan['months'])->toDateString(),
            'status' => MembershipStatus::Active,
            'amount' => $plan['amount'],
        ]);
    }

    private function validateCard(array $card): bool
    {
        $number = preg_replace('/\D/', '', $card['card_number'] ?? '');

        if (strlen($number) < 13 || strlen($number) > 19) {
            return false;
        }

        if (! preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $card['expiry'] ?? '')) {
            return false;
        }

        if (! preg_match('/^\d{3,4}$/', $card['cvv'] ?? '')) {
            return false;
        }

        if (trim($card['card_name'] ?? '') === '') {
            return false;
        }

        return true;
    }
}
