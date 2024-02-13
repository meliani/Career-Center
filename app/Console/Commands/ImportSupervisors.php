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
    protected $signature = 'carrieres:import-supervisors';

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
        $file = 'app/developer_docs/csv/EMO_Supervisors.csv';

        //  open the file
        $handle = fopen($file, 'r');

        //  get the first row of the file
        $header = fgetcsv($handle);

        // Update internship agreements with the data from the csv file
        while ($data = fgetcsv($handle)) {
            //  disable model scope
            $importedData = array_combine($header, $data);
            //  find the internship by the id
            $internshipAgreement = InternshipAgreement::withoutGlobalScopes()->where('id_pfe', '=', $importedData['id_pfe'])->first();
            $this->ImportProfessorFromInternshipAgreement($internshipAgreement, $importedData['professor_name']);
        }
    }

    public function ImportProfessorFromInternshipAgreement(InternshipAgreement $internshipAgreement, $professor_name)
    {
        $professor = Professor::where('name', 'like', '%'.$professor_name.'%')->first();
        if ($professor != null) {
            $project = Project::withoutGlobalScopes()->where('id', $internshipAgreement->project_id)->first();
            // dd($project->professors);
            if ($project->professors->contains($professor)) {
                $professor->projects()->detach($internshipAgreement->project_id);
                $professor->projects()->attach([$internshipAgreement->project_id => ['jury_role' => 'Supervisor']]);
            } else {
                $professor->projects()->attach([$internshipAgreement->project_id => ['jury_role' => 'Supervisor']]);
            }
        }
    }
}
