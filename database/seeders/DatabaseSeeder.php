<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Eloquent::unguard();

        $this->call(UserSeeder::class);
        $this->call(ProfessorSeeder::class);
        $this->call(YearSeeder::class);
        $this->call(StudentSeeder::class);

        $this->call(ScheduleSettingsSeeder::class);
        $this->call(RoomsSeeder::class);

        $this->call(DocumentTemplateSeeder::class);

        /* Uncomment if you want to seed sample data from SQL files */

        // DB::unprepared(file_get_contents('developer_docs/sql/careers_backend_data.sql'));
        // $this->command->info('Backend data seeded!');

        /* $path = 'app/developer_docs/sql/users.sql';
        DB::unprepared(file_get_contents($path));
        $this->command->info('Users table seeded!');
        DB::unprepared(file_get_contents('app/developer_docs/sql/years.sql'));
        $this->command->info('Years table seeded!');
        DB::unprepared(file_get_contents('app/developer_docs/sql/internships.sql'));
        $this->command->info('Internships table seeded!');
        DB::unprepared(file_get_contents('app/developer_docs/sql/internship_offers.sql'));
        $this->command->info('Internship offers table seeded!');
        DB::unprepared(file_get_contents('app/developer_docs/sql/students.sql'));
        $this->command->info('Students table seeded!'); */

    }
}
