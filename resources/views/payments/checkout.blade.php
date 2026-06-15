@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm payment-checkout-card overflow-hidden">
                <div class="card-header payment-checkout-header py-4">
                    <h4 class="mb-1 text-white"><i class="bi bi-shield-lock me-2"></i>Secure Checkout</h4>
                    <p class="mb-0 text-white-50">Complete your payment to continue</p>
                </div>
                <div class="card-body p-4">
                    <div class="payment-summary-box p-4 mb-4 rounded">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-light text-dark mb-2">{{ $payment->payment_type->label() }}</span>
                                <h5 class="mb-1">{{ $payment->description }}</h5>
                            </div>
                            <h4 class="text-gold mb-0">{{ $payment->formatted_amount }}</h4>
                        </div>
                    </div>

                    <div class="alert alert-info border-0">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>Demo mode:</strong> Use any valid card format (e.g. 4242 4242 4242 4242, expiry 12/28, CVV 123).
                        No real charges are made.
                    </div>

                    <form method="POST" action="{{ route('payments.process', $payment) }}">
                        @csrf

                        <div class="mb-3">
                            <label for="card_name" class="form-label fw-semibold">Cardholder Name</label>
                            <input type="text" class="form-control @error('card_name') is-invalid @enderror"
                                   id="card_name" name="card_name" value="{{ old('card_name', auth()->user()->name) }}" required>
                            @error('card_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="card_number" class="form-label fw-semibold">Card Number</label>
                            <input type="text" class="form-control @error('card_number') is-invalid @enderror"
                                   id="card_number" name="card_number" value="{{ old('card_number') }}"
                                   placeholder="4242 4242 4242 4242" maxlength="19" required>
                            @error('card_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="expiry" class="form-label fw-semibold">Expiry (MM/YY)</label>
                                <input type="text" class="form-control @error('expiry') is-invalid @enderror"
                                       id="expiry" name="expiry" value="{{ old('expiry') }}"
                                       placeholder="12/28" maxlength="5" required>
                                @error('expiry')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="cvv" class="form-label fw-semibold">CVV</label>
                                <input type="text" class="form-control @error('cvv') is-invalid @enderror"
                                       id="cvv" name="cvv" value="{{ old('cvv') }}"
                                       placeholder="123" maxlength="4" required>
                                @error('cvv')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-btn-gold btn-lg flex-grow-1">
                                <i class="bi bi-lock me-1"></i> Pay {{ $payment->formatted_amount }}
                            </button>
                            <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
