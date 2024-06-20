<?php

namespace App\Console\Commands;

use App\Facades\GlobalDefenseCalendarConnector;
use App\Models\InternshipAgreement;
use App\Models\Professor;
use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class ImportReviewersGoogle extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:reviewers-google {column}';

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
        $column = $this->argument('column');

        $connector = new GlobalDefenseCalendarConnector();
        $data = $connector->getDefenses();
        $importedLines = 0;
        // Update internship agreements with the data from the csv file
        foreach ($data as $record) {
            //  disable model scope
            $importedData = $record;
            //  find the internship by the id
            // $internshipAgreement = InternshipAgreement::withoutGlobalScopes()->where('id_pfe', '=', $importedData['id_pfe'])->first();
            $InternshipAgreement = InternshipAgreement::withoutGlobalScopes()->where('id_pfe', '=', $importedData['ID PFE'])->first();
            $project = $InternshipAgreement?->project;
            if ($InternshipAgreement == null) {
                $IdsPfe = explode(',', $importedData['ID PFE']);

                foreach ($IdsPfe as $idPfe) {
                    $InternshipAgreement = InternshipAgreement::withoutGlobalScopes()->where('id_pfe', '=', $idPfe)->first();
                    if ($InternshipAgreement != null) {
                        $project = $InternshipAgreement->project;

                        break;
                    }
                }
                if ($InternshipAgreement == null) {
                    $this->error('Project with id_pfe ' . $importedData['ID PFE'] . ' not found');
                } else {
                    $this->line('Project with id_pfe ' . $importedData['ID PFE'] . ' not found, but maybe here is a binome ' . $InternshipAgreement->id_pfe);
                }

                continue;
            }
            $this->ImportProfessorFromInternshipAgreement($project, $importedData[$column]);
            $importedLines++;
        }
        $this->info('Imported ' . $importedLines . ' supervisors');
    }

    public function ImportProfessorFromInternshipAgreement(Project $project, $professor_name)
    {
        $professor = Professor::where('name', 'like', '%' . $professor_name . '%')->first();
        if ($professor == null) {
            // $professor = Professor::whereFuzzy('first_name', $professor_name)
            //     ->WhereFuzzy('last_name', $professor_name)
            //     ->WhereFuzzy('name', $professor_name)
            //     ->withMinimumRelevance(40)
            //     ->first();

            $professor = Professor::where('name', 'like', '%' . $professor_name . '%')->first();

            if ($professor != null) {
                $this->info('Professor ' . $professor_name . ' is similar to ' . $professor->name);
            } else {
                $this->error('Professor ' . $professor_name . ' not found');

                return;
            }

        } elseif ($professor != null) {
            $this->info('Professor ' . $professor_name . ' found as ' . $professor->name);
            $project = InternshipAgreement::withoutGlobalScopes()->where('project_id', $project->id)->first();
            $project = $project->project;
            if ($project->professors->contains($professor)) {
                $professor->projects()->detach($project->id);
                $professor->projects()->attach([$project->id => ['jury_role' => 'Reviewer']]);
            } else {
                $professor->projects()->attach([$project->id => ['jury_role' => 'Reviewer']]);
            }
        }
    }
}
