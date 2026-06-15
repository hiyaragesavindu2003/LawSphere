<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLawyerApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === UserRole::Lawyer) {
            $lawyer = $user->lawyer;

            if (! $lawyer || ! $lawyer->is_approved) {
                return redirect()->route('lawyer.pending-approval')
                    ->with('warning', 'Your lawyer account is pending admin approval.');
            }
        }

        return $next($request);
    }
}
