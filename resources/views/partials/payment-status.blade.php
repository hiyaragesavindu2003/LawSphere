@if($payment ?? null)
    @if($payment->isPaid())
        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Paid {{ $payment->formatted_amount }}</span>
    @elseif($payment->status === \App\Enums\PaymentStatus::Pending)
        <a href="{{ route('payments.checkout', $payment) }}" class="badge bg-warning text-dark text-decoration-none">
            <i class="bi bi-exclamation-circle me-1"></i>Payment pending — Pay now
        </a>
    @endif
@elseif(isset($isPaid) && $isPaid)
    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Paid</span>
@endif
