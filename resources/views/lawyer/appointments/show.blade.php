@extends('layouts.app')

@section('title', 'Manage Appointment')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('lawyer.appointments.index') }}">Appointments</a></li>
            <li class="breadcrumb-item active">#{{ $appointment->id }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between py-3">
                    <h5 class="mb-0">Appointment Details</h5>
                    <span class="badge {{ $appointment->status->badgeClass() }}">{{ $appointment->status->label() }}</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <h6 class="text-muted">Client</h6>
                            <p class="mb-0 fw-semibold">{{ $appointment->client->user->name }}</p>
                            <small>{{ $appointment->client->user->phone ?? 'No phone' }}</small>
                        </div>
                        <div class="col-sm-6">
                            <h6 class="text-muted">Date & Time</h6>
                            <p class="mb-0 fw-semibold">{{ $appointment->formatted_date_time }}</p>
                        </div>
                        @if($appointment->notes)
                            <div class="col-12">
                                <h6 class="text-muted">Client notes</h6>
                                <p class="mb-0">{{ $appointment->notes }}</p>
                            </div>
                        @endif
                    </div>

                    @if($appointment->status === \App\Enums\AppointmentStatus::Pending)
                        <hr>
                        <div class="d-flex gap-2 flex-wrap">
                            <form method="POST" action="{{ route('lawyer.appointments.approve', $appointment) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-lg me-1"></i> Approve
                                </button>
                            </form>
                            <button type="button" class="btn btn-danger" data-bs-toggle="collapse" data-bs-target="#rejectForm">
                                <i class="bi bi-x-lg me-1"></i> Reject
                            </button>
                        </div>
                        <div class="collapse mt-3" id="rejectForm">
                            <form method="POST" action="{{ route('lawyer.appointments.reject', $appointment) }}">
                                @csrf
                                @method('PATCH')
                                <div class="mb-2">
                                    <label class="form-label">Rejection reason</label>
                                    <textarea name="cancellation_reason" class="form-control" rows="2" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm">Confirm Reject</button>
                            </form>
                        </div>
                    @endif

                    @if($appointment->status === \App\Enums\AppointmentStatus::Approved)
                        <hr>
                        <form method="POST" action="{{ route('lawyer.appointments.complete', $appointment) }}" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i> Mark Completed
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            @if(in_array($appointment->status, [\App\Enums\AppointmentStatus::Pending, \App\Enums\AppointmentStatus::Approved]))
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0"><i class="bi bi-calendar-event me-1"></i> Reschedule</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('lawyer.appointments.reschedule', $appointment) }}">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3">
                                <label class="form-label">New date</label>
                                <input type="date" name="appointment_date" class="form-control"
                                       value="{{ $appointment->appointment_date->format('Y-m-d') }}"
                                       min="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New time</label>
                                <input type="time" name="appointment_time" class="form-control"
                                       value="{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}"
                                       min="08:00" max="18:00" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Reason</label>
                                <textarea name="reschedule_reason" class="form-control" rows="2" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-btn-gold w-100">Reschedule</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
