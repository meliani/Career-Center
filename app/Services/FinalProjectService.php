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

    public static function AssignFinalInternshipsToProjects(bool $override = false): void
    {
        self::$createdProjects = 0;
        self::$duplicateProjects = 0;

        $agreements = FinalYearInternshipAgreement::where('status', Status::Signed);

        if (! $override) {
            $agreements = $agreements->doesntHave('project');
        }

        $agreements = $agreements->get();

        foreach ($agreements as $agreement) {
            $projectData = [
                'title' => $agreement->title,
                'language' => null,
                'start_date' => $agreement->starting_at,
                'end_date' => $agreement->ending_at,
                'organization_id' => $agreement->organization_id,
                'external_supervisor_id' => $agreement->external_supervisor_id,
                'parrain_id' => $agreement->parrain_id,
                'defense_status' => 'Pending',
            ];

            if ($override && $agreement->project) {
                $agreement->project->update($projectData);
                self::$duplicateProjects++;
            } else {
                $project = FinalProject::create($projectData);
                $agreement->project()->attach($project);
                self::$createdProjects++;
            }
        }

        $message = [];
        if (self::$createdProjects) {
            $message[] = __(':count new projects created', ['count' => self::$createdProjects]);
        }
        if (self::$duplicateProjects) {
            $message[] = __(':count existing projects updated', ['count' => self::$duplicateProjects]);
        }

        Notification::make()
            ->title(__('Projects Assignment Complete'))
            ->success()
            ->body($message ? implode(', ', $message) : __('No changes made'))
            ->send();
    }
}
