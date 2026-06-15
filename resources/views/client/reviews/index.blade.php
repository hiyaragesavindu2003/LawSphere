@extends('layouts.app')

@section('title', 'My Reviews')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="mb-1"><i class="bi bi-star me-2"></i>My Reviews</h2>
            <p class="text-muted mb-0">Rate lawyers after completed consultations or legal advice</p>
        </div>
    </div>

    @if($pendingAppointments->isNotEmpty() || $pendingLegalRequests->isNotEmpty())
        <div class="card border-0 shadow-sm mb-4 border-warning">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Pending Reviews</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($pendingAppointments as $appointment)
                        <div class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <strong>{{ $appointment->lawyer->user->name }}</strong>
                                <span class="badge bg-light text-dark ms-1">Consultation</span>
                                <br><small class="text-muted">{{ $appointment->formatted_date_time }}</small>
                            </div>
                            <a href="{{ route('client.appointments.show', $appointment) }}" class="btn btn-btn-gold btn-sm">
                                Leave Review
                            </a>
                        </div>
                    @endforeach
                    @foreach($pendingLegalRequests as $request)
                        <div class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <strong>{{ $request->lawyer->user->name }}</strong>
                                <span class="badge bg-light text-dark ms-1">Legal Advice</span>
                                <br><small class="text-muted">{{ Str::limit($request->subject, 50) }}</small>
                            </div>
                            <a href="{{ route('client.legal-advice.show', $request) }}" class="btn btn-btn-gold btn-sm">
                                Leave Review
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <h5 class="mb-3">Reviews You've Written</h5>

    @if($reviews->isEmpty())
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body">
                <i class="bi bi-star display-4 text-muted mb-3"></i>
                <h5>No reviews yet</h5>
                <p class="text-muted">After a completed appointment or resolved legal advice, you can rate your lawyer here.</p>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($reviews as $review)
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div>
                                    <h6 class="mb-1">{{ $review->lawyer->user->name }}</h6>
                                    <small class="text-muted">
                                        @if($review->appointment)
                                            Consultation · {{ $review->appointment->formatted_date_time }}
                                        @elseif($review->legalRequest)
                                            Legal Advice · {{ $review->legalRequest->subject }}
                                        @endif
                                    </small>
                                </div>
                                <div class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <p class="mb-0 mt-2">{{ $review->review_text }}</p>
                            <small class="text-muted">{{ $review->created_at->format('M d, Y') }}</small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $reviews->links() }}</div>
    @endif
</div>
@endsection
