<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\User;
use App\Models\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class JobFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Job::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::factory()->create()->id;
        return [
            'file_url' => $this->faker->imageUrl(400,400),
            'name' => $this->faker->word,
            'time_in_seconds' => $this->faker->numberBetween(300, 999999),
            'uploader_id' => $user,
            'collection_id' => function() use ($user){
                return Collection::factory()->create(['creator_id' => $user]);
            },
            'start_at' => $this->faker->dateTimeBetween(Carbon::now(), Carbon::now()->addWeek(1))
        ];
    }
}
