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

        $stats = [
            'upcoming_appointments' => $client->appointments()->upcoming()->count(),
            'total_appointments' => $client->appointments()->count(),
            'pending_requests' => $client->legalRequests()->pending()->count(),
            'total_reviews' => $client->reviews()->count(),
        ];

        return view('client.dashboard', compact('stats', 'upcomingAppointments', 'recentRequests'));
    }
}
