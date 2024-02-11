<?php

namespace App\Services;

use App\Models\DefenseSchedule;
use App\Models\InternshipAgreement;
use App\Models\Project;
use Filament\Notifications\Notification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use App\Models\Professor;
use App\Models\Jury;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Actions\Action;

class ProjectService
{
    protected $forceOverwrite = true;

    public static function setForceOverwrite($value)
    {
        self::$forceOverwrite = $value;
    }

    public static function AssignInternshipsToProjects($record)
    {
        $assignedInternships = 0;
        $forceOverwrite = true;
        try {
            if (Gate::denies('batch-assign-internships-to-projects', )) {
                throw new AuthorizationException();
            }
            $signedInternships = InternshipAgreement::where('status', '=', 'Signed')->get();

            foreach ($signedInternships as $signedInternship) {
                try {
                    $project = Project::create([
                        'id' => $signedInternship->id,
                        'id_pfe' => $signedInternship->id_pfe,
                        'title' => $signedInternship->title,
                        'description' => $signedInternship->description,
                        'organization' => $signedInternship->organization_name,
                        'start_date' => $signedInternship->starting_at,
                        'end_date' => $signedInternship->ending_at,
                    ]);
                    $signedInternship->project_id = $project->id;
                    $signedInternship->save();
                } catch (\Exception $e) {
                    if ($e->getCode() == 23000) {
                        // duplicate entry
                        if ($forceOverwrite) {
                            $project = Project::find($signedInternship->id);
                            $project->id_pfe = $signedInternship->id_pfe;
                            $project->title = $signedInternship->title;
                            $project->description = $signedInternship->description;
                            $project->organization = $signedInternship->organization;
                            $project->start_date = $signedInternship->starting_at;
                            $project->end_date = $signedInternship->ending_at;
                            $project->save();
                        } else {
                            continue;
                        }
                    } else {
                        Log::error($e->getMessage());
                        throw new \Exception('There was an error creating the project. Please try again.');
                    }
                }
                // $signedInternship->attach($project);

                // $project->students()->attach($student);
                $project = New Project();
                $project->attach($signedInternship);
                $project->save();
                $signedInternship->save();
                $assignedInternships++;
                // if ($assignedInternships == 5) {
                //     break;
                // }
            }
        } catch (AuthorizationException $e) {
            Notification::make()
                ->title('Sorry You must be an Administrator.')
                ->danger()
                ->send();
            return response()->json(['error' => 'This action is unauthorized.'], 403);
        }
        Notification::make()
            ->title($assignedInternships . ' internships assigned to projects')
            ->success()
            ->send();
    }
    public static function GenerateProjectsJury($record)
    {
        $supervisors = collect();
        $projects = Project::all();
        $projects->each(function ($project) use ($record, $supervisors) {
            if ($project->internship->int_adviser_name != null && $project->internship->int_adviser_name != 'NA') {
                $supervisorName = $project->internship->int_adviser_name;
                $supervisors = $supervisors->push($supervisorName);
                $professor = Professor::where('name', 'like', '%'.$supervisorName.'%')->first();
                $professors[] = $professor;
                if ($professor != null) {
                    if ($project->id_pfe == null) {
                        $project->id_pfe = $project->id;
                    }
                    $jury = Jury::create([
                        'project_id' => $project->id,
                    ]);
                    $professor->juries()->attach($jury, ['role' => 'supervisor']);
                    
                }

            }
        });
        Notification::make()
        ->title($supervisors . ' internships assigned to projects')
        ->success()
        ->send();
    }
    public static function CreateFromInternshipAgreement(InternshipAgreement $record)
    {
        $project = Project::create([
        'id_pfe' => $record->id_pfe,
        'title' => $record->title,
        'organization' => $record->organization_name,
        'description' => $record->description,
        'start_date' => $record->starting_at,
        'end_date' => $record->ending_at,
        // 'has_teammate' => false,
        // 'teammate_status' => null,
        // 'teammate_id' => null,

        ]);
        // dd($record->student->projects);
        $project->students()->attach($record->student);
        $project->save();
        Notification::make()
        ->title('Project created successfully')
        ->success()
        ->send();
    }
}