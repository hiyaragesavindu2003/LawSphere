@extends('layouts.app')

@section('title', $lawyer->user->name)

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lawyers.index') }}">Find a Lawyer</a></li>
            <li class="breadcrumb-item active">{{ $lawyer->user->name }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm lawyer-profile-card">
                <div class="card-body text-center p-4">
                    <img src="{{ $lawyer->user->profile_photo_url }}" alt="{{ $lawyer->user->name }}"
                         class="lawyer-profile-photo rounded-circle mb-3">
                    <h3 class="mb-1">{{ $lawyer->user->name }}</h3>
                    <span class="badge lawyer-spec-badge mb-3">{{ $lawyer->specialization }}</span>

                    <div class="text-warning mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= round($lawyer->average_rating) ? '-fill' : '' }}"></i>
                        @endfor
                    </div>
                    <p class="text-muted small mb-4">
                        {{ number_format($lawyer->average_rating, 1) }} / 5
                        ({{ $lawyer->total_reviews }} review{{ $lawyer->total_reviews !== 1 ? 's' : '' }})
                    </p>

                    <ul class="list-unstyled text-start small">
                        <li class="mb-2"><i class="bi bi-briefcase text-gold me-2"></i>{{ $lawyer->experience_years }} years experience</li>
                        <li class="mb-2"><i class="bi bi-telephone text-gold me-2"></i>{{ $lawyer->user->phone ?? 'N/A' }}</li>
                        <li class="mb-2"><i class="bi bi-envelope text-gold me-2"></i>{{ $lawyer->user->email }}</li>
                        @if($lawyer->user->address)
                            <li class="mb-2"><i class="bi bi-geo-alt text-gold me-2"></i>{{ $lawyer->user->address }}</li>
                        @endif
                        <li><i class="bi bi-award text-gold me-2"></i>Bar #{{ $lawyer->bar_number }}</li>
                    </ul>

                    <div class="payment-fee-box p-3 rounded mb-3 text-start small">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Consultation</span>
                            <strong>{{ config('lawsphere.currency_symbol') }}{{ number_format($lawyer->consultation_fee, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Legal advice</span>
                            <strong>{{ config('lawsphere.currency_symbol') }}{{ number_format($lawyer->legal_advice_fee, 2) }}</strong>
                        </div>
                    </div>

                    @auth
                        @if(auth()->user()->isClient())
                            <a href="{{ route('client.legal-advice.create', $lawyer) }}" class="btn btn-outline-primary w-100 mb-2">
                                <i class="bi bi-journal-text me-1"></i> Request Legal Advice
                            </a>
                            <a href="{{ route('client.appointments.create', $lawyer) }}" class="btn btn-btn-navy w-100 mb-2">
                                <i class="bi bi-calendar-plus me-1"></i> Book Appointment
                            </a>
                            <form method="POST" action="{{ route('client.chat.start', $lawyer) }}" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-btn-gold w-100">
                                    <i class="bi bi-chat-dots me-1"></i> Start Chat
                                </button>
                            </form>
                            <a href="{{ route('client.appointments.index') }}" class="btn btn-outline-secondary w-100 btn-sm">
                                <i class="bi bi-calendar-check me-1"></i> My Appointments
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-btn-gold w-100 mt-3 mb-2">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login to Book
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>About</h5>
                </div>
                <div class="card-body">
                    <h6 class="text-muted">Qualifications</h6>
                    <p>{{ $lawyer->qualifications }}</p>
                    <h6 class="text-muted">Biography</h6>
                    <p class="mb-0">{{ $lawyer->biography ?? 'No biography provided.' }}</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-chat-quote me-2"></i>Client Reviews</h5>
                </div>
                <div class="card-body">
                    @forelse($lawyer->reviews as $review)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <strong>{{ $review->client->user->name }}</strong>
                                <div class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-muted small mb-1">{{ $review->created_at->format('M d, Y') }}</p>
                            <p class="mb-0">{{ $review->review_text }}</p>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No reviews yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
