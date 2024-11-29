<?php

namespace App\Services;

use App\Enums\DefenseStatus;
use App\Enums\Status;
use App\Models\FinalProject;
use App\Models\FinalYearInternshipAgreement;
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
            ->get();

        foreach ($agreements as $agreement) {
            // Check if project already exists using polymorphic relation
            dd($agreement->project);
            if ($agreement->project instanceof FinalProject) {
                self::$duplicateProjects++;

                continue;
            }

            // if ($agreement->final_project_id) {
            //     self::$duplicateProjects++;

            //     continue;
            // }

            $project = FinalProject::create([
                'title' => $agreement->title,
                'language' => 'fr', // Set default or get from agreement if available
                'start_date' => $agreement->starting_at,
                'end_date' => $agreement->ending_at,
                'organization_id' => $agreement->organization_id,
                'external_supervisor_id' => $agreement->external_supervisor_id,
                'defense_status' => 'pending',
            ]);

            // add polymorphic relation from agreement to project
            $agreement->project()->associate($project);

            self::$createdProjects++;
        }

        // Show notification with results
        Notification::make()
            ->title('Projects Assignment Complete')
            ->success()
            ->body('Created ' . self::$createdProjects . ' new projects. Found ' . self::$duplicateProjects . ' existing projects.')
            ->send();
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
