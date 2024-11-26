<?php

namespace App\Services;

use App\Enums\Status;
use App\Models\FinalYearInternshipAgreement;
use App\Models\InternshipAgreement;
use App\Models\Professor;
use App\Models\Project;
use Filament\Notifications\Notification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Gate;

class ProjectService extends Facade
{
    protected static bool $forceOverwrite;

    private static int $createdProjects;

    public static int $overwrittenProjects;

    private static int $duplicateProjects;

    private static $assignedProfessors;

    private static $existingProfessors;

    public function __construct()
    {
        self::$forceOverwrite = false;
        self::$createdProjects = 0;
        self::$overwrittenProjects = 0;
        self::$duplicateProjects = 0;
        self::$assignedProfessors = 0;
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
            if (Gate::denies('batch-assign-internships-to-projects')) {
                throw new AuthorizationException;
            }
            $signedInternships = InternshipAgreement::signed()->get();

            foreach ($signedInternships as $signedInternship) {
                ProjectService::SyncAgreementWithProjects($signedInternship);
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
                __(
                    ':x projects created, and :y projects were ignored.',
                    [
                        'x' => self::$createdProjects,
                        'y' => self::$duplicateProjects,
                    ]
                )
            )
            ->success()
            ->send();
    }

    public static function AssignFinalInternshipsToProjects()
    {
        self::setForceOverwrite(false);
        self::setOverwrittenProjects(0);
        self::setCreatedProjects(0);
        self::setDuplicateProjects(0);

        try {
            if (Gate::denies('batch-assign-internships-to-projects')) {
                throw new AuthorizationException;
            }

            $signedInternships = FinalYearInternshipAgreement::where('status', Status::Signed)->get();

            foreach ($signedInternships as $signedInternship) {
                ProjectService::SyncFinalAgreementWithProjects($signedInternship);
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
                __(
                    ':x projects created, and :y projects were ignored.',
                    [
                        'x' => self::$createdProjects,
                        'y' => self::$duplicateProjects,
                    ]
                )
            )
            ->success()
            ->send();
    }

    private static function SyncAgreementWithProjects(InternshipAgreement $internshipAgreement)
    {

        $project = ProjectService::CreateFromInternshipAgreement($internshipAgreement);

    }

    private static function SyncFinalAgreementWithProjects(FinalYearInternshipAgreement $internshipAgreement)
    {
        $project = ProjectService::CreateFromFinalInternshipAgreement($internshipAgreement);
    }

    private static function SyncStudentToProjectFromInternshipAgreement(InternshipAgreement $internshipAgreement, Project $project)
    {
        $project->students()->sync($internshipAgreement->student);
        $project->save();
    }

    private static function CreateFromInternshipAgreement(InternshipAgreement $internshipAgreement): Project
    {
        if ($internshipAgreement->project) {

            $project = $internshipAgreement->project;

            if ($project->students->count() > 1) {
                return $project;
            } else {
                // if (! $project->id_pfe) {
                //     $project->id_pfe = $internshipAgreement->id_pfe;
                // }
                // $project->title = $internshipAgreement->title;
                // $project->description = $internshipAgreement->description;
                // $project->organization = $internshipAgreement->organization_name;
                // $project->start_date = $internshipAgreement->starting_at;
                // $project->end_date = $internshipAgreement->ending_at;
                // $project->save();
                // increment overwrittenProjects global variable
                // self::$overwrittenProjects++;
                self::$duplicateProjects++;

                return $project;
            }
        } else {
            $project = Project::create([
                // 'id_pfe' => $internshipAgreement->id_pfe,
                'title' => $internshipAgreement->title,
                // 'description' => $internshipAgreement->description,
                // 'organization' => $internshipAgreement->organization_name,
                'start_date' => $internshipAgreement->starting_at,
                'end_date' => $internshipAgreement->ending_at,
            ]);

            $project->save();
            $internshipAgreement->project_id = $project->id;
            $internshipAgreement->save();
            self::SyncStudentToProjectFromInternshipAgreement($internshipAgreement, $project);
            self::$createdProjects++;

            return $project;
        }

    }

    private static function CreateFromFinalInternshipAgreement(FinalYearInternshipAgreement $internshipAgreement): Project
    {
        if ($internshipAgreement->project) {
            $project = $internshipAgreement->project;
            self::$duplicateProjects++;

            return $project;
        } else {
            $project = Project::create([
                'title' => $internshipAgreement->title,
                'start_date' => $internshipAgreement->starting_at,
                'end_date' => $internshipAgreement->ending_at,
            ]);

            $project->save();
            $internshipAgreement->project_id = $project->id;
            $internshipAgreement->save();
            $project->students()->sync($internshipAgreement->student);
            self::$createdProjects++;

            return $project;
        }
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
                        $professor->projects()->attach([$internshipAgreement->project_id => ['jury_role' => 'Supervisor']]);
                        self::$existingProfessors++;
                    } else {
                        $professor->projects()->attach([$internshipAgreement->project_id => ['jury_role' => 'Supervisor']]);
                        self::$assignedProfessors++;
                    }
                }
            }
        });
        Notification::make()
            ->title(self::$assignedProfessors . ' Professors imported from internship agreements and '
                . self::$existingProfessors . ' Professors already assigned to the projects.')
            ->success()
            ->send();
    }
}
