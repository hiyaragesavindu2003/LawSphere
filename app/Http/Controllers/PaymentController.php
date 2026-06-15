<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    public function index(Request $request): View
    {
        $payments = auth()->user()->payments()
            ->latest()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->paginate(10)
            ->withQueryString();

        return view('payments.index', compact('payments'));
    }

    public function checkout(Payment $payment): View|RedirectResponse
    {
        $this->authorizePayment($payment);

        if ($payment->isPaid()) {
            return redirect()->route('payments.receipt', $payment);
        }

        $payment->load('payable');

        return view('payments.checkout', compact('payment'));
    }

    public function process(Request $request, Payment $payment): RedirectResponse
    {
        $this->authorizePayment($payment);

        abort_unless($payment->status === \App\Enums\PaymentStatus::Pending, 403);

        $validated = $request->validate([
            'card_name' => ['required', 'string', 'max:100'],
            'card_number' => ['required', 'string', 'max:19'],
            'expiry' => ['required', 'string', 'max:5'],
            'cvv' => ['required', 'string', 'max:4'],
        ]);

        if (! $this->paymentService->process($payment, $validated)) {
            return back()->withInput()->withErrors([
                'card_number' => 'Payment failed. Please check your card details and try again.',
            ]);
        }

        return redirect()
            ->route('payments.receipt', $payment)
            ->with('status', 'Payment completed successfully.');
    }

    public function receipt(Payment $payment): View
    {
        $this->authorizePayment($payment);

        abort_unless($payment->isPaid(), 404);

        $payment->load('payable');

        return view('payments.receipt', compact('payment'));
    }

    private function authorizePayment(Payment $payment): void
    {
        abort_unless($payment->user_id === auth()->id(), 403);
    }
}
