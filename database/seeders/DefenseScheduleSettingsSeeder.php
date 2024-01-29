<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ScheduleParameters;

class DefenseScheduleSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ScheduleParameters::create([
            'starting_from' => '2022-01-01',
            'ending_at' => '2022-12-31',
            'working_from' => '09:00:00',
            'working_to' => '17:00:00',
            'number_of_rooms' => 5,
            'max_defenses_per_professor' => 10,
            'max_rooms' => 5,
            'minutes_per_slot' => 30,
        ]);
    }
}
