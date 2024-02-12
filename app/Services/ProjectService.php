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
use Illuminate\Support\Facades\Facade;

class ProjectService extends Facade
{
    protected static bool $forceOverwrite;
    private static int $createdProjects;
    public static int $overwrittenProjects;
    private static int $duplicateProjects;
    private static $affectedProffessors;
    private static $existingProfessors;

    public function __construct()
    {
        self::$forceOverwrite = false;
        self::$createdProjects = 0;
        self::$overwrittenProjects = 0;
        self::$duplicateProjects = 0;
        self::$affectedProffessors = 0;
        self::$existingProfessors = 0;
    }
    public static function setForceOverwrite($value)
    {
        self::$forceOverwrite = $value;
    }

    public static function setCreatedProjects($value)
    {
        self::$createdProjects = $value;
    }
    public static function setOverwrittenProjects($value)
    {
        self::$overwrittenProjects = $value;
    }
    public static function setDuplicateProjects($value)
    {
        self::$duplicateProjects = $value;
    }
    public static function AssignInternshipsToProjects()
    {
        self::setForceOverwrite(false);
        self::setOverwrittenProjects(0);
        self::setCreatedProjects(0);
        self::setDuplicateProjects(0);
        try {
            if (Gate::denies('batch-assign-internships-to-projects',)) {
                throw new AuthorizationException();
            }
            $signedInternships = InternshipAgreement::signed()->get();

            foreach ($signedInternships as $signedInternship) {
                try {
                    // Try Create a project from the internship agreement
                    $project = ProjectService::CreateFromInternshipAgreement($signedInternship);
                    ProjectService::SyncStudentToProjectFromInternshipAgreement($signedInternship, $project);
                } catch (\Exception $e) {
                    // catch duplicate project exception
                    if ($e->getCode() == 23000) {
                        // If the project already exists, we can overwrite it
                        if (self::$forceOverwrite) {
                            $project = ProjectService::OverwriteFromInternshipAgreement($signedInternship);
                            ProjectService::SyncStudentToProjectFromInternshipAgreement($signedInternship, $project);
                        } else {
                            continue;
                        }
                    } else {
                        Log::error($e->getMessage());
                        throw new \Exception('There was an error creating the project. Please try again.');
                    }
                }
            }
        } catch (AuthorizationException $e) {
            Notification::make()
                ->title('Sorry You must be an Administrator.')
                ->danger()
                ->send();
            return response()->json(['error' => 'This action is unauthorized.'], 403);
        }

        Notification::make()
            ->title(
                'Projects created: ' . self::$createdProjects .
                    ' Projects overwritten: ' . self::$overwrittenProjects .
                    ' Duplicate projects Found : ' . self::$duplicateProjects
            )
            ->success()
            ->send();
    }
    private static function SyncStudentToProjectFromInternshipAgreement(InternshipAgreement $internshipAgreement, Project $project)
    {
        $project->students()->sync($internshipAgreement->student);
        $project->save();
    }

    private static function CreateFromInternshipAgreement(InternshipAgreement $internshipAgreement): Project
    {
        //  check if the project already exists from the internshipAgreements() relationship
        if ($internshipAgreement->project_id != null) {
            $project = Project::find($internshipAgreement->project_id);
            if ($project != null) {
                $project->id_pfe = $internshipAgreement->id_pfe;
                $project->title = $internshipAgreement->title;
                $project->description = $internshipAgreement->description;
                $project->organization = $internshipAgreement->organization_name;
                $project->start_date = $internshipAgreement->starting_at;
                $project->end_date = $internshipAgreement->ending_at;
                $project->save();
                // increment overwrittenProjects global variable
                self::$overwrittenProjects++;
                return $project;
            }
        }
        $project = Project::create([
            'id_pfe' => $internshipAgreement->id_pfe,
            'title' => $internshipAgreement->title,
            'description' => $internshipAgreement->description,
            'organization' => $internshipAgreement->organization_name,
            'start_date' => $internshipAgreement->starting_at,
            'end_date' => $internshipAgreement->ending_at,
        ]);

        $project->save();
        $internshipAgreement->project_id = $project->id;
        $internshipAgreement->save();
        self::$createdProjects++;

        return $project;
    }
    private static function OverwriteFromInternshipAgreement(InternshipAgreement $internshipAgreement): Project
    {
        if ($internshipAgreement->project_id != null) {
            $project = Project::find($internshipAgreement->project_id);
            if ($project != null) {
                $project->id_pfe = $internshipAgreement->id_pfe;
                $project->title = $internshipAgreement->title;
                $project->description = $internshipAgreement->description;
                $project->organization = $internshipAgreement->organization;
                $project->start_date = $internshipAgreement->starting_at;
                $project->end_date = $internshipAgreement->ending_at;
                $project->save();
                self::$duplicateProjects++;
                return $project;
            }
        }
        $project = Project::find($internshipAgreement->project_id);
        $internshipAgreement->project_id = $project->id;
        $internshipAgreement->save();

        return $project;
    }

    public static function ImportProfessorsFromInternshipAgreements()
    {
        $internshipAgreements = InternshipAgreement::signed()->get();
        $internshipAgreements->each(function ($internshipAgreement) {
            if ($internshipAgreement->int_adviser_name != null && $internshipAgreement->int_adviser_name != 'NA') {
                $professor = Professor::where('name', 'like', '%' . $internshipAgreement->int_adviser_name . '%')->first();
                if ($professor != null) {
                    $project = Project::where('id', $internshipAgreement->project_id)->first();
                    if ($project->professors->contains($professor)) {
                        // $professor->projects()->sync([$internshipAgreement->project_id => ['role' => 'Supervisor']]);
                        $professor->projects()->detach($internshipAgreement->project_id);
                        $professor->projects()->attach([$internshipAgreement->project_id => ['role' => 'Supervisor']]);
                        self::$existingProfessors++;
                    } else {
                        $professor->projects()->attach([$internshipAgreement->project_id => ['role' => 'Supervisor']]);
                        self::$affectedProffessors++;
                    }
                }
            }
        });
        Notification::make()
            ->title(self::$affectedProffessors . ' Professors imported from internship agreements and '
                . self::$existingProfessors . ' Professors already assigned to the projects.')
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
                $professor = Professor::where('name', 'like', '%' . $supervisorName . '%')->first();
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
}
