<?php

namespace Database\Seeders;

use App\Enums\Department;
use App\Enums\Program;
use App\Enums\Role;
use App\Enums\Title;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $email = env('ADMIN_EMAIL', 'admin@example.com');
        $password = env('ADMIN_PASSWORD', 'password');

        User::updateOrCreate(
            ['email' => $email],
            [
                'title' => Title::cases()[array_rand(Title::cases())]->value,
                'name' => 'Admin',
                'first_name' => 'Admin',
                'last_name' => 'Admin',
                'department' => Department::cases()[array_rand(Department::cases())]->value,
                'role' => Role::SuperAdministrator->value,
                'assigned_program' => Program::cases()[array_rand(Program::cases())]->value,
                'is_enabled' => true,
                'email_verified_at' => now(),
                'password' => bcrypt($password),
                'remember_token' => Str::random(10),
                'active_status' => true,
                'avatar_url' => 'https://ui-avatars.com/api/?name=Admin',
                'avatar' => 'https://ui-avatars.com/api/?name=Admin',
                'dark_mode' => false,
                'messenger_color' => '#3b5998',
            ]
        );

        $faker = Faker::create();
dd(Program::cases(), Program::getArray());
        foreach (range(1, 10) as $index) {
            User::create([
                'title' => Title::cases()[array_rand(Title::cases())]->value,
                'name' => $faker->name,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'department' => Department::cases()[array_rand(Department::cases())],
                'role' => Role::cases()[array_rand(Role::cases())],
                'email' => $faker->unique()->safeEmail,
                'assigned_program' => Program::cases()[array_rand(Program::cases())],
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
