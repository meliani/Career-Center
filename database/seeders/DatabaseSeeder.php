<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;


use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Eloquent::unguard();

        // $this->call('UserTableSeeder');
        // $this->command->info('User table seeded!');

        $path = 'app/developer_docs/sql/users.sql';
        DB::unprepared(file_get_contents($path));
        $this->command->info('Users table seeded!');
        DB::unprepared(file_get_contents('app/developer_docs/sql/years.sql'));
        $this->command->info('Years table seeded!');
        DB::unprepared(file_get_contents('app/developer_docs/sql/internships.sql'));
        $this->command->info('Internships table seeded!');
        // DB::unprepared(file_get_contents('app/developer_docs/sql/internship_offers.sql'));
        // $this->command->info('Internship offers table seeded!');
        DB::unprepared(file_get_contents('app/developer_docs/sql/students.sql'));
        $this->command->info('Students table seeded!');



        // User::factory()->root()->create();
        // User::factory()->issati()->create();
        // User::factory()->ennouaary()->create();
        // User::factory()->samira()->create();
        // User::factory()->nisrine()->create();
        // User::factory()->kensi()->create();
        $this->call(ScheduleSettingsSeeder::class);
        // User::factory(10)->create();
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
