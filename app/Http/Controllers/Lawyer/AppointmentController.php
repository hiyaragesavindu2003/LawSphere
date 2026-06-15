<?php

namespace App\Http\Controllers\Lawyer;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $lawyer = auth()->user()->lawyer;

        $query = $lawyer->appointments()->with('client.user')->latest('appointment_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $appointments = $query->paginate(10)->withQueryString();

        $stats = [
            'pending' => $lawyer->appointments()->pending()->count(),
            'approved' => $lawyer->appointments()->where('status', AppointmentStatus::Approved)->count(),
            'completed' => $lawyer->appointments()->where('status', AppointmentStatus::Completed)->count(),
        ];

        return view('lawyer.appointments.index', compact('appointments', 'stats'));
    }

    public function show(Appointment $appointment): View
    {
        $this->authorizeLawyer($appointment);

        $appointment->load(['lawyer.user', 'client.user']);

        return view('lawyer.appointments.show', compact('appointment'));
    }

    public function approve(Appointment $appointment): RedirectResponse
    {
        $this->authorizeLawyer($appointment);
        abort_unless($appointment->status === AppointmentStatus::Pending, 403);

        $appointment->update(['status' => AppointmentStatus::Approved]);

        return back()->with('status', 'Appointment approved.');
    }

    public function reject(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorizeLawyer($appointment);
        abort_unless($appointment->status === AppointmentStatus::Pending, 403);

        $validated = $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:500'],
        ]);

        $appointment->update([
            'status' => AppointmentStatus::Cancelled,
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);

        return redirect()
            ->route('lawyer.appointments.index')
            ->with('status', 'Appointment rejected.');
    }

    public function reschedule(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorizeLawyer($appointment);
        abort_unless(in_array($appointment->status, [AppointmentStatus::Pending, AppointmentStatus::Approved], true), 403);

        $validated = $request->validate([
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required', 'date_format:H:i'],
            'reschedule_reason' => ['required', 'string', 'max:500'],
        ]);

        if ($this->slotTaken($appointment->lawyer_id, $validated['appointment_date'], $validated['appointment_time'], $appointment->id)) {
            return back()->withInput()->withErrors([
                'appointment_time' => 'This time slot is already booked.',
            ]);
        }

        $appointment->update([
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'reschedule_reason' => $validated['reschedule_reason'],
            'rescheduled_at' => now(),
            'status' => AppointmentStatus::Approved,
        ]);

        return back()->with('status', 'Appointment rescheduled successfully.');
    }

    public function complete(Appointment $appointment): RedirectResponse
    {
        $this->authorizeLawyer($appointment);
        abort_unless($appointment->status === AppointmentStatus::Approved, 403);

        $appointment->update(['status' => AppointmentStatus::Completed]);

        return back()->with('status', 'Appointment marked as completed.');
    }

    private function authorizeLawyer(Appointment $appointment): void
    {
        abort_unless(
            auth()->user()->isLawyer()
            && $appointment->lawyer_id === auth()->user()->lawyer->id,
            403
        );
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
