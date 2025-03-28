<?php

namespace Database\Seeders;

use App\Enums\Department;
use App\Enums\Program;
use App\Enums\Role;
use App\Enums\Title;
use App\Models\Professor;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProfessorSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 10) as $index) {
            $firstName = $faker->firstName;
            $lastName = $faker->lastName;
            
            Professor::create([
                'name' => "$firstName $lastName",
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
                'role' => Role::getArray()[array_rand(Role::getArray())],
                'department' => Department::getArray()[array_rand(Department::getArray())],
                'assigned_program' => Program::getArray()[array_rand(Program::getArray())],
                'title' => Title::getArray()[array_rand(Title::getArray())],
                'is_enabled' => true,
                'can_supervise' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'active_status' => true,
                'avatar_url' => $faker->imageUrl(),
                'avatar' => $faker->imageUrl(),
                'dark_mode' => false,
                'messenger_color' => '#3b5998',
            ]);
        }
    }
}
