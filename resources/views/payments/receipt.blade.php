@extends('layouts.app')

@section('title', 'Payment Receipt')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm text-center payment-receipt-card">
                <div class="card-body p-5">
                    <div class="payment-receipt-icon mb-3">
                        <i class="bi bi-check-circle-fill text-success display-3"></i>
                    </div>
                    <h3 class="mb-2">Payment Successful</h3>
                    <p class="text-muted mb-4">Thank you. Your transaction has been completed.</p>

                    <div class="text-start bg-light rounded p-4 mb-4">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <small class="text-muted d-block">Transaction ID</small>
                                <strong>{{ $payment->transaction_id }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <small class="text-muted d-block">Date</small>
                                <strong>{{ $payment->paid_at?->format('M d, Y h:i A') }}</strong>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block">Description</small>
                                <strong>{{ $payment->description }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <small class="text-muted d-block">Type</small>
                                <strong>{{ $payment->payment_type->label() }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <small class="text-muted d-block">Amount Paid</small>
                                <strong class="text-gold fs-5">{{ $payment->formatted_amount }}</strong>
                            </div>
                            @if($payment->card_last_four)
                                <div class="col-sm-6">
                                    <small class="text-muted d-block">Card</small>
                                    <strong>**** **** **** {{ $payment->card_last_four }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                        @if($payment->payable && $payment->payment_type === \App\Enums\PaymentType::Appointment)
                            <a href="{{ route('client.appointments.show', $payment->payable) }}" class="btn btn-btn-navy">
                                View Appointment
                            </a>
                        @elseif($payment->payable && $payment->payment_type === \App\Enums\PaymentType::LegalAdvice)
                            <a href="{{ route('client.legal-advice.show', $payment->payable) }}" class="btn btn-btn-navy">
                                View Request
                            </a>
                        @elseif($payment->payment_type === \App\Enums\PaymentType::Membership)
                            <a href="{{ route('lawyer.membership.index') }}" class="btn btn-btn-navy">
                                View Membership
                            </a>
                        @endif
                        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">All Payments</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
