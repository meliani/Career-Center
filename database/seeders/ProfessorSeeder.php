<?php

namespace Database\Seeders;

use App\Enums\Department;
use App\Enums\Program;
use App\Enums\Role;
use App\Enums\Title;
use App\Models\Professor;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ProfessorSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 10) as $index) {
            Professor::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'), // You may want to change this
                'role' => Role::getArray()[array_rand(Role::getArray())],
                'department' => Department::getArray()[array_rand(Department::getArray())],
                'assigned_program' => Program::getArray()[array_rand(Program::getArray())],
                'title' => Title::getArray()[array_rand(Title::getArray())],
            ]);
        }
    }
}
