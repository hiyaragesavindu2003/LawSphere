<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if (! $request->user()->is_active) {
            auth()->logout();

            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been deactivated.']);
        }

        $userRole = $request->user()->role->value;

        if (! in_array($userRole, $roles, true)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
