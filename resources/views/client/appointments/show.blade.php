@extends('layouts.app')

@section('title', 'Appointment Details')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('client.appointments.index') }}">My Appointments</a></li>
            <li class="breadcrumb-item active">Details</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0">Appointment #{{ $appointment->id }}</h5>
                    <div class="d-flex align-items-center gap-2">
                        @include('partials.payment-status', ['payment' => $appointment->payment])
                        <span class="badge {{ $appointment->status->badgeClass() }} fs-6">
                            {{ $appointment->status->label() }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <h6 class="text-muted">Lawyer</h6>
                            <p class="mb-0 fw-semibold">{{ $appointment->lawyer->user->name }}</p>
                            <small class="text-muted">{{ $appointment->lawyer->specialization }}</small>
                        </div>
                        <div class="col-sm-6">
                            <h6 class="text-muted">Scheduled</h6>
                            <p class="mb-0 fw-semibold">{{ $appointment->formatted_date_time }}</p>
                        </div>
                        @if($appointment->notes)
                            <div class="col-12">
                                <h6 class="text-muted">Your notes</h6>
                                <p class="mb-0">{{ $appointment->notes }}</p>
                            </div>
                        @endif
                        @if($appointment->reschedule_reason)
                            <div class="col-12">
                                <div class="alert alert-info mb-0">
                                    <strong>Rescheduled:</strong> {{ $appointment->reschedule_reason }}
                                    @if($appointment->rescheduled_at)
                                        <br><small>{{ $appointment->rescheduled_at->format('M d, Y h:i A') }}</small>
                                    @endif
                                </div>
                            </div>
                        @endif
                        @if($appointment->cancellation_reason)
                            <div class="col-12">
                                <div class="alert alert-danger mb-0">
                                    <strong>Cancellation reason:</strong> {{ $appointment->cancellation_reason }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if(in_array($appointment->status, [\App\Enums\AppointmentStatus::Pending, \App\Enums\AppointmentStatus::Approved]))
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 text-danger">Cancel Appointment</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('client.appointments.cancel', $appointment) }}">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3">
                                <label for="cancellation_reason" class="form-label">Reason for cancellation</label>
                                <textarea class="form-control @error('cancellation_reason') is-invalid @enderror"
                                          id="cancellation_reason" name="cancellation_reason" rows="3" required>{{ old('cancellation_reason') }}</textarea>
                                @error('cancellation_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn btn-outline-danger"
                                    onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                Cancel Appointment
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            @if($appointment->canBeReviewed())
                <div class="mt-4">
                    @include('partials.review-form', [
                        'action' => route('client.reviews.appointment', $appointment),
                        'formId' => 'appt_'.$appointment->id,
                    ])
                </div>
            @elseif($appointment->review)
                <div class="mt-4">
                    @include('partials.review-display', ['review' => $appointment->review])
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <img src="{{ $appointment->lawyer->user->profile_photo_url }}" class="lawyer-profile-photo rounded-circle mb-3" alt="">
                    <h6>{{ $appointment->lawyer->user->name }}</h6>
                    <a href="{{ route('lawyers.show', $appointment->lawyer) }}" class="btn btn-outline-primary btn-sm w-100 mb-2">View Profile</a>
                    <form method="POST" action="{{ route('client.chat.start', $appointment->lawyer) }}">
                        @csrf
                        <button type="submit" class="btn btn-btn-navy btn-sm w-100">Message Lawyer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
