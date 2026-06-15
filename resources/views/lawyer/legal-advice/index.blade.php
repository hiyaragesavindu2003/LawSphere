@extends('layouts.app')

@section('title', 'Legal Advice Requests')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="bi bi-journal-text me-2"></i>Legal Advice</h2>
            <p class="text-muted mb-0">Review and respond to client legal questions</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach([
            ['label' => 'Pending', 'value' => $stats['pending'], 'class' => 'border-warning'],
            ['label' => 'In Progress', 'value' => $stats['in_progress'], 'class' => 'border-primary'],
            ['label' => 'Resolved', 'value' => $stats['resolved'], 'class' => 'border-success'],
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
                    @foreach(\App\Enums\LegalRequestStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected(request('status') === $status->value)>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    @if($requests->isEmpty())
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body">
                <i class="bi bi-journal-x display-4 text-muted mb-3"></i>
                <h5>No advice requests yet</h5>
                <p class="text-muted mb-0">Client requests will appear here when submitted.</p>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($requests as $request)
                <div class="col-12">
                    <div class="card border-0 shadow-sm legal-advice-card">
                        <div class="card-body p-4">
                            <div class="row align-items-center g-3">
                                <div class="col-md-4">
                                    <h6 class="mb-1">{{ $request->subject }}</h6>
                                    <small class="text-muted">
                                        <i class="bi bi-person me-1"></i>{{ $request->client->user->name }}
                                    </small>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Submitted</small>
                                    <strong>{{ $request->created_at->format('M d, Y') }}</strong>
                                </div>
                                <div class="col-md-3">
                                    <span class="badge {{ $request->status->badgeClass() }}">
                                        {{ $request->status->label() }}
                                    </span>
                                    @if($request->status === \App\Enums\LegalRequestStatus::Pending)
                                        <br><small class="text-warning">Needs response</small>
                                    @endif
                                </div>
                                <div class="col-md-2 text-md-end">
                                    <a href="{{ route('lawyer.legal-advice.show', $request) }}" class="btn btn-btn-navy btn-sm">
                                        Review <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $requests->links() }}</div>
    @endif
</div>
@endsection
