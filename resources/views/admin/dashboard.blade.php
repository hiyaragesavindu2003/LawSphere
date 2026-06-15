@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="mb-0"><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</h2>
        <a href="{{ route('admin.lawyers.index', ['filter' => 'pending']) }}" class="btn btn-btn-gold">
            <i class="bi bi-person-check me-1"></i> Manage Lawyers
            @if($stats['pending_approvals'] > 0)
                <span class="badge bg-danger ms-1">{{ $stats['pending_approvals'] }}</span>
            @endif
        </a>
    </div>

    <div class="row g-4 mb-4">
        @foreach([
            ['label' => 'Total Users', 'value' => $stats['total_users'], 'icon' => 'people'],
            ['label' => 'Total Lawyers', 'value' => $stats['total_lawyers'], 'icon' => 'briefcase'],
            ['label' => 'Pending Approvals', 'value' => $stats['pending_approvals'], 'icon' => 'hourglass'],
            ['label' => 'Appointments', 'value' => $stats['total_appointments'], 'icon' => 'calendar'],
            ['label' => 'Active Memberships', 'value' => $stats['active_memberships'], 'icon' => 'award'],
            ['label' => 'Total Revenue', 'value' => config('lawsphere.currency_symbol').number_format($stats['total_revenue'], 2), 'icon' => 'currency-dollar'],
        ] as $stat)
        <div class="col-md-4 col-lg">
            <div class="card dashboard-stat h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">{{ $stat['label'] }}</p>
                            <h3 class="mb-0">{{ $stat['value'] }}</h3>
                        </div>
                        <i class="bi bi-{{ $stat['icon'] }} fs-3 text-gold"></i>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Pending Lawyer Approvals</h5>
                    <a href="{{ route('admin.lawyers.index', ['filter' => 'pending']) }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @forelse($pendingLawyers as $lawyer)
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom gap-2">
                            <div>
                                <strong>{{ $lawyer->user->name }}</strong>
                                <br><small class="text-muted">{{ $lawyer->specialization }} · {{ $lawyer->user->email }}</small>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                <a href="{{ route('admin.lawyers.show', $lawyer) }}" class="btn btn-sm btn-btn-navy">Review</a>
                                <form method="POST" action="{{ route('admin.lawyers.approve', $lawyer) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success"
                                            onclick="return confirm('Approve {{ $lawyer->user->name }}?')">
                                        Approve
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No pending approvals.</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Recent Activity</h5>
                </div>
                <div class="card-body">
                    @forelse($recentActivity as $log)
                        <div class="py-2 border-bottom">
                            <strong>{{ $log->action }}</strong>
                            <br><small class="text-muted">{{ $log->description }} — {{ $log->created_at->diffForHumans() }}</small>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No recent activity.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
