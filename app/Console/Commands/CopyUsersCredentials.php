// app/Console/Commands/CopyUserCredentials.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CopyUserCredentials extends Command
{
    protected $signature = 'copy:credentials';
    protected $description = 'Copy email and password from users to students';

    public function handle()
    {
        // Retrieve users from the users table
        $users = DB::connection('your_connection_name')->table('users')->get();

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
