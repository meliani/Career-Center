<?php

namespace App\Console\Commands;

use App\Jobs\UpdateMidtermDueDates;
use Illuminate\Console\Command;

class UpdateMidtermDueDatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:update-midterm-dates {--now : Dispatch the job immediately}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate and update midterm due dates for all projects';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Dispatching job to update midterm due dates...');
        
        $job = new UpdateMidtermDueDates();
        
        if ($this->option('now')) {
            $job->handle();
            $this->info('Midterm due dates updated successfully.');
        } else {
            dispatch($job);
            $this->info('Job dispatched to queue.');
        }
        
        return Command::SUCCESS;
    }
}
