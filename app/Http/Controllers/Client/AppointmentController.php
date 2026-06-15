<?php

namespace App\Http\Controllers\Client;

use App\Enums\AppointmentStatus;
use App\Enums\PaymentType;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Lawyer;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    public function index(Request $request): View
    {
        $client = auth()->user()->client;

        $query = $client->appointments()->with('lawyer.user')->latest('appointment_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $appointments = $query->paginate(10)->withQueryString();

        return view('client.appointments.index', compact('appointments'));
    }

    public function create(Lawyer $lawyer): View
    {
        abort_unless($lawyer->is_approved, 404);

        $consultationFee = (float) $lawyer->consultation_fee;

        return view('client.appointments.create', compact('lawyer', 'consultationFee'));
    }

    public function store(Request $request, Lawyer $lawyer): RedirectResponse
    {
        abort_unless($lawyer->is_approved, 404);

        $validated = $this->validateBooking($request);

        if ($this->slotTaken($lawyer->id, $validated['appointment_date'], $validated['appointment_time'])) {
            return back()->withInput()->withErrors([
                'appointment_time' => 'This time slot is already booked. Please choose another.',
            ]);
        }

        $appointment = Appointment::create([
            'client_id' => auth()->user()->client->id,
            'lawyer_id' => $lawyer->id,
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'notes' => $validated['notes'] ?? null,
            'status' => AppointmentStatus::Pending,
        ]);

        $fee = (float) $lawyer->consultation_fee;

        if ($fee > 0) {
            $payment = $this->paymentService->create(
                user: auth()->user(),
                type: PaymentType::Appointment,
                amount: $fee,
                description: "Consultation with {$lawyer->user->name} on {$appointment->formatted_date_time}",
                payable: $appointment,
            );

            return redirect()
                ->route('payments.checkout', $payment)
                ->with('status', 'Appointment created. Please complete payment to confirm your booking.');
        }

        return redirect()
            ->route('client.appointments.show', $appointment)
            ->with('status', 'Appointment request submitted. Waiting for lawyer approval.');
    }

    public function show(Appointment $appointment): View
    {
        $this->authorizeClient($appointment);

        $appointment->load(['lawyer.user', 'client.user', 'review', 'payment']);

        return view('client.appointments.show', compact('appointment'));
    }

    public function cancel(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorizeClient($appointment);

        abort_unless(in_array($appointment->status, [AppointmentStatus::Pending, AppointmentStatus::Approved], true), 403);

        $validated = $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:500'],
        ]);

        $appointment->update([
            'status' => AppointmentStatus::Cancelled,
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);

        return redirect()
            ->route('client.appointments.index')
            ->with('status', 'Appointment cancelled successfully.');
    }

    private function authorizeClient(Appointment $appointment): void
    {
        abort_unless(
            auth()->user()->isClient()
            && $appointment->client_id === auth()->user()->client->id,
            403
        );
    }

    private function validateBooking(Request $request): array
    {
        return $request->validate([
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    private function slotTaken(int $lawyerId, string $date, string $time, ?int $exceptId = null): bool
    {
        return Appointment::query()
            ->where('lawyer_id', $lawyerId)
            ->where('appointment_date', $date)
            ->where('appointment_time', $time)
            ->whereIn('status', [AppointmentStatus::Pending, AppointmentStatus::Approved])
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->exists();
    }
}
