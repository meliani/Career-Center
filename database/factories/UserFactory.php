<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'first_name' => fake()->name(),
            'last_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'Professor',
        ];
    }
    /**
     * Indicate that the model's state should be root.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function root(): Factory
    {
        return $this->state([
            'name' => 'Meliani',
            'first_name' => 'Meliani',
            'last_name' => 'Mohamed',
            'email' => 'elmeliani@inpt.ac.ma',
            'role' => 'SuperAdministrator',
            'password' => bcrypt('root_password'), // replace 'root_password' with the actual password
            'remember_token' => Str::random(10),
        ]);
    }
    public function kensi(): Factory
    {
        return $this->state([
            'name' => 'Kensi Ahmed',
            'first_name' => 'Kensi',
            'last_name' => 'Ahmed',
            'email' => 'a.kensi@inpt.ac.ma',
            'role' => 'Administrator',
            'password' => bcrypt('root_password'), // replace 'root_password' with the actual password
            'remember_token' => Str::random(10),
        ]);
    }
    public function samira(): Factory
    {
        return $this->state([
            'name' => 'Bellahcen Samira',
            'first_name' => 'Bellahcen',
            'last_name' => 'Samira',
            'email' => 'b.samira@inpt.ac.ma',
            'role' => 'Administrator',
            'password' => bcrypt('root_password'), // replace 'root_password' with the actual password
            'remember_token' => Str::random(10),
        ]);
    }
    public function nisrine(): Factory
    {
        return $this->state([
            'name' => 'Azzi Nisrine',
            'first_name' => 'Azzi',
            'last_name' => 'Nisrine',
            'email' => 'n.azzi@inpt.ac.ma',
            'role' => 'Administrator',
            'password' => bcrypt('root_password'), // replace 'root_password' with the actual password
            'remember_token' => Str::random(10),
        ]);
    }
    /* 
    * wil add some professors with ProgramCoordinator roles
    * we'll test with Oussama EL ISSATI / SESNUM
    * and with Abdeslam EN-NOUAARY / SUD
    */
    public function issati(): Factory
    {
        return $this->state([
            'name' => 'EL ISSATI Oussama',
            'first_name' => 'Oussama',
            'last_name' => 'EL ISSATI',
            'email' => 'issati@inpt.ac.ma',
            'role' => 'ProgramCoordinator',
            'department' => 'SC',
            'program_coordinator' => 'SESNUM',
            'password' => bcrypt('professor_password'), // replace 'professor_password' with the actual password
            'remember_token' => Str::random(10),
        ]);
    }
    public function ennouaary(): Factory
    {
        return $this->state([
            'name' => 'EN-NOUAARY Abdeslam',
            'first_name' => 'Abdeslam',
            'last_name' => 'EN-NOUAARY',
            'email' => 'ennouaary@inpt.ac.ma',
            'role' => 'ProgramCoordinator',
            'department' => 'RIM',
            'program_coordinator' => 'SUD',
            'password' => bcrypt('professor_password'), // replace 'professor_password' with the actual password
            'remember_token' => Str::random(10),
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
