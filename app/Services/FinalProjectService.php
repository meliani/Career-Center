<?php

namespace App\Services;

use App\Enums\DefenseStatus;
use App\Enums\Status;
use App\Models\FinalYearInternshipAgreement;
use App\Models\Project as FinalProject;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

class FinalProjectService
{
    private static int $createdProjects = 0;

    private static int $duplicateProjects = 0;

    public function __construct(protected FinalProject $finalProject) {}

    public function create(array $data): FinalProject
    {
        return $this->finalProject->create($data);
    }

    public function update(FinalProject $finalProject, array $data): bool
    {
        return $finalProject->update($data);
    }

    public function updateDefenseStatus(FinalProject $finalProject, string $status, int $authorizedBy): bool
    {
        return $finalProject->update([
            'defense_status' => $status,
            'defense_authorized' => now(),
            'defense_authorized_by' => $authorizedBy,
        ]);
    }

    public function submitOrganizationEvaluation(FinalProject $finalProject, string $sheetUrl, int $submittedBy): bool
    {
        return $finalProject->update([
            'organization_evaluation_sheet_url' => $sheetUrl,
            'organization_evaluation_received_at' => now(),
            'organization_evaluation_received_by' => $submittedBy,
        ]);
    }

    public function getPendingDefense(): Collection
    {
        return $this->finalProject->where('defense_status', DefenseStatus::Pending)->get();
    }

    public function getAuthorizedDefense(): Collection
    {
        return $this->finalProject->where('defense_status', DefenseStatus::Authorized)->get();
    }

    public static function AssignFinalInternshipsToProjects(): void
    {
        self::$createdProjects = 0;
        self::$duplicateProjects = 0;

        $agreements = FinalYearInternshipAgreement::where('status', Status::Signed)
        // not having a project polymorphic using ProjectAgreement
            ->doesntHave('project')
            ->get();

        foreach ($agreements as $agreement) {

            $project = FinalProject::create([
                'title' => $agreement->title,
                'language' => null, // Set default or get from agreement if available
                'start_date' => $agreement->starting_at,
                'end_date' => $agreement->ending_at,
                // 'organization_id' => $agreement->organization_id,
                // 'external_supervisor_id' => $agreement->external_supervisor_id,
                'defense_status' => 'Pending',
            ]);

            $agreement->project()->attach($project);

            self::$createdProjects++;
        }

        // Show notification with results
        Notification::make()
            ->title(__('Projects Assignment Complete'))
            ->success()
            ->body(self::$createdProjects ? __(':count new project created.', ['count' => self::$createdProjects]) : __('No new projects created'))->send();
    }

    private static function createFromAgreement(FinalYearInternshipAgreement $internshipAgreement): FinalProject
    {
        if ($internshipAgreement->project instanceof FinalProject) {
            self::$duplicateProjects++;

            return $internshipAgreement->project;
        }

        /** @var FinalProject $project */
        $project = FinalProject::create([
            'title' => $internshipAgreement->title,
            'start_date' => $internshipAgreement->starting_at,
            'end_date' => $internshipAgreement->ending_at,
        ]);

        $internshipAgreement->final_project_id = $project->id;
        $internshipAgreement->save();

        if ($internshipAgreement->student) {
            $project->students()->sync([$internshipAgreement->student->id]);
        }

        self::$createdProjects++;

        return $project;
    }
}
