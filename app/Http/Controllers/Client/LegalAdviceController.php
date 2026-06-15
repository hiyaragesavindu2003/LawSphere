<?php

namespace App\Http\Controllers\Client;

use App\Enums\LegalRequestStatus;
use App\Enums\PaymentType;
use App\Http\Controllers\Controller;
use App\Models\Lawyer;
use App\Models\LegalRequest;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LegalAdviceController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    public function index(Request $request): View
    {
        $client = auth()->user()->client;

        $query = $client->legalRequests()
            ->with(['lawyer.user', 'responses'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(10)->withQueryString();

        return view('client.legal-advice.index', compact('requests'));
    }

    public function create(Lawyer $lawyer): View
    {
        abort_unless($lawyer->is_approved, 404);

        $adviceFee = (float) $lawyer->legal_advice_fee;

        return view('client.legal-advice.create', compact('lawyer', 'adviceFee'));
    }

    public function store(Request $request, Lawyer $lawyer): RedirectResponse
    {
        abort_unless($lawyer->is_approved, 404);

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
        ]);

        $legalRequest = LegalRequest::create([
            'client_id' => auth()->user()->client->id,
            'lawyer_id' => $lawyer->id,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'status' => LegalRequestStatus::Pending,
        ]);

        $fee = (float) $lawyer->legal_advice_fee;

        if ($fee > 0) {
            $payment = $this->paymentService->create(
                user: auth()->user(),
                type: PaymentType::LegalAdvice,
                amount: $fee,
                description: "Legal advice: {$legalRequest->subject}",
                payable: $legalRequest,
            );

            return redirect()
                ->route('payments.checkout', $payment)
                ->with('status', 'Request created. Please complete payment to submit to the lawyer.');
        }

        return redirect()
            ->route('client.legal-advice.show', $legalRequest)
            ->with('status', 'Your legal advice request has been submitted.');
    }

    public function show(LegalRequest $legalRequest): View
    {
        $this->authorizeClient($legalRequest);

        $legalRequest->load(['lawyer.user', 'client.user', 'responses.lawyer.user', 'payment', 'review']);

        return view('client.legal-advice.show', compact('legalRequest'));
    }

    public function close(LegalRequest $legalRequest): RedirectResponse
    {
        $this->authorizeClient($legalRequest);

        abort_unless($legalRequest->status !== LegalRequestStatus::Closed, 403);

        $legalRequest->update(['status' => LegalRequestStatus::Closed]);

        return redirect()
            ->route('client.legal-advice.index')
            ->with('status', 'Request closed successfully.');
    }

    private function authorizeClient(LegalRequest $legalRequest): void
    {
        abort_unless(
            auth()->user()->isClient()
            && $legalRequest->client_id === auth()->user()->client->id,
            403
        );
    }
}
