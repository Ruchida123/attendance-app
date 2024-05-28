<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Rest;

class RestFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = Rest::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'start_rest_time' => $this->faker->datetime($max = 'now', $timezone = date_default_timezone_get()),
            'end_rest_time' => $this->faker->datetime(),
            'total_rest_time' => $this->faker->datetime(),
        ];
    }
}
