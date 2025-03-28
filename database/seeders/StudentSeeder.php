<?php

namespace Database\Seeders;

use App\Enums\Program;
use App\Enums\StudentLevel;
use App\Enums\Title;
use App\Models\Student;
use App\Models\Year;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $year = Year::where('is_current', true)->first();
        
        if (!$year) {
            // If no current year exists, get the first year or create one
            $year = Year::first() ?? Year::create([
                'title' => date('Y') . '-' . (date('Y') + 1),
                'is_current' => true
            ]);
        }

        foreach (range(1, 10) as $index) {
            $firstName = $faker->firstName;
            $lastName = $faker->lastName;
            $email = strtolower(Str::slug($firstName . '.' . $lastName) . '@ine.inpt.ac.ma');
            
            Student::create([
                'title' => Title::getArray()[array_rand(Title::getArray())],
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email_perso' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'birth_date' => $faker->dateTimeBetween('-25 years', '-18 years')->format('Y-m-d'),
                'level' => StudentLevel::getArray()[array_rand(StudentLevel::getArray())],
                'program' => Program::getArray()[array_rand(Program::getArray())],
                'is_mobility' => $faker->boolean(20), // 20% chance of being a mobility student
                'abroad_school' => $faker->company,
                'is_active' => true,
                'year_id' => $year->id,
                'password' => bcrypt('password'), // Default password for all seeded students
                'is_verified' => true,
                'email_verified_at' => now(),
            ]);
        }
    }
}
