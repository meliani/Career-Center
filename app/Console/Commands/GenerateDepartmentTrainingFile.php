<?php

namespace App\Console\Commands;

use App\Models\InternshipAgreement;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateDepartmentTrainingFile extends Command
{
    protected $signature = 'train:generate-department-file 
                            {filepath : Where to store the training file}';

    protected $description = 'Generate a FastText training file from FinalYearInternshipAgreement data';

    public function handle()
    {
        $filepath = $this->argument('filepath');
        // Get only completed/validated internships from previous years
        $records = InternshipAgreement::whereNotNull('assigned_department')
            ->whereNotNull('validated_at')
            ->get();

        $this->info("Found {$records->count()} training records.");

        $departmentCounts = [];
        $lines = [];
        foreach ($records as $record) {
            $text = $record->title . ' ' . $record->description;
            // Enhance text preprocessing
            $text = strtolower($text); // Convert to lowercase
            $text = preg_replace('/[\r\n]+/', ' ', $text);
            $text = preg_replace('/[^\w\s]/', ' ', $text); // Remove special characters
            $text = preg_replace('/\s+/', ' ', $text);
            $text = trim($text);

            if (! empty($text) && $record->assigned_department) {
                $departmentLabel = $record->assigned_department->value;
                $lines[] = "__label__{$departmentLabel} {$text}";
                $departmentCounts[$departmentLabel] = ($departmentCounts[$departmentLabel] ?? 0) + 1;
            }
        }

        // Show distribution of departments in training data
        $this->info("\nDepartment distribution in training data:");
        foreach ($departmentCounts as $dept => $count) {
            $this->line("$dept: $count examples");
        }

        if (empty($lines)) {
            $this->error('No valid training data found!');

            return;
        }

        // Shuffle lines for better training
        shuffle($lines);

        $destination = 'training/' . $filepath;
        Storage::disk('training_data')->put($destination, implode(PHP_EOL, $lines));
        $this->info('Training file created with ' . count($lines) . " examples at: {$destination}");
    }
}
