@extends('layouts.app')

@section('title', 'Review Lawyer')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.lawyers.index') }}">Lawyers</a></li>
            <li class="breadcrumb-item active">{{ $lawyer->user->name }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0">Application Details</h5>
                    @if($lawyer->is_approved)
                        <span class="badge bg-success">Approved</span>
                    @else
                        <span class="badge bg-warning text-dark">Pending Approval</span>
                    @endif
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <h6 class="text-muted">Full Name</h6>
                            <p class="mb-0 fw-semibold">{{ $lawyer->user->name }}</p>
                        </div>
                        <div class="col-sm-6">
                            <h6 class="text-muted">Email</h6>
                            <p class="mb-0">{{ $lawyer->user->email }}</p>
                        </div>
                        <div class="col-sm-6">
                            <h6 class="text-muted">Phone</h6>
                            <p class="mb-0">{{ $lawyer->user->phone ?? 'Not provided' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <h6 class="text-muted">Bar Number</h6>
                            <p class="mb-0">{{ $lawyer->bar_number ?? 'Not provided' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <h6 class="text-muted">Specialization</h6>
                            <p class="mb-0">{{ $lawyer->specialization }}</p>
                        </div>
                        <div class="col-sm-6">
                            <h6 class="text-muted">Experience</h6>
                            <p class="mb-0">{{ $lawyer->experience_years }} years</p>
                        </div>
                        @if($lawyer->user->address)
                            <div class="col-12">
                                <h6 class="text-muted">Address</h6>
                                <p class="mb-0">{{ $lawyer->user->address }}</p>
                            </div>
                        @endif
                        <div class="col-12">
                            <h6 class="text-muted">Qualifications</h6>
                            <p class="mb-0">{{ $lawyer->qualifications ?? 'Not provided' }}</p>
                        </div>
                        <div class="col-12">
                            <h6 class="text-muted">Biography</h6>
                            <p class="mb-0">{{ $lawyer->biography ?? 'Not provided' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <h6 class="text-muted">Applied On</h6>
                            <p class="mb-0">{{ $lawyer->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        @if($lawyer->approved_at)
                            <div class="col-sm-6">
                                <h6 class="text-muted">Approved On</h6>
                                <p class="mb-0">{{ $lawyer->approved_at->format('M d, Y h:i A') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @unless($lawyer->is_approved)
                <div class="card border-0 shadow-sm border-danger">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 text-danger"><i class="bi bi-x-circle me-1"></i>Reject Application</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.lawyers.reject', $lawyer) }}"
                              onsubmit="return confirm('Reject this lawyer application? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <div class="mb-3">
                                <label for="rejection_reason" class="form-label">Reason for rejection</label>
                                <textarea class="form-control @error('rejection_reason') is-invalid @enderror"
                                          id="rejection_reason" name="rejection_reason" rows="3" required
                                          placeholder="Provide a reason (stored in activity log)...">{{ old('rejection_reason') }}</textarea>
                                @error('rejection_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn btn-outline-danger">Reject Application</button>
                        </form>
                    </div>
                </div>
            @endunless
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <img src="{{ $lawyer->user->profile_photo_url }}" class="lawyer-profile-photo rounded-circle mb-3" alt="">
                    <h6>{{ $lawyer->user->name }}</h6>
                    <p class="text-muted small">{{ $lawyer->specialization }}</p>

                    @unless($lawyer->is_approved)
                        <form method="POST" action="{{ route('admin.lawyers.approve', $lawyer) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success w-100 mb-2"
                                    onclick="return confirm('Approve this lawyer?')">
                                <i class="bi bi-check-lg me-1"></i> Approve Lawyer
                            </button>
                        </form>
                    @else
                        <div class="alert alert-success mb-0 small">
                            <i class="bi bi-check-circle me-1"></i> This lawyer is approved and visible to clients.
                        </div>
                    @endunless

                    <a href="{{ route('admin.lawyers.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                        Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
