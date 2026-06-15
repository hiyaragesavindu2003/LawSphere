<?php

namespace App\Http\Controllers\Lawyer;

use App\Enums\LegalRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\LegalRequest;
use App\Models\LegalResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LegalAdviceController extends Controller
{
    public function index(Request $request): View
    {
        $lawyer = auth()->user()->lawyer;

        $query = $lawyer->legalRequests()
            ->with(['client.user', 'responses'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(10)->withQueryString();

        $stats = [
            'pending' => $lawyer->legalRequests()->pending()->count(),
            'in_progress' => $lawyer->legalRequests()->where('status', LegalRequestStatus::InProgress)->count(),
            'resolved' => $lawyer->legalRequests()->where('status', LegalRequestStatus::Resolved)->count(),
        ];

        return view('lawyer.legal-advice.index', compact('requests', 'stats'));
    }

    public function show(LegalRequest $legalRequest): View
    {
        $this->authorizeLawyer($legalRequest);

        $legalRequest->load(['lawyer.user', 'client.user', 'responses.lawyer.user', 'payment']);

        return view('lawyer.legal-advice.show', compact('legalRequest'));
    }

    public function respond(Request $request, LegalRequest $legalRequest): RedirectResponse
    {
        $this->authorizeLawyer($legalRequest);

        abort_unless(
            in_array($legalRequest->status, [LegalRequestStatus::Pending, LegalRequestStatus::InProgress], true),
            403
        );

        $validated = $request->validate([
            'response_text' => ['required', 'string', 'max:5000'],
        ]);

        LegalResponse::create([
            'legal_request_id' => $legalRequest->id,
            'lawyer_id' => auth()->user()->lawyer->id,
            'response_text' => $validated['response_text'],
        ]);

        if ($legalRequest->status === LegalRequestStatus::Pending) {
            $legalRequest->update(['status' => LegalRequestStatus::InProgress]);
        }

        return back()->with('status', 'Response sent to the client.');
    }

    public function resolve(LegalRequest $legalRequest): RedirectResponse
    {
        $this->authorizeLawyer($legalRequest);

        abort_unless(
            in_array($legalRequest->status, [LegalRequestStatus::InProgress, LegalRequestStatus::Pending], true),
            403
        );

        abort_unless($legalRequest->responses()->exists(), 403, 'Please send at least one response before marking as resolved.');

        $legalRequest->update(['status' => LegalRequestStatus::Resolved]);

        return back()->with('status', 'Request marked as resolved.');
    }

    private function authorizeLawyer(LegalRequest $legalRequest): void
    {
        abort_unless(
            auth()->user()->isLawyer()
            && $legalRequest->lawyer_id === auth()->user()->lawyer->id,
            403
        );
    }
}
