@extends('layouts.app')

@section('title', 'Payment History')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="mb-1"><i class="bi bi-credit-card me-2"></i>Payments</h2>
            <p class="text-muted mb-0">Your transaction history on LawSphere</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET">
                <select name="status" class="form-select w-auto" onchange="this.form.submit()">
                    <option value="">All statuses</option>
                    @foreach(\App\Enums\PaymentStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected(request('status') === $status->value)>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    @if($payments->isEmpty())
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body">
                <i class="bi bi-wallet2 display-4 text-muted mb-3"></i>
                <h5>No payments yet</h5>
                <p class="text-muted mb-0">Payments for appointments, legal advice, or memberships will appear here.</p>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr>
                                <td>{{ $payment->created_at->format('M d, Y') }}</td>
                                <td>{{ Str::limit($payment->description, 50) }}</td>
                                <td><span class="badge bg-light text-dark">{{ $payment->payment_type->label() }}</span></td>
                                <td class="fw-semibold">{{ $payment->formatted_amount }}</td>
                                <td><span class="badge {{ $payment->status->badgeClass() }}">{{ $payment->status->label() }}</span></td>
                                <td class="text-end">
                                    @if($payment->isPaid())
                                        <a href="{{ route('payments.receipt', $payment) }}" class="btn btn-sm btn-outline-primary">Receipt</a>
                                    @elseif($payment->status === \App\Enums\PaymentStatus::Pending)
                                        <a href="{{ route('payments.checkout', $payment) }}" class="btn btn-sm btn-btn-gold">Pay Now</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $payments->links() }}</div>
    @endif
</div>
@endsection
