<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ScheduleParameters;

class ScheduleSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ScheduleParameters::create([
            'schedule_starting_at' => '2024-07-01',
            'schedule_ending_at' => '2024-07-15',
            'day_starting_at' => '09:00:00',
            'day_ending_at' => '18:00:00',
            'lunch_starting_at' => '12:00:00',
            'lunch_ending_at' => '14:00:00',
            'number_of_rooms' => 5,
            'max_defenses_per_professor' => 10,
            'max_rooms' => 5,
            'minutes_per_slot' => 30,
        ]);
    }
}
