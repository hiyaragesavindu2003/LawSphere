@extends('layouts.app')

@section('title', $legalRequest->subject)

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('client.legal-advice.index') }}">Legal Advice</a></li>
            <li class="breadcrumb-item active">{{ Str::limit($legalRequest->subject, 40) }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0">{{ $legalRequest->subject }}</h5>
                    <div class="d-flex align-items-center gap-2">
                        @include('partials.payment-status', ['payment' => $legalRequest->payment])
                        <span class="badge {{ $legalRequest->status->badgeClass() }} fs-6">
                            {{ $legalRequest->status->label() }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <h6 class="text-muted">Your question</h6>
                    <p class="mb-3">{{ $legalRequest->description }}</p>
                    <small class="text-muted">Submitted {{ $legalRequest->created_at->format('M d, Y h:i A') }}</small>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-chat-left-quote me-2"></i>Lawyer Responses</h5>
                </div>
                <div class="card-body p-4">
                    @forelse($legalRequest->responses as $response)
                        <div class="legal-advice-response mb-4 {{ !$loop->last ? 'border-bottom pb-4' : '' }}">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <img src="{{ $response->lawyer->user->profile_photo_url }}"
                                     class="chat-list-avatar rounded-circle" alt="">
                                <div>
                                    <strong>{{ $response->lawyer->user->name }}</strong>
                                    <br><small class="text-muted">{{ $response->created_at->format('M d, Y h:i A') }}</small>
                                </div>
                            </div>
                            <p class="mb-0">{{ $response->response_text }}</p>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-hourglass-split display-6 mb-2 d-block"></i>
                            <p class="mb-0">No response yet. The lawyer will review your request soon.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            @if($legalRequest->status !== \App\Enums\LegalRequestStatus::Closed)
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body">
                        <form method="POST" action="{{ route('client.legal-advice.close', $legalRequest) }}"
                              onsubmit="return confirm('Close this request? You can still view it later.')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-x-circle me-1"></i> Close Request
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <img src="{{ $legalRequest->lawyer->user->profile_photo_url }}"
                         class="lawyer-profile-photo rounded-circle mb-3" alt="">
                    <h6>{{ $legalRequest->lawyer->user->name }}</h6>
                    <p class="text-muted small">{{ $legalRequest->lawyer->specialization }}</p>
                    <a href="{{ route('lawyers.show', $legalRequest->lawyer) }}" class="btn btn-outline-primary btn-sm w-100 mb-2">
                        View Profile
                    </a>
                    <form method="POST" action="{{ route('client.chat.start', $legalRequest->lawyer) }}">
                        @csrf
                        <button type="submit" class="btn btn-btn-navy btn-sm w-100">Message Lawyer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
