<?php

namespace App\Console\Commands;

use App\Models\InternshipAgreement;
use App\Models\Professor;
use App\Models\Project;
use Illuminate\Console\Command;

class ImportSupervisors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'carrieres:import-supervisors {filename}';

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
        //  get csv file from app\developer_docs\csv\internships.csv
        $file = 'app/developer_docs/csv/' . $this->argument('filename');

        //  open the file
        $handle = fopen($file, 'r');

        //  get the first row of the file
        $header = fgetcsv($handle);
        $importedLines = 0;
        // Update internship agreements with the data from the csv file
        while ($data = fgetcsv($handle)) {
            //  disable model scope
            $importedData = array_combine($header, $data);
            //  find the internship by the id
            // $internshipAgreement = InternshipAgreement::withoutGlobalScopes()->where('id_pfe', '=', $importedData['id_pfe'])->first();
            $project = Project::withoutGlobalScopes()->where('id_pfe', '=', $importedData['id_pfe'])->first();
            if ($project == null) {
                $project = Project::withoutGlobalScopes()->where('id_pfe', 'like', "%{$importedData['id_pfe']}%")->first();
                if ($project == null) {
                    $this->error('Project with id_pfe ' . $importedData['id_pfe'] . ' not found');
                } else {
                    $this->error('Project with id_pfe ' . $importedData['id_pfe'] . ' not found, but maybe here is a binome ' . $project->id_pfe);
                }

                continue;
            }
            $this->ImportProfessorFromInternshipAgreement($project, $importedData['professor_name']);
            $importedLines++;
        }
        $this->info('Imported ' . $importedLines . ' supervisors');
    }

    public function ImportProfessorFromInternshipAgreement(Project $project, $professor_name)
    {
        $professor = Professor::where('name', 'like', '%' . $professor_name . '%')->first();
        if ($professor == null) {
            $professor = Professor::whereFuzzy('first_name', $professor_name)
                ->orWhereFuzzy('last_name', $professor_name)
                ->orWhereFuzzy('name', $professor_name)
                ->withMinimumRelevance(40)
                ->first();

            if ($professor != null) {
                $this->info('Professor ' . $professor_name . ' is similar to ' . $professor->name);
            } else {
                $this->error('Professor ' . $professor_name . ' not found');

                return;
            }

        } elseif ($professor != null) {
            $this->info('Professor ' . $professor_name . ' found as ' . $professor->name);
            $project = Project::withoutGlobalScopes()->where('id', $project->id)->first();
            if ($project->professors->contains($professor)) {
                $professor->projects()->detach($project->id);
                $professor->projects()->attach([$project->id => ['jury_role' => 'Supervisor']]);
            } else {
                $professor->projects()->attach([$project->id => ['jury_role' => 'Supervisor']]);
            }
        }
    }
}
