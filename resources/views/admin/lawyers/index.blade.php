@extends('layouts.app')

@section('title', 'Manage Lawyers')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="mb-1"><i class="bi bi-briefcase me-2"></i>Lawyer Management</h2>
            <p class="text-muted mb-0">Review and approve lawyer registration requests</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Dashboard
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card dashboard-stat h-100 border-warning">
                <div class="card-body">
                    <p class="text-muted mb-1 small">Pending Approval</p>
                    <h3 class="mb-0">{{ $stats['pending'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card dashboard-stat h-100 border-success">
                <div class="card-body">
                    <p class="text-muted mb-1 small">Approved Lawyers</p>
                    <h3 class="mb-0">{{ $stats['approved'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" class="d-flex gap-2 flex-wrap">
                <select name="filter" class="form-select w-auto" onchange="this.form.submit()">
                    <option value="">All lawyers</option>
                    <option value="pending" @selected(request('filter') === 'pending')>Pending only</option>
                    <option value="approved" @selected(request('filter') === 'approved')>Approved only</option>
                </select>
            </form>
        </div>
    </div>

    @if($lawyers->isEmpty())
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body">
                <i class="bi bi-person-check display-4 text-muted mb-3"></i>
                <h5>No lawyers found</h5>
                <p class="text-muted mb-0">No lawyers match the current filter.</p>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Lawyer</th>
                            <th>Specialization</th>
                            <th>Bar #</th>
                            <th>Registered</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lawyers as $lawyer)
                            <tr>
                                <td>
                                    <strong>{{ $lawyer->user->name }}</strong>
                                    <br><small class="text-muted">{{ $lawyer->user->email }}</small>
                                </td>
                                <td>{{ $lawyer->specialization }}</td>
                                <td>{{ $lawyer->bar_number ?? '—' }}</td>
                                <td>{{ $lawyer->created_at->format('M d, Y') }}</td>
                                <td>
                                    @if($lawyer->is_approved)
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.lawyers.show', $lawyer) }}" class="btn btn-sm btn-btn-navy">
                                        Review
                                    </a>
                                    @unless($lawyer->is_approved)
                                        <form method="POST" action="{{ route('admin.lawyers.approve', $lawyer) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success"
                                                    onclick="return confirm('Approve {{ $lawyer->user->name }}?')">
                                                Approve
                                            </button>
                                        </form>
                                    @endunless
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $lawyers->links() }}</div>
    @endif
</div>
@endsection
