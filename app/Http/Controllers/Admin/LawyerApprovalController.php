<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Lawyer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LawyerApprovalController extends Controller
{
    public function index(Request $request): View
    {
        $query = Lawyer::with('user')->latest();

        if ($request->get('filter') === 'approved') {
            $query->where('is_approved', true);
        } elseif ($request->get('filter') === 'pending') {
            $query->where('is_approved', false);
        }

        $lawyers = $query->paginate(10)->withQueryString();

        $stats = [
            'pending' => Lawyer::where('is_approved', false)->count(),
            'approved' => Lawyer::where('is_approved', true)->count(),
        ];

        return view('admin.lawyers.index', compact('lawyers', 'stats'));
    }

    public function show(Lawyer $lawyer): View
    {
        $lawyer->load('user');

        return view('admin.lawyers.show', compact('lawyer'));
    }

    public function approve(Lawyer $lawyer): RedirectResponse
    {
        abort_if($lawyer->is_approved, 403, 'This lawyer is already approved.');

        $lawyer->update([
            'is_approved' => true,
            'approved_at' => now(),
        ]);

        ActivityLog::log(
            'lawyer.approved',
            "Approved lawyer: {$lawyer->user->name} ({$lawyer->specialization})",
            $lawyer
        );

        return redirect()
            ->route('admin.lawyers.index', ['filter' => 'pending'])
            ->with('status', "{$lawyer->user->name} has been approved.");
    }

    public function reject(Request $request, Lawyer $lawyer): RedirectResponse
    {
        abort_if($lawyer->is_approved, 403, 'Cannot reject an already approved lawyer.');

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $name = $lawyer->user->name;

        ActivityLog::log(
            'lawyer.rejected',
            "Rejected lawyer: {$name}. Reason: {$validated['rejection_reason']}",
            $lawyer
        );

        $lawyer->user->update(['is_active' => false]);
        $lawyer->delete();

        return redirect()
            ->route('admin.lawyers.index', ['filter' => 'pending'])
            ->with('status', "{$name}'s application has been rejected.");
    }
}
