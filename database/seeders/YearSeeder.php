<?php

namespace Database\Seeders;

use App\Models\Year;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class YearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $years = [];
        
        // Create 10 years
        foreach (range(1, 10) as $index) {
            $years[] = Year::create([
                'title' => $faker->year,
                'is_current' => false,
            ]);
        }
        
        // Set the most recent year as current
        $currentYear = $years[count($years) - 1];
        $currentYear->is_current = true;
        $currentYear->save();
    }
}
