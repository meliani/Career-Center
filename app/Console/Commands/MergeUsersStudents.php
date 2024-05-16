<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\DB;

class MergeUsersStudents extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'carrieres:merge-users-students';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Retrieve users from the users table
        $users = DB::connection('frontend_database')->table('users')->get();

        foreach ($users as $user) {
            // Assuming 'id' is the common field between users and students
            $student = DB::table('students')->where('id', $user->id)->first();

            if ($student) {
                // Copy email and password fields
                DB::table('students')
                    ->where('id', $user->id)
                    ->update([
                        'email' => $user->email,
                        'password' => $user->password,
                    ]);
            }
        }

        $this->info('User credentials copied successfully!');
    }
}
