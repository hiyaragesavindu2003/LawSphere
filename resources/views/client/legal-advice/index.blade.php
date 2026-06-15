@extends('layouts.app')

@section('title', 'Legal Advice')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="mb-1"><i class="bi bi-journal-text me-2"></i>Legal Advice</h2>
            <p class="text-muted mb-0">Your submitted advice requests and lawyer responses</p>
        </div>
        <a href="{{ route('lawyers.index') }}" class="btn btn-btn-gold">
            <i class="bi bi-plus-lg me-1"></i> New Request
        </a>
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
                <p class="text-muted">Find a lawyer and submit your first legal question.</p>
                <a href="{{ route('lawyers.index') }}" class="btn btn-btn-navy">Find a Lawyer</a>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($requests as $request)
                <div class="col-12">
                    <div class="card border-0 shadow-sm legal-advice-card">
                        <div class="card-body p-4">
                            <div class="row align-items-center g-3">
                                <div class="col-md-5">
                                    <h6 class="mb-1">{{ $request->subject }}</h6>
                                    <small class="text-muted">
                                        <i class="bi bi-person me-1"></i>{{ $request->lawyer->user->name }}
                                        · {{ $request->lawyer->specialization }}
                                    </small>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Submitted</small>
                                    <strong>{{ $request->created_at->format('M d, Y') }}</strong>
                                </div>
                                <div class="col-md-2">
                                    <span class="badge {{ $request->status->badgeClass() }}">
                                        {{ $request->status->label() }}
                                    </span>
                                    @if($request->responses->count())
                                        <br><small class="text-muted">{{ $request->responses->count() }} response(s)</small>
                                    @endif
                                </div>
                                <div class="col-md-2 text-md-end">
                                    <a href="{{ route('client.legal-advice.show', $request) }}" class="btn btn-btn-navy btn-sm">
                                        View <i class="bi bi-arrow-right"></i>
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
