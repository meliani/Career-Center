<?php

namespace Database\Seeders;

use App\Enums\Department;
use App\Enums\Program; // Replace with your actual User model namespace
use App\Enums\Role;
use App\Enums\Title;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
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
            User::create([
                'title' => Title::values()[array_rand(Title::values())],
                'name' => $faker->name,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'department' => Department::values()[array_rand(Department::values())],
                'role' => Role::values()[array_rand(Role::values())],
                'email' => $faker->unique()->safeEmail,
                'assigned_program' => Program::values()[array_rand(Program::values())],
                'is_enabled' => $faker->boolean,
                'email_verified_at' => now(),
                'password' => bcrypt('password'), // You may want to change this
                'remember_token' => Str::random(10),
                'active_status' => $faker->boolean,
                'avatar_url' => $faker->imageUrl(),
                'avatar' => $faker->imageUrl(),
                'dark_mode' => $faker->boolean,
                'messenger_color' => $faker->hexColor,
            ]);
        }
    }
}
