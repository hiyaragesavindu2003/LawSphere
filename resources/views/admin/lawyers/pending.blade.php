@extends('layouts.app')

@section('title', 'Pending Lawyer Approvals')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="mb-1"><i class="bi bi-hourglass-split me-2"></i>Pending Lawyer Approvals</h2>
            <p class="text-muted mb-0">Review and approve lawyer registration requests</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Admin Dashboard
        </a>
    </div>

    @if($pendingLawyers->isEmpty())
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body">
                <i class="bi bi-check-circle display-4 text-success mb-3"></i>
                <h5>All caught up</h5>
                <p class="text-muted mb-0">No lawyers are waiting for approval.</p>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($pendingLawyers as $lawyer)
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="row align-items-center g-3">
                                <div class="col-md-5">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $lawyer->user->profile_photo_url }}"
                                             class="chat-list-avatar rounded-circle" alt="">
                                        <div>
                                            <h6 class="mb-0">{{ $lawyer->user->name }}</h6>
                                            <small class="text-muted">{{ $lawyer->user->email }}</small>
                                            <br><span class="badge lawyer-spec-badge mt-1">{{ $lawyer->specialization }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Experience</small>
                                    <strong>{{ $lawyer->experience_years }} years</strong>
                                    <br><small class="text-muted">Bar #{{ $lawyer->bar_number }}</small>
                                </div>
                                <div class="col-md-2">
                                    <small class="text-muted d-block">Applied</small>
                                    <strong>{{ $lawyer->created_at->format('M d, Y') }}</strong>
                                </div>
                                <div class="col-md-2 text-md-end">
                                    <a href="{{ route('admin.lawyers.review', $lawyer) }}" class="btn btn-btn-navy btn-sm w-100 mb-1">
                                        Review
                                    </a>
                                    <form method="POST" action="{{ route('admin.lawyers.approve', $lawyer) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm w-100"
                                                onclick="return confirm('Approve {{ $lawyer->user->name }}?')">
                                            Approve
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $pendingLawyers->links() }}</div>
    @endif
</div>
@endsection
