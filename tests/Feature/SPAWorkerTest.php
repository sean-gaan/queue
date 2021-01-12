<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Worker;
use Laravel\Sanctum\Sanctum;

class SPAWorkerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authenticated_user_can_see_a_list_of_their_workers(){
        // Set up the test
        $user = signIn();
        $workers = Worker::factory()->count(3)->create(['owner_id' => $user->id]);

        // Run the test
        $response = $this->json('GET', 'api/workers');

        // Check the results
        foreach($workers as $worker){
            $response->assertSee($worker->name);
        }
    }

    /** @test */
    public function an_authenticated_user_cannot_see_anyone_elses_workers(){
        // Set up the test
        $unauthenticated_user = User::factory()->create();
        $unauthenticated_workers = Worker::factory()->count(3)->create(['owner_id' => $unauthenticated_user->id]);
        $authenticated_user = signIn();

        // Run the test
        $response = $this->json('GET', 'api/workers');

        // Check the results
        foreach($unauthenticated_workers as $worker){
            $response->assertDontSee($worker->name);
        }
    }

    /** @test */
    public function an_authenticated_user_can_see_the_specific_details_of_one_worker(){
        // Set up the test
        $user = signIn();
        $worker = Worker::factory()->create(['owner_id' => $user->id]);

        // Run the test
        $response = $this->json('GET', "api/workers/{$worker->id}");

        // Check the result
        $response->assertSee('created_at');
    }

    /** @test */
    public function an_authenticated_user_cannot_see_the_specific_details_of_someone_elses_worker(){
        // Set up the test
        $user = signIn();
        $unauthenticated_user = User::factory()->create();
        $worker = Worker::factory()->create(['owner_id' => $unauthenticated_user->id]);

        // Run the test
        $response = $this->json('GET', "api/workers/{$worker->id}");

        // Check the result
        $response->assertStatus(404);
        $this->assertCount(0, $response['data']);
    }

    /** @test **/
    public function an_authenticated_user_can_add_a_worker_to_their_account()
    {
        // Set up the test
        $user = signIn();
        $worker = Worker::factory()->make(['owner_id' => $user->id]);

        // Run the test
        $response = $this->json('POST', "api/workers", $worker->toArray());

        // Check the result
        $response->assertSee($worker->name);
    }

    /** @test **/
    public function an_unauthenticated_user_cannot_add_a_worker_to_the_system()
    {
        // Set up the test
        $worker = Worker::factory()->make();

        // Run the test
        $response = $this->json('POST', "api/workers", $worker->toArray());

        // Check the result
        $response->assertStatus(401);
        $this->assertDatabaseMissing('workers', ['name' => $worker->name]);
    }

    /** @test */
    public function an_authenticated_user_can_update_their_own_worker(){
        // Set up the test
        $user = signIn();
        $worker = Worker::factory()->create(['owner_id' => $user->id]);
        $updatedWorker = Worker::factory()->make(['owner_id' => $user->id]);

        // Run the test
        $response = $this->json('PATCH', "api/workers/{$worker->id}", $updatedWorker->toArray());

        // Check the result
        $response->assertSee($updatedWorker->name);
    }

    /** @test */
    public function an_authenticated_user_cannot_update_anyone_elses_worker(){
        // Set up the test
        $worker = Worker::factory()->create();
        $user = signIn();
        $new_worker = Worker::factory()->make(['owner_id' => $user->id]);

        // Run the test
        $response = $this->json('PATCH', "api/workers/{$worker->id}", $new_worker->toArray());

        // Check the result
        $response->assertStatus(404);
        $this->assertDatabaseHas('workers', ['id' => $worker->id, 'name' => $worker->name]);
    }

    /** @test */
    public function an_authenticated_user_can_remove_their_own_worker(){
        // Set up the test
        $user = signIn();
        $worker = Worker::factory()->create(['owner_id' => $user->id]);

        // Run the test
        $response = $this->json('DELETE', "api/workers/{$worker->id}");

        // Check the result
        $this->assertSoftDeleted('workers', ['id' => $worker->id]);
    }

    /** @test */
    public function an_authenticated_user_cannot_remove_someone_elses_worker(){
        // Set up the test
        $worker = Worker::factory()->create();
        $user = signIn();

        // Run the test
        $response = $this->json('DELETE', "api/workers/{$worker->id}");

        // Check the result
        $response->assertStatus(404);
        $this->assertDatabaseHas('workers', ['id' => $worker->id, 'deleted_at' => null]);
    }
}
