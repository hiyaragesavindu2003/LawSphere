@extends('layouts.app')

@section('title', 'My Appointments')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="mb-1"><i class="bi bi-calendar-check me-2"></i>My Appointments</h2>
            <p class="text-muted mb-0">View and manage your consultation bookings</p>
        </div>
        <a href="{{ route('lawyers.index') }}" class="btn btn-btn-gold">
            <i class="bi bi-plus-lg me-1"></i> Book New
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small mb-1">Filter by status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All statuses</option>
                        @foreach(\App\Enums\AppointmentStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected(request('status') === $status->value)>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    @if($appointments->isEmpty())
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body">
                <i class="bi bi-calendar-x display-4 text-muted mb-3"></i>
                <h5>No appointments yet</h5>
                <p class="text-muted">Find a lawyer and book your first consultation.</p>
                <a href="{{ route('lawyers.index') }}" class="btn btn-btn-navy">Find a Lawyer</a>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($appointments as $appointment)
                <div class="col-12">
                    <div class="card border-0 shadow-sm appointment-list-card">
                        <div class="card-body p-4">
                            <div class="row align-items-center g-3">
                                <div class="col-md-5">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $appointment->lawyer->user->profile_photo_url }}"
                                             class="chat-list-avatar rounded-circle" alt="">
                                        <div>
                                            <h6 class="mb-0">{{ $appointment->lawyer->user->name }}</h6>
                                            <small class="text-muted">{{ $appointment->lawyer->specialization }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Date & Time</small>
                                    <strong>{{ $appointment->formatted_date_time }}</strong>
                                </div>
                                <div class="col-md-2">
                                    <span class="badge {{ $appointment->status->badgeClass() }}">
                                        {{ $appointment->status->label() }}
                                    </span>
                                </div>
                                <div class="col-md-2 text-md-end">
                                    <a href="{{ route('client.appointments.show', $appointment) }}" class="btn btn-btn-navy btn-sm">
                                        View <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $appointments->links() }}</div>
    @endif
</div>
@endsection
