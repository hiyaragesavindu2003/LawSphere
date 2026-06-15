<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\LegalRequest;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $client = auth()->user()->client;

        $upcomingAppointments = $client->appointments()
            ->with('lawyer.user')
            ->upcoming()
            ->orderBy('appointment_date')
            ->take(5)
            ->get();

        $recentRequests = $client->legalRequests()
            ->with('lawyer.user')
            ->latest()
            ->take(5)
            ->get();

        $pendingReviews = $client->appointments()
            ->where('status', \App\Enums\AppointmentStatus::Completed)
            ->whereDoesntHave('review')
            ->with('lawyer')
            ->get()
            ->filter(fn ($a) => $a->canBeReviewed())
            ->count()
            + $client->legalRequests()
                ->whereIn('status', [\App\Enums\LegalRequestStatus::Resolved, \App\Enums\LegalRequestStatus::Closed])
                ->whereDoesntHave('review')
                ->with(['lawyer', 'responses'])
                ->get()
                ->filter(fn ($r) => $r->canBeReviewed())
                ->count();

        $stats = [
            'upcoming_appointments' => $client->appointments()->upcoming()->count(),
            'total_appointments' => $client->appointments()->count(),
            'pending_requests' => $client->legalRequests()->pending()->count(),
            'total_reviews' => $client->reviews()->count(),
            'pending_reviews' => $pendingReviews,
        ];

        return view('client.dashboard', compact('stats', 'upcomingAppointments', 'recentRequests'));
    }
}
