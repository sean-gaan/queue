<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Collection;
use Laravel\Sanctum\Sanctum;

class SPACollectionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authenticated_user_can_see_a_list_of_their_collections()
    {
        // Set up the test
        $user = signIn();
        $collections = Collection::factory()->count(3)->create(['creator_id' => $user->id]);

        // Run the test
        $response = $this->json('GET', 'api/collections');

        // Check the results
        foreach ($collections as $collection) {
            $response->assertSee($collection->name);
        }
    }

    /** @test */
    public function an_authenticated_user_cannot_see_anyone_elses_collections()
    {
        // Set up the test
        $unauthenticated_user = User::factory()->create();
        $unauthenticated_collections = Collection::factory()->count(3)->create(['creator_id' => $unauthenticated_user->id]);
        $authenticated_user = signIn();

        // Run the test
        $response = $this->json('GET', 'api/collections');

        // Check the results
        foreach ($unauthenticated_collections as $collection) {
            $response->assertDontSee($collection->name);
        }
    }

    /** @test */
    public function an_authenticated_user_can_see_the_specific_details_of_one_collection()
    {
        // Set up the test
        $user = signIn();
        $collection = Collection::factory()->create(['creator_id' => $user->id]);

        // Run the test
        $response = $this->json('GET', "api/collections/{$collection->id}");

        // Check the result
        $response->assertSee('created_at');
    }

    /** @test */
    public function an_authenticated_user_cannot_see_the_specific_details_of_someone_elses_collection()
    {
        // Set up the test
        $user = signIn();
        $unauthenticated_user = User::factory()->create();
        $collection = Collection::factory()->create(['creator_id' => $unauthenticated_user->id]);

        // Run the test
        $response = $this->json('GET', "api/collections/{$collection->id}");

        // Check the result
        $response->assertStatus(404);
        $this->assertCount(0, $response['data']);
    }

    /** @test **/
    public function an_authenticated_user_can_add_a_collection_to_their_account()
    {
        // Set up the test
        $user = signIn();
        $collection = Collection::factory()->make(['creator_id' => $user->id]);

        // Run the test
        $response = $this->json('POST', "api/collections", $collection->toArray());

        // Check the result
        $response->assertSee($collection->name);
    }

    /** @test **/
    public function an_unauthenticated_user_cannot_add_a_collection_to_the_system()
    {
        // Set up the test
        $collection = Collection::factory()->make();

        // Run the test
        $response = $this->json('POST', "api/collections", $collection->toArray());

        // Check the result
        $response->assertStatus(401);
        $this->assertDatabaseMissing('collections', ['name' => $collection->name]);
    }

    /** @test */
    public function an_authenticated_user_can_update_their_own_collection()
    {
        // Set up the test
        $user = signIn();
        $collection = Collection::factory()->create(['creator_id' => $user->id]);
        $updatedCollection = Collection::factory()->make(['creator_id' => $user->id]);

        // Run the test
        $response = $this->json('PATCH', "api/collections/{$collection->id}", $updatedCollection->toArray());

        // Check the result
        $response->assertSee($updatedCollection->name);
    }

    /** @test */
    public function an_authenticated_user_cannot_update_anyone_elses_collection()
    {
        // Set up the test
        $collection = Collection::factory()->create();
        $user = signIn();
        $new_collection = Collection::factory()->make(['creator_id' => $user->id]);

        // Run the test
        $response = $this->json('PATCH', "api/collections/{$collection->id}", $new_collection->toArray());

        // Check the result
        $response->assertStatus(404);
        $this->assertDatabaseHas('collections', ['id' => $collection->id, 'name' => $collection->name]);
    }

    /** @test */
    public function an_authenticated_user_can_remove_their_own_collection()
    {
        // Set up the test
        $user = signIn();
        $collection = Collection::factory()->create(['creator_id' => $user->id]);

        // Run the test
        $response = $this->json('DELETE', "api/collections/{$collection->id}");

        // Check the result
        $this->assertSoftDeleted('collections', ['id' => $collection->id]);
    }

    /** @test */
    public function an_authenticated_user_cannot_remove_someone_elses_collection()
    {
        // Set up the test
        $collection = Collection::factory()->create();
        $user = signIn();

        // Run the test
        $response = $this->json('DELETE', "api/collections/{$collection->id}");

        // Check the result
        $response->assertStatus(404);
        $this->assertDatabaseHas('collections', ['id' => $collection->id, 'deleted_at' => null]);
    }
}
