<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\Client;
use App\Models\Lawyer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_login_and_redirect_to_role_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Client,
            'email_verified_at' => now(),
        ]);
        Client::create(['user_id' => $user->id]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('client.dashboard'));
    }

    public function test_inactive_users_cannot_login(): void
    {
        $user = User::factory()->create([
            'is_active' => false,
            'email_verified_at' => now(),
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_client_can_register(): void
    {
        $response = $this->post('/register/client', [
            'name' => 'Test Client',
            'email' => 'client@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'client@test.com',
            'role' => UserRole::Client->value,
        ]);
        $this->assertDatabaseHas('clients', [
            'user_id' => User::where('email', 'client@test.com')->first()->id,
        ]);
    }

    public function test_lawyer_can_register_with_profile(): void
    {
        $response = $this->post('/register/lawyer', [
            'name' => 'Test Lawyer',
            'email' => 'lawyer@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'phone' => '+1234567890',
            'qualifications' => 'JD Test University',
            'specialization' => 'Criminal Law',
            'experience_years' => 5,
            'bar_number' => 'BAR-TEST-001',
        ]);

        $this->assertDatabaseHas('lawyers', [
            'specialization' => 'Criminal Law',
            'is_approved' => false,
        ]);
    }

    public function test_admin_routes_blocked_for_clients(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Client,
            'email_verified_at' => now(),
        ]);
        Client::create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_unapproved_lawyer_redirected_to_pending_page(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Lawyer,
            'email_verified_at' => now(),
        ]);
        Lawyer::create([
            'user_id' => $user->id,
            'specialization' => 'Family Law',
            'is_approved' => false,
        ]);

        $response = $this->actingAs($user)->get(route('lawyer.dashboard'));

        $response->assertRedirect(route('lawyer.pending-approval'));
    }
}
