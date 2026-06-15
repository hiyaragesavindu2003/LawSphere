<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\LegalRequest;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $lawyer = auth()->user()->lawyer;

        $stats = [
            'total_appointments' => $lawyer->appointments()->count(),
            'pending_appointments' => $lawyer->appointments()->pending()->count(),
            'pending_requests' => LegalRequest::where('lawyer_id', $lawyer->id)->pending()->count(),
            'average_rating' => $lawyer->average_rating,
        ];

        $recentAppointments = $lawyer->appointments()
            ->with('client.user')
            ->latest()
            ->take(5)
            ->get();

        $recentReviews = $lawyer->reviews()
            ->with('client.user')
            ->latest()
            ->take(5)
            ->get();

        return view('lawyer.dashboard', compact('stats', 'recentAppointments', 'recentReviews'));
    }

    public function pendingApproval(): View
    {
        return view('lawyer.pending-approval');
    }
}
