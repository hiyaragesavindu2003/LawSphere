@extends('layouts.app')

@section('title', 'Review Request')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('lawyer.legal-advice.index') }}">Legal Advice</a></li>
            <li class="breadcrumb-item active">#{{ $legalRequest->id }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between py-3">
                    <h5 class="mb-0">{{ $legalRequest->subject }}</h5>
                    <div class="d-flex align-items-center gap-2">
                        @include('partials.payment-status', ['payment' => $legalRequest->payment, 'isPaid' => $legalRequest->isPaid()])
                        <span class="badge {{ $legalRequest->status->badgeClass() }}">{{ $legalRequest->status->label() }}</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <h6 class="text-muted">Client</h6>
                            <p class="mb-0 fw-semibold">{{ $legalRequest->client->user->name }}</p>
                            <small>{{ $legalRequest->client->user->email }}</small>
                        </div>
                        <div class="col-sm-6">
                            <h6 class="text-muted">Submitted</h6>
                            <p class="mb-0 fw-semibold">{{ $legalRequest->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    <h6 class="text-muted">Client's question</h6>
                    <p class="mb-0">{{ $legalRequest->description }}</p>
                </div>
            </div>

            @if($legalRequest->responses->isNotEmpty())
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Previous Responses</h5>
                    </div>
                    <div class="card-body p-4">
                        @foreach($legalRequest->responses as $response)
                            <div class="mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                                <small class="text-muted">{{ $response->created_at->format('M d, Y h:i A') }}</small>
                                <p class="mb-0 mt-1">{{ $response->response_text }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(in_array($legalRequest->status, [\App\Enums\LegalRequestStatus::Pending, \App\Enums\LegalRequestStatus::InProgress]))
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-reply me-2"></i>Send Response</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('lawyer.legal-advice.respond', $legalRequest) }}">
                            @csrf
                            <div class="mb-3">
                                <label for="response_text" class="form-label">Your professional advice</label>
                                <textarea class="form-control @error('response_text') is-invalid @enderror"
                                          id="response_text" name="response_text" rows="6" required
                                          placeholder="Provide clear, professional guidance for the client...">{{ old('response_text') }}</textarea>
                                @error('response_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn btn-btn-gold">
                                <i class="bi bi-send me-1"></i> Send Response
                            </button>
                        </form>
                        @if($legalRequest->responses->isNotEmpty())
                            <form method="POST" action="{{ route('lawyer.legal-advice.resolve', $legalRequest) }}" class="mt-3">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success"
                                        onclick="return confirm('Mark this request as resolved?')">
                                    <i class="bi bi-check-circle me-1"></i> Mark Resolved
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @elseif($legalRequest->status === \App\Enums\LegalRequestStatus::Resolved)
                <div class="alert alert-success">
                    <i class="bi bi-check-circle me-1"></i> This request has been marked as resolved.
                </div>
            @elseif($legalRequest->status === \App\Enums\LegalRequestStatus::Closed)
                <div class="alert alert-secondary">
                    <i class="bi bi-x-circle me-1"></i> The client has closed this request.
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="text-muted mb-3">Quick actions</h6>
                    <a href="{{ route('lawyer.chat.index') }}" class="btn btn-outline-primary btn-sm w-100 mb-2">
                        <i class="bi bi-chat-dots me-1"></i> Messages
                    </a>
                    <a href="{{ route('lawyer.legal-advice.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bi bi-arrow-left me-1"></i> All Requests
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
