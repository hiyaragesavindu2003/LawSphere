@extends('layouts.app')

@section('title', 'Review Lawyer Application')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.lawyers.pending') }}">Pending Approvals</a></li>
            <li class="breadcrumb-item active">{{ $lawyer->user->name }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Application Details</h5>
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
                            <p class="mb-0">{{ $lawyer->user->phone ?? 'N/A' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <h6 class="text-muted">Bar Number</h6>
                            <p class="mb-0">{{ $lawyer->bar_number }}</p>
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
                        <div class="col-12">
                            <h6 class="text-muted">Applied On</h6>
                            <p class="mb-0">{{ $lawyer->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center p-4">
                    <img src="{{ $lawyer->user->profile_photo_url }}" class="lawyer-profile-photo rounded-circle mb-3" alt="">
                    <h6>{{ $lawyer->user->name }}</h6>
                    <span class="badge bg-warning text-dark">Pending Approval</span>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 text-success">Approve Application</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted">The lawyer will gain full access to the dashboard, appointments, and legal advice portal.</p>
                    <form method="POST" action="{{ route('admin.lawyers.approve', $lawyer) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-lg me-1"></i> Approve Lawyer
                        </button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 text-danger">Reject Application</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.lawyers.reject', $lawyer) }}">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label small">Reason (optional)</label>
                            <textarea class="form-control @error('rejection_reason') is-invalid @enderror"
                                      id="rejection_reason" name="rejection_reason" rows="3"
                                      placeholder="e.g. Invalid bar number...">{{ old('rejection_reason') }}</textarea>
                            @error('rejection_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-outline-danger w-100"
                                onclick="return confirm('Reject this application? The account will be deactivated.')">
                            <i class="bi bi-x-lg me-1"></i> Reject
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
