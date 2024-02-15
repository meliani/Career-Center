<?php

namespace App\Console\Commands;

use App\Models\InternshipAgreement;
use Illuminate\Console\Command;

class UpdateInternshipAgreements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'carrieres:import-internships-data';

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
        $updatedLines = 0;
        $this->info('Updating internship agreements');
        $totalLines = 0;
        //  get csv file from app\developer_docs\csv\internships.csv
        $file = 'app/developer_docs/csv/internships_import_data_15fev.csv';

        //  open the file
        $handle = fopen($file, 'r');

        //  get the first row of the file
        $header = fgetcsv($handle);

        // Update internship agreements with the data from the csv file
        while ($data = fgetcsv($handle)) {
            $totalLines++;
            //  disable model scope
            $importedData = array_combine($header, $data);
            //  find the internship by the id
            $internship = InternshipAgreement::withoutGlobalScopes()->find($importedData['id']);

            //  update the internship with the data from the csv file
            // first row contain column names
            // ignore non present columns
            if ($internship) {
                // $internship->hydrate($importedData);
                $internship->update(array_filter($importedData));
                $this->info('Updated internship agreement with id '.$importedData['id']);
                $updatedLines++;
            }
            else {
                $this->error('Internship agreement with id '.$importedData['id'].' not found');
            }

        }
        $this->info('Updated '.$updatedLines.' internship agreements out of '.$totalLines);
    }
}
