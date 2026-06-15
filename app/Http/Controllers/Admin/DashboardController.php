<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\Lawyer;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_lawyers' => Lawyer::count(),
            'pending_approvals' => Lawyer::where('is_approved', false)->count(),
            'total_appointments' => Appointment::count(),
            'active_memberships' => Membership::where('status', 'active')->count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
        ];

        $pendingLawyers = Lawyer::with('user')
            ->where('is_approved', false)
            ->latest()
            ->take(5)
            ->get();

        $recentActivity = ActivityLog::with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'pendingLawyers', 'recentActivity'));
    }
}
