<?php

namespace Database\Seeders;

use App\Enums\Program;
use App\Enums\StudentLevel; // Replace with your actual Student model namespace
use App\Enums\Title;
use App\Models\Student;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

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

        foreach (range(1, 10) as $index) {
            Student::create([
                'title' => Title::getArray()[array_rand(Title::getArray())],
                'pin' => $faker->randomNumber(),
                'email' => $faker->unique()->safeEmail,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email_perso' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'birth_date' => $faker->date(),
                'level' => StudentLevel::getArray()[array_rand(StudentLevel::getArray())],
                'program' => Program::getArray()[array_rand(Program::getArray())],
                'is_mobility' => $faker->boolean,
                'abroad_school' => $faker->company,
                'is_active' => $faker->boolean,
                'password' => bcrypt('password'), // You may want to change this
            ]);
        }
    }
}
