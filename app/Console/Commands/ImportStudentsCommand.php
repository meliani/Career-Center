<?php

namespace App\Console\Commands;

use App\Imports\StudentsImport;
use App\Models\Year;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportStudentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:import
                            {file : Path to the CSV file}
                            {--merge=update : Merge mode (update, skip, or replace)}
                            {--year= : Academic year to associate with imported students}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import students from a CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');
        $mergeMode = $this->option('merge');
        $academicYear = $this->option('year');
        
        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }
        
        // If academic year not provided, use current
        if (empty($academicYear)) {
            $academicYear = Year::current()->title;
        }
        
        $this->info("Starting import from {$filePath}...");
        $this->info("Merge mode: {$mergeMode}");
        $this->info("Academic year: {$academicYear}");
        
        try {
            // Create an import instance
            $import = new StudentsImport($mergeMode, $academicYear);
            
            // Create a progress bar
            $this->output->progressStart();
            
            // Import the CSV
            $import->import($filePath);
            
            // Finish the progress bar
            $this->output->progressFinish();
            
            // Get the results
            $results = $import->getImportResults();
            
            // Show the results
            $this->info("\nImport completed:");
            $this->table(
                ['Total', 'Created', 'Updated', 'Skipped', 'Failed'],
                [[
                    $results['total'],
                    $results['created'],
                    $results['updated'],
                    $results['skipped'],
                    $results['failed'],
                ]]
            );
            
            // Show errors if any
            if (!empty($results['errors'])) {
                $this->warn("\nErrors encountered:");
                foreach ($results['errors'] as $error) {
                    $this->error("  - {$error}");
                }
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Import failed: {$e->getMessage()}");
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
