<?php

namespace App\Console\Commands;

use App\Models\FinalYearInternshipAgreement;
use App\Models\InternshipAgreement;
use App\Services\FastTextService;
use Illuminate\Console\Command;

class PredictFinalYearInternshipDepartment extends Command
{
    protected $signature = 'predict:final-year-department';

    protected $description = 'Predicts and assigns department for FinalYearInternshipAgreement using last year\'s InternshipAgreement data';

    protected $minConfidence = 0.25; // Lowered from 0.4

    protected $maxProbabilityDiff = 0.1; // Maximum difference between top predictions to consider multiple departments

    public function handle(FastTextService $fastTextService)
    {
        // Add debug information
        $this->info('Loading model and starting predictions...');

        // Get training data stats first
        $trainingStats = InternshipAgreement::whereNotNull('assigned_department')
            ->whereNotNull('validated_at')
            ->selectRaw('assigned_department, count(*) as count')
            ->groupBy('assigned_department')
            ->get();

        $this->info("\nTraining data distribution:");
        foreach ($trainingStats as $stat) {
            $this->line("Department {$stat->assigned_department->value}: {$stat->count} examples");
        }

        $agreements = FinalYearInternshipAgreement::whereNull('assigned_department')->get();
        $this->info("Found {$agreements->count()} agreements needing department assignment.");

        $assigned = 0;
        $skipped = 0;

        foreach ($agreements as $final) {
            try {
                $text = $final->title . ' ' . $final->description;
                $text = preg_replace('/[\r\n]+/', ' ', $text);
                $text = preg_replace('/\s+/', ' ', $text);
                $text = trim($text);

                if (! empty($text)) {
                    $predictions = $fastTextService->predictDepartment($text, 3);

                    // Sort predictions by probability
                    arsort($predictions);
                    $topPrediction = array_key_first($predictions);
                    $topProbability = $predictions[$topPrediction];

                    if ($topProbability < $this->minConfidence) {
                        $this->warn("Low confidence ({$topProbability}) for agreement ID {$final->id}");
                        $skipped++;

                        continue;
                    }

                    // Check if multiple departments have similar probabilities
                    $possibleDepartments = [];
                    foreach ($predictions as $dept => $prob) {
                        if (($topProbability - $prob) <= $this->maxProbabilityDiff) {
                            $possibleDepartments[$dept] = $prob;
                        }
                    }

                    if (count($possibleDepartments) > 1) {
                        $deptList = implode(', ', array_map(
                            fn ($dept, $prob) => "$dept ($prob)",
                            array_keys($possibleDepartments),
                            $possibleDepartments
                        ));
                        $this->warn("Multiple possible departments for ID {$final->id}: {$deptList}");
                        $skipped++;

                        continue;
                    }

                    // Only assign if we have a clear winner with good confidence
                    if (\App\Enums\Department::tryFrom($topPrediction)) {
                        $final->assigned_department = $topPrediction;
                        $final->save();
                        $assigned++;
                        $this->info("Assigned department '{$topPrediction}' (confidence: {$topProbability}) to ID {$final->id}");
                    }
                }
            } catch (\Exception $e) {
                $this->error("Error processing agreement {$final->id}: " . $e->getMessage());
            }
        }

        $this->info("Completed: Assigned {$assigned} departments, skipped {$skipped} unclear cases.");
    }
}
