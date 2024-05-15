<?php

namespace Database\Seeders;

use App\Models\Year;
use Faker\Factory as Faker; // Replace with your actual Year model namespace
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

        foreach (range(1, 10) as $index) {
            Year::create([
                'title' => $faker->year,
                'date' => $faker->dateTimeThisYear,
            ]);
        }
    }
}
