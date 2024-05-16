<?php

namespace App\Console\Commands;

use App\Imports\StudentsImport;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class ImportStudents extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-students {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import students from a csv file, Fields : title,Nom,Prénom,level,program,email';

    public function handle()
    {
        $file = $this->argument('filename');

        $this->output->title('Starting import');
        (new StudentsImport)->withOutput($this->output)->import($file);
        $this->output->success('Import successful');
    }
}
