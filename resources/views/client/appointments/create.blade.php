@extends('layouts.app')

@section('title', 'Book Appointment')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('lawyers.index') }}">Find a Lawyer</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lawyers.show', $lawyer) }}">{{ $lawyer->user->name }}</a></li>
            <li class="breadcrumb-item active">Book Appointment</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm appointment-booking-card">
                <div class="card-header appointment-booking-header py-4">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ $lawyer->user->profile_photo_url }}" alt="{{ $lawyer->user->name }}"
                             class="chat-list-avatar rounded-circle">
                        <div>
                            <h4 class="mb-1 text-white">Book Consultation</h4>
                            <p class="mb-0 text-white-50">{{ $lawyer->user->name }} — {{ $lawyer->specialization }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('client.appointments.store', $lawyer) }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="appointment_date" class="form-label fw-semibold">
                                    <i class="bi bi-calendar-event text-gold me-1"></i> Date
                                </label>
                                <input type="date" class="form-control form-control-lg @error('appointment_date') is-invalid @enderror"
                                       id="appointment_date" name="appointment_date"
                                       value="{{ old('appointment_date') }}"
                                       min="{{ date('Y-m-d') }}" required>
                                @error('appointment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="appointment_time" class="form-label fw-semibold">
                                    <i class="bi bi-clock text-gold me-1"></i> Time
                                </label>
                                <input type="time" class="form-control form-control-lg @error('appointment_time') is-invalid @enderror"
                                       id="appointment_time" name="appointment_time"
                                       value="{{ old('appointment_time', '09:00') }}"
                                       min="08:00" max="18:00" required>
                                @error('appointment_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="text-muted">Office hours: 8:00 AM – 6:00 PM</small>
                            </div>
                            <div class="col-12">
                                <label for="notes" class="form-label fw-semibold">
                                    <i class="bi bi-card-text text-gold me-1"></i> Notes (optional)
                                </label>
                                <textarea class="form-control @error('notes') is-invalid @enderror"
                                          id="notes" name="notes" rows="4"
                                          placeholder="Briefly describe the reason for your consultation...">{{ old('notes') }}</textarea>
                                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="alert alert-light border mt-4 mb-0">
                            <i class="bi bi-info-circle text-gold me-1"></i>
                            Your request will be sent to the lawyer for <strong>approval</strong> after payment.
                        </div>

                        <div class="payment-fee-box d-flex justify-content-between align-items-center mt-4 p-3 rounded">
                            <span class="fw-semibold"><i class="bi bi-credit-card text-gold me-1"></i> Consultation fee</span>
                            <span class="fs-5 fw-bold text-gold">{{ config('lawsphere.currency_symbol') }}{{ number_format($consultationFee, 2) }}</span>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-btn-gold btn-lg flex-grow-1">
                                <i class="bi bi-calendar-check me-1"></i> Request Appointment
                            </button>
                            <a href="{{ route('lawyers.show', $lawyer) }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
