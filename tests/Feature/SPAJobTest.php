<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Job;
use Laravel\Sanctum\Sanctum;

class SPAJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authenticated_user_can_see_a_list_of_their_jobs()
    {
        // Set up the test
        $user = signIn();
        $jobs = Job::factory()->count(3)->create(['uploader_id' => $user->id]);

        // Run the test
        $response = $this->json('GET', 'api/jobs');

        // Check the results
        foreach ($jobs as $job) {
            $response->assertSee($job->name);
        }
    }

    /** @test */
    public function an_authenticated_user_cannot_see_anyone_elses_jobs()
    {
        // Set up the test
        $unauthenticated_user = User::factory()->create();
        $unauthenticated_jobs = Job::factory()->count(3)->create(['uploader_id' => $unauthenticated_user->id]);
        $authenticated_user = signIn();

        // Run the test
        $response = $this->json('GET', 'api/jobs');

        // Check the results
        foreach ($unauthenticated_jobs as $job) {
            $response->assertDontSee($job->name);
        }
    }

    /** @test */
    public function an_authenticated_user_can_see_the_specific_details_of_one_job()
    {
        // Set up the test
        $user = signIn();
        $job = Job::factory()->create(['uploader_id' => $user->id]);

        // Run the test
        $response = $this->json('GET', "api/jobs/{$job->id}");

        // Check the result
        $response->assertSee('created_at');
    }

    /** @test */
    public function an_authenticated_user_cannot_see_the_specific_details_of_someone_elses_job()
    {
        // Set up the test
        $user = signIn();
        $unauthenticated_user = User::factory()->create();
        $job = Job::factory()->create(['uploader_id' => $unauthenticated_user->id]);

        // Run the test
        $response = $this->json('GET', "api/jobs/{$job->id}");

        // Check the result
        $response->assertStatus(404);
        $this->assertCount(0, $response['data']);
    }

    /** @test **/
    public function an_authenticated_user_can_add_a_job_to_their_account()
    {
        // Set up the test
        $user = signIn();
        $job = Job::factory()->make(['uploader_id' => $user->id]);

        // Run the test
        $response = $this->json('POST', "api/jobs", $job->toArray());

        // Check the result
        $response->assertSee($job->name);
    }

    /** @test **/
    public function an_unauthenticated_user_cannot_add_a_job_to_the_system()
    {
        // Set up the test
        $job = Job::factory()->make();

        // Run the test
        $response = $this->json('POST', "api/jobs", $job->toArray());

        // Check the result
        $response->assertStatus(401);
        $this->assertDatabaseMissing('jobs', ['name' => $job->name]);
    }

    /** @test */
    public function an_authenticated_user_can_update_their_own_job()
    {
        // Set up the test
        $user = signIn();
        $job = Job::factory()->create(['uploader_id' => $user->id]);
        $updatedJob = Job::factory()->make(['uploader_id' => $user->id]);

        // Run the test
        $response = $this->json('PATCH', "api/jobs/{$job->id}", $updatedJob->toArray());

        // Check the result
        $response->assertSee($updatedJob->name);
    }

    /** @test */
    public function an_authenticated_user_cannot_update_anyone_elses_job()
    {
        // Set up the test
        $job = Job::factory()->create();
        $user = signIn();
        $new_job = Job::factory()->make(['uploader_id' => $user->id]);

        // Run the test
        $response = $this->json('PATCH', "api/jobs/{$job->id}", $new_job->toArray());

        // Check the result
        $response->assertStatus(404);
        $this->assertDatabaseHas('jobs', ['id' => $job->id, 'name' => $job->name]);
    }

    /** @test */
    public function an_authenticated_user_can_remove_their_own_job()
    {
        // Set up the test
        $user = signIn();
        $job = Job::factory()->create(['uploader_id' => $user->id]);

        // Run the test
        $response = $this->json('DELETE', "api/jobs/{$job->id}");

        // Check the result
        $this->assertSoftDeleted('jobs', ['id' => $job->id]);
    }

    /** @test */
    public function an_authenticated_user_cannot_remove_someone_elses_job()
    {
        // Set up the test
        $job = Job::factory()->create();
        $user = signIn();

        // Run the test
        $response = $this->json('DELETE', "api/jobs/{$job->id}");

        // Check the result
        $response->assertStatus(404);
        $this->assertDatabaseHas('jobs', ['id' => $job->id, 'deleted_at' => null]);
    }
}
