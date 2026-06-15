@extends('layouts.app')

@section('title', 'Client Dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="mb-0"><i class="bi bi-speedometer2 me-2"></i>Client Dashboard</h2>
        <a href="{{ route('client.chat.index') }}" class="btn btn-btn-navy">
            <i class="bi bi-chat-dots me-1"></i> Messages
        </a>
        <a href="{{ route('client.appointments.index') }}" class="btn btn-btn-gold">
            <i class="bi bi-calendar-check me-1"></i> Appointments
        </a>
    </div>

    <div class="row g-4 mb-4">
        @foreach([
            ['label' => 'Upcoming Appointments', 'value' => $stats['upcoming_appointments']],
            ['label' => 'Total Appointments', 'value' => $stats['total_appointments']],
            ['label' => 'Pending Requests', 'value' => $stats['pending_requests']],
            ['label' => 'Reviews Given', 'value' => $stats['total_reviews']],
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
                <div class="card-header bg-white"><h5 class="mb-0">Upcoming Appointments</h5></div>
                <div class="card-body">
                    @forelse($upcomingAppointments as $appointment)
                        <a href="{{ route('client.appointments.show', $appointment) }}" class="d-flex justify-content-between py-2 border-bottom text-decoration-none text-dark">
                            <div>
                                <strong>{{ $appointment->lawyer->user->name }}</strong>
                                <br><small>{{ $appointment->appointment_date->format('M d, Y') }} at {{ $appointment->formatted_time }}</small>
                            </div>
                            <span class="badge {{ $appointment->status->badgeClass() }}">{{ $appointment->status->label() }}</span>
                        </a>
                    @empty
                        <p class="text-muted mb-0">No upcoming appointments.</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white"><h5 class="mb-0">Recent Legal Requests</h5></div>
                <div class="card-body">
                    @forelse($recentRequests as $request)
                        <div class="py-2 border-bottom">
                            <strong>{{ $request->subject }}</strong>
                            <br><small class="text-muted">{{ $request->status->label() }} — {{ $request->created_at->diffForHumans() }}</small>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No requests submitted yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
