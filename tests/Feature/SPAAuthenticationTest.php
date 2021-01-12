<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class SPAAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_guest_can_create_a_user()
    {
        // Set up the test
        $user = User::factory()->make();
        $password = 'password';
        $data = array_merge($user->toArray(), ['password' => $password, 'password_confirmation' => $password]);

        // Run the test
        $xsrf = $this->getXSRF();
        $response = $this->withHeaders([
            'X-XSRF-TOKEN' => $xsrf,
        ])->json('POST', '/api/register', $data);

        // Check the results
        $this->assertDatabaseHas('users', ['email' => $user->email]);
    }

    /** @test */
    public function a_guest_can_not_register_with_the_same_email()
    {
        // Set up the test
        $user = User::factory()->make();
        $password = 'password';
        $data = array_merge($user->toArray(), ['password' => $password, 'password_confirmation' => $password]);

        // Run the test
        $xsrf = $this->getXSRF();
        $this->withHeaders([
            'X-XSRF-TOKEN' => $xsrf,
        ])->json('POST', '/api/register', $data);
        $xsrf = $this->getXSRF();
        $response = $this->withHeaders([
            'X-XSRF-TOKEN' => $xsrf,
        ])->json('POST', '/api/register', $data);

        // Check the results
        $this->assertDatabaseHas('users', ['email' => $user->email]);
        $response->assertStatus(422);
        $this->assertCount(1, User::all());
    }

    /** @test */
    public function a_guest_can_log_in()
    {
        // Set up required variables for the test
        $user = User::factory()->make()->toArray();
        $password = 'password';

        User::create(array_merge(
            $user,
            ['password' => bcrypt($password)]
        ));

        // Run the test
        $xsrf = $this->getXSRF();
        $response = $this->withHeaders([
            'X-XSRF-TOKEN' => $xsrf,
        ])->json('POST', '/api/login', array_merge(
            $user,
            ['password' => $password]
        ));

        // Check the results
        $response->assertStatus(200)
            ->assertJson([
                'success' => 1,
            ]);
    }

    /** @test */
    public function an_authenticated_user_can_logout()
    {
        // Setup the test
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        // Run the test
        $xsrf = $this->getXSRF();
        $response = $this->withHeaders([
            'X-XSRF-TOKEN' => $xsrf,
        ])->json('POST', '/api/logout');

        // Check the results
        $response->assertStatus(200)
            ->assertJson([
                'success' => 1,
            ]);
    }
}
