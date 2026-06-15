@extends('layouts.app')

@section('title', 'Appointments')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="bi bi-calendar-check me-2"></i>Appointments</h2>
            <p class="text-muted mb-0">Manage client consultation requests</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach([
            ['label' => 'Pending', 'value' => $stats['pending'], 'class' => 'border-warning'],
            ['label' => 'Approved', 'value' => $stats['approved'], 'class' => 'border-primary'],
            ['label' => 'Completed', 'value' => $stats['completed'], 'class' => 'border-success'],
        ] as $stat)
            <div class="col-md-4">
                <div class="card dashboard-stat h-100 {{ $stat['class'] }}">
                    <div class="card-body">
                        <p class="text-muted mb-1 small">{{ $stat['label'] }}</p>
                        <h3 class="mb-0">{{ $stat['value'] }}</h3>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET">
                <select name="status" class="form-select w-auto" onchange="this.form.submit()">
                    <option value="">All statuses</option>
                    @foreach(\App\Enums\AppointmentStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected(request('status') === $status->value)>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    @if($appointments->isEmpty())
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body">
                <i class="bi bi-calendar-x display-4 text-muted mb-3"></i>
                <h5>No appointments yet</h5>
                <p class="text-muted mb-0">Client booking requests will appear here.</p>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($appointments as $appointment)
                <div class="col-12">
                    <div class="card border-0 shadow-sm appointment-list-card">
                        <div class="card-body p-4">
                            <div class="row align-items-center g-3">
                                <div class="col-md-4">
                                    <h6 class="mb-0">{{ $appointment->client->user->name }}</h6>
                                    <small class="text-muted">{{ $appointment->client->user->email }}</small>
                                </div>
                                <div class="col-md-3">
                                    <strong>{{ $appointment->formatted_date_time }}</strong>
                                </div>
                                <div class="col-md-2">
                                    <span class="badge {{ $appointment->status->badgeClass() }}">
                                        {{ $appointment->status->label() }}
                                    </span>
                                </div>
                                <div class="col-md-3 text-md-end">
                                    <a href="{{ route('lawyer.appointments.show', $appointment) }}" class="btn btn-btn-navy btn-sm">
                                        Manage <i class="bi bi-arrow-right"></i>
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
