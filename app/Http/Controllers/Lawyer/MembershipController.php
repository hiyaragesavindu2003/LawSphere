<?php

namespace App\Http\Controllers\Lawyer;

use App\Enums\PaymentType;
use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MembershipController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    public function index(): View
    {
        $lawyer = auth()->user()->lawyer;

        $plans = config('lawsphere.membership_plans', []);
        $activeMembership = $lawyer->memberships()->active()->latest('end_date')->first();
        $paymentHistory = auth()->user()->payments()
            ->where('payment_type', PaymentType::Membership)
            ->latest()
            ->take(5)
            ->get();

        return view('lawyer.membership.index', compact('plans', 'activeMembership', 'paymentHistory'));
    }

    public function subscribe(string $plan): RedirectResponse
    {
        $plans = config('lawsphere.membership_plans', []);

        abort_unless(isset($plans[$plan]), 404);

        $planData = $plans[$plan];
        $lawyer = auth()->user()->lawyer;

        $payment = $this->paymentService->create(
            user: auth()->user(),
            type: PaymentType::Membership,
            amount: (float) $planData['amount'],
            description: "LawSphere {$planData['name']} Membership ({$planData['months']} month(s))",
            metadata: [
                'plan_key' => $plan,
                'lawyer_id' => $lawyer->id,
            ],
        );

        return redirect()->route('payments.checkout', $payment);
    }
}
