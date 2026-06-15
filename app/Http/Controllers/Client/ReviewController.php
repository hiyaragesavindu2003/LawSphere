<?php

namespace App\Http\Controllers\Client;

use App\Enums\AppointmentStatus;
use App\Enums\LegalRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\LegalRequest;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        $client = auth()->user()->client;

        $reviews = $client->reviews()
            ->with(['lawyer.user', 'appointment', 'legalRequest'])
            ->latest()
            ->paginate(10);

        $pendingAppointments = $client->appointments()
            ->with(['lawyer.user', 'review'])
            ->where('status', AppointmentStatus::Completed)
            ->whereDoesntHave('review')
            ->latest()
            ->get()
            ->filter(fn (Appointment $a) => $a->canBeReviewed());

        $pendingLegalRequests = $client->legalRequests()
            ->with(['lawyer.user', 'review', 'responses'])
            ->whereIn('status', [LegalRequestStatus::Resolved, LegalRequestStatus::Closed])
            ->whereDoesntHave('review')
            ->latest()
            ->get()
            ->filter(fn (LegalRequest $r) => $r->canBeReviewed());

        return view('client.reviews.index', compact('reviews', 'pendingAppointments', 'pendingLegalRequests'));
    }

    public function storeAppointment(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorizeClient();
        $this->authorizeAppointmentReview($appointment);

        $validated = $this->validateReview($request);

        Review::create([
            'client_id' => auth()->user()->client->id,
            'lawyer_id' => $appointment->lawyer_id,
            'appointment_id' => $appointment->id,
            'rating' => $validated['rating'],
            'review_text' => $validated['review_text'],
        ]);

        return back()->with('status', 'Thank you! Your review has been submitted.');
    }

    public function storeLegalRequest(Request $request, LegalRequest $legalRequest): RedirectResponse
    {
        $this->authorizeClient();
        $this->authorizeLegalRequestReview($legalRequest);

        $validated = $this->validateReview($request);

        Review::create([
            'client_id' => auth()->user()->client->id,
            'lawyer_id' => $legalRequest->lawyer_id,
            'legal_request_id' => $legalRequest->id,
            'rating' => $validated['rating'],
            'review_text' => $validated['review_text'],
        ]);

        return back()->with('status', 'Thank you! Your review has been submitted.');
    }

    private function validateReview(Request $request): array
    {
        return $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review_text' => ['required', 'string', 'min:10', 'max:1000'],
        ]);
    }

    private function authorizeClient(): void
    {
        abort_unless(auth()->user()->isClient(), 403);
    }

    private function authorizeAppointmentReview(Appointment $appointment): void
    {
        abort_unless(
            $appointment->client_id === auth()->user()->client->id,
            403
        );
        abort_unless($appointment->canBeReviewed(), 403, 'You can only review after a completed consultation.');
    }

    private function authorizeLegalRequestReview(LegalRequest $legalRequest): void
    {
        abort_unless(
            $legalRequest->client_id === auth()->user()->client->id,
            403
        );
        abort_unless($legalRequest->canBeReviewed(), 403, 'You can only review after receiving legal advice.');
    }
}
