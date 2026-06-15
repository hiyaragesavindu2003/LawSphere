@extends('layouts.app')

@section('title', 'Membership Plans')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="mb-1"><i class="bi bi-award me-2"></i>LawSphere Membership</h2>
            <p class="text-muted mb-0">Subscribe to grow your practice on the platform</p>
        </div>
        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-credit-card me-1"></i> Payment History
        </a>
    </div>

    @if($activeMembership)
        <div class="alert alert-success border-0 shadow-sm">
            <i class="bi bi-check-circle me-1"></i>
            <strong>Active plan:</strong> {{ $activeMembership->plan_name }}
            — valid until {{ $activeMembership->end_date->format('M d, Y') }}
        </div>
    @endif

    <div class="row g-4 mb-5">
        @foreach($plans as $key => $plan)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 membership-plan-card {{ $key === 'professional' ? 'membership-plan-featured' : '' }}">
                    <div class="card-body p-4 d-flex flex-column">
                        @if($key === 'professional')
                            <span class="badge lawyer-spec-badge mb-2">Popular</span>
                        @endif
                        <h5 class="mb-1">{{ $plan['name'] }}</h5>
                        <p class="text-muted small flex-grow-1">{{ $plan['description'] }}</p>
                        <div class="mb-3">
                            <span class="display-6 fw-bold text-gold">{{ config('lawsphere.currency_symbol') }}{{ number_format($plan['amount'], 2) }}</span>
                            <span class="text-muted">/ {{ $plan['months'] }} mo</span>
                        </div>
                        <form method="POST" action="{{ route('lawyer.membership.subscribe', $key) }}">
                            @csrf
                            <button type="submit" class="btn {{ $key === 'professional' ? 'btn-btn-gold' : 'btn-btn-navy' }} w-100">
                                Subscribe
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($paymentHistory->isNotEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Recent Membership Payments</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paymentHistory as $payment)
                                <tr>
                                    <td>{{ $payment->created_at->format('M d, Y') }}</td>
                                    <td>{{ $payment->description }}</td>
                                    <td>{{ $payment->formatted_amount }}</td>
                                    <td><span class="badge {{ $payment->status->badgeClass() }}">{{ $payment->status->label() }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
