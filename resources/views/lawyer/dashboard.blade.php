@extends('layouts.app')

@section('title', 'Lawyer Dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="mb-0"><i class="bi bi-speedometer2 me-2"></i>Lawyer Dashboard</h2>
        <a href="{{ route('lawyer.legal-advice.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-journal-text me-1"></i> Legal Advice
        </a>
        <a href="{{ route('lawyer.membership.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-award me-1"></i> Membership
        </a>
        <a href="{{ route('lawyer.chat.index') }}" class="btn btn-btn-navy">
            <i class="bi bi-chat-dots me-1"></i> Messages
        </a>
        <a href="{{ route('lawyer.appointments.index') }}" class="btn btn-btn-gold">
            <i class="bi bi-calendar-check me-1"></i> Appointments
        </a>
    </div>

    <div class="row g-4 mb-4">
        @foreach([
            ['label' => 'Total Appointments', 'value' => $stats['total_appointments']],
            ['label' => 'Pending Appointments', 'value' => $stats['pending_appointments']],
            ['label' => 'Pending Requests', 'value' => $stats['pending_requests']],
            ['label' => 'Average Rating', 'value' => number_format($stats['average_rating'], 1)],
        ] as $stat)
        <div class="col-md-3">
            <div class="card dashboard-stat h-100">
                <div class="card-body">
                    <p class="text-muted mb-1 small">{{ $stat['label'] }}</p>
                    <h3 class="mb-0">{{ $stat['value'] }}</h3>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white"><h5 class="mb-0">Recent Appointments</h5></div>
                <div class="card-body">
                    @forelse($recentAppointments as $appointment)
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <div>
                                <strong>{{ $appointment->client->user->name }}</strong>
                                <br><small>{{ $appointment->appointment_date->format('M d, Y') }} at {{ $appointment->appointment_time }}</small>
                            </div>
                            <span class="badge {{ $appointment->status->badgeClass() }}">{{ $appointment->status->label() }}</span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No appointments yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white"><h5 class="mb-0">Recent Reviews</h5></div>
                <div class="card-body">
                    @forelse($recentReviews as $review)
                        <div class="py-2 border-bottom">
                            <div class="text-warning">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                @endfor
                            </div>
                            <small>{{ $review->client->user->name }} — {{ Str::limit($review->review_text, 80) }}</small>
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
