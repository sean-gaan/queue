<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class SPAUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authenticated_user_can_get_their_details()
    {
        // Create the test
        $user = signIn();

        // Run the test
        $response = $this->json('GET', '/api/user');

        // Check the results
        $response->assertStatus(200)
            ->assertSee($user->name);
    }

    /** @test */
    public function an_authenticated_user_can_update_their_details()
    {
        // Create the test
        $user = signIn();

        // Run the test
        $response = $this->json('PATCH', '/api/user', ['name' => 'my fantastic name']);

        // Check the results
        $response->assertStatus(200)
            ->assertSee('my fantastic name');
    }

    /** @test */
    public function an_unauthenticated_user_cannot_access_the_user_endpoint()
    {
        // Create the test

        // Run the test
        $response = $this->json('GET', '/api/user');

        // Check the results
        $response->assertStatus(401);
    }
}
