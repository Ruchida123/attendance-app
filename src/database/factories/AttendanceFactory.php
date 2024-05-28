<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Attendance;

class AttendanceFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = Attendance::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'date' => $this->faker->unique()->dateTimeBetween($startDate = '-4 days', $endDate = 'now'),
            'start_work_time' => $this->faker->datetime($max = 'now', $timezone = date_default_timezone_get()),
            'end_work_time' => $this->faker->datetime($min = 'now', $timezone = date_default_timezone_get()),
            'total_work_time' => $this->faker->datetime(),
            'state' => '勤務終了',
        ];
    }
}
