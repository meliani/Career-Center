<?php

namespace App\Filament\Administration\Widgets;

use App\Models\Project;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Support\Concerns\HasExtraAttributes;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class AdministratorProjectsWidget extends Widget
{
    use HasExtraAttributes;
    use InteractsWithActions;

    protected static string $view = 'filament.administration.widgets.administrator-projects-widget';

    protected int | string | array $columnSpan = 'full';

    protected function getProjects(): Collection
    {
        return Project::query()
            ->with(['organization', 'agreements.agreeable.student'])
            ->orderBy('created_at', 'desc')
            ->active()
            ->get();
    }

    public function scheduleDefense(): void
    {
        // dd('scheduleDefense');
        $this->mountAction('scheduleDefense');
    }

    public function changeStatus(): void
    {
        $this->mountAction('changeStatus');
    }

    protected function getScheduleDefenseAction(): Action
    {
        return Action::make('scheduleDefense')
            ->record(fn (array $arguments) => Project::find($arguments['projectId']))
            ->form([
                DateTimePicker::make('defense_date')
                    ->required()
                    ->native(false),
            ])
            ->action(function (Project $record, array $data): void {
                $record->update([
                    'defense_date' => $data['defense_date'],
                    'defense_status' => 'Authorized',
                ]);
            });
    }

    protected function getChangeStatusAction(): Action
    {
        return Action::make('changeStatus')
            ->record(fn (array $arguments) => Project::find($arguments['projectId']))
            ->form([
                Select::make('defense_status')
                    ->options([
                        'Pending' => 'Pending',
                        'Authorized' => 'Authorized',
                        'Completed' => 'Completed',
                        'Postponed' => 'Postponed',
                        'Rejected' => 'Rejected',
                    ])
                    ->required(),
            ])
            ->action(function (Project $record, array $data): void {
                $record->update([
                    'defense_status' => $data['defense_status'],
                ]);
            });
    }
}
