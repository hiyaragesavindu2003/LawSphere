<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Lawyer;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_lawyer_and_client_relationships(): void
    {
        $lawyerUser = User::factory()->lawyer()->create();
        $lawyer = Lawyer::create([
            'user_id' => $lawyerUser->id,
            'specialization' => 'Tax Law',
        ]);

        $clientUser = User::factory()->client()->create();
        $client = Client::create(['user_id' => $clientUser->id]);

        $this->assertTrue($lawyerUser->isLawyer());
        $this->assertTrue($clientUser->isClient());
        $this->assertEquals('Tax Law', $lawyerUser->lawyer->specialization);
        $this->assertNotNull($clientUser->client);
    }

    public function test_review_recalculates_lawyer_rating(): void
    {
        $lawyerUser = User::factory()->lawyer()->create();
        $lawyer = Lawyer::create([
            'user_id' => $lawyerUser->id,
            'specialization' => 'Immigration Law',
        ]);

        $clientUser = User::factory()->client()->create();
        $client = Client::create(['user_id' => $clientUser->id]);

        Review::create([
            'client_id' => $client->id,
            'lawyer_id' => $lawyer->id,
            'rating' => 4,
            'review_text' => 'Great service',
        ]);

        $lawyer->refresh();
        $this->assertEquals(4.00, (float) $lawyer->average_rating);
        $this->assertEquals(1, $lawyer->total_reviews);
    }
}
