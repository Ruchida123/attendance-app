<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        User::factory(50)->create()->each(function($user){
            Attendance::factory(1)->create([
                'user_id' => $user->id,
            ])->each(function($attendance){
                Rest::factory(1)->create([
                    'user_id' => $attendance->user_id,
                    'attendance_id' => $attendance->id,
                    'date' => $attendance->date
                ]);
            });
        });
    }
}
