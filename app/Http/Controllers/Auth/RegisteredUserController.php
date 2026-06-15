<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Lawyer;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function createClient(): View
    {
        return view('auth.register-client');
    }

    public function createLawyer(): View
    {
        return view('auth.register-lawyer');
    }

    public function storeClient(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => UserRole::Client,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'email_verified_at' => now(),
        ]);

        Client::create(['user_id' => $user->id]);

        event(new Registered($user));

        Auth::login($user);

        ActivityLog::log('client_registered', "Client {$user->name} registered.");

        return redirect()->route($user->dashboard_route);
    }

    public function storeLawyer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'qualifications' => ['required', 'string', 'max:1000'],
            'specialization' => ['required', 'string', 'max:255'],
            'experience_years' => ['required', 'integer', 'min:0', 'max:60'],
            'biography' => ['nullable', 'string', 'max:2000'],
            'bar_number' => ['required', 'string', 'max:100', 'unique:lawyers,bar_number'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => UserRole::Lawyer,
            'phone' => $validated['phone'],
            'address' => $validated['address'] ?? null,
            'email_verified_at' => now(),
        ]);

        Lawyer::create([
            'user_id' => $user->id,
            'qualifications' => $validated['qualifications'],
            'specialization' => $validated['specialization'],
            'experience_years' => $validated['experience_years'],
            'biography' => $validated['biography'] ?? null,
            'bar_number' => $validated['bar_number'],
            'is_approved' => false,
        ]);

        event(new Registered($user));

        Auth::login($user);

        ActivityLog::log('lawyer_registered', "Lawyer {$user->name} registered (pending approval).");

        return redirect()->route($user->dashboard_route);
    }
}
