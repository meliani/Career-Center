<?php

namespace App\Filament\Administration\Widgets;

use App\Models\Professor;
use App\Models\Year;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class ProfessorsParticipationTable extends BaseWidget
{
    protected static bool $isLazy = true;

    protected static ?string $heading = 'Participation aux soutenances PFE';

    public function table(Table $table): Table
    {
        // Get the current year ID
        $currentYearId = Year::current()->id;

        // we'll professors and their participation in defenses from pivot table there is jury_role(Reviewer1, Reviewer2, Supervisor) so we want to count all of them and count eveny one seperately
        $query = Professor::query()
            ->withCount([
                // Assuming 'projects' is the name of the relationship on the Professor model
                'allProjects as total_participation_count' => function (Builder $query) use ($currentYearId) {
                    $query->whereHas('agreements', function ($q) use ($currentYearId) {
                        $q->whereMorphRelation(
                            'agreeable',
                            '*',
                            'year_id',
                            $currentYearId
                        );
                    });
                },
                'allProjects as total_presence_count' => function (Builder $query) use ($currentYearId) {
                    $query->where('professor_projects.was_present', true)
                        ->whereHas('agreements', function ($q) use ($currentYearId) {
                            $q->whereMorphRelation(
                                'agreeable',
                                '*',
                                'year_id',
                                $currentYearId
                            );
                        });
                },
                'allProjects as supervisor_count' => function (Builder $query) use ($currentYearId) {
                    $query->where('professor_projects.jury_role', 'Supervisor')
                        ->whereHas('agreements', function ($q) use ($currentYearId) {
                            $q->whereMorphRelation(
                                'agreeable',
                                '*',
                                'year_id',
                                $currentYearId
                            );
                        });
                },
                'allProjects as total_reviewer_count' => function (Builder $query) use ($currentYearId) {
                    $query->whereIn('professor_projects.jury_role', ['Reviewer1', 'Reviewer2'])
                        ->whereHas('agreements', function ($q) use ($currentYearId) {
                            $q->whereMorphRelation(
                                'agreeable',
                                '*',
                                'year_id',
                                $currentYearId
                            );
                        });
                },
                'allProjects as reviewer1_count' => function (Builder $query) use ($currentYearId) {
                    $query->where('professor_projects.jury_role', 'Reviewer1')
                        ->whereHas('agreements', function ($q) use ($currentYearId) {
                            $q->whereMorphRelation(
                                'agreeable',
                                '*',
                                'year_id',
                                $currentYearId
                            );
                        });
                },
                'allProjects as reviewer2_count' => function (Builder $query) use ($currentYearId) {
                    $query->where('professor_projects.jury_role', 'Reviewer2')
                        ->whereHas('agreements', function ($q) use ($currentYearId) {
                            $q->whereMorphRelation(
                                'agreeable',
                                '*',
                                'year_id',
                                $currentYearId
                            );
                        });
                },
            ]);

        return $table
            ->query(
                $query
            )
            ->deferLoading()
            // ->paginated(false)
            // ->extremePaginationLinks(true)
            ->paginationPageOptions([5, 10, 25, 50, 100])
            ->defaultPaginationPageOption(5)
            ->defaultSort('total_participation_count', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Professor name')
                    ->sortable()
                    ->searchable(
                        ['first_name', 'last_name']
                    ),
                Tables\Columns\TextColumn::make('department')
                    ->label('Department')
                    ->searchable(false),
                Tables\Columns\TextColumn::make('total_presence_count')
                    ->label('Total presence')
                    ->sortable()
                    ->searchable(false)
                    ->hidden(true)
                    ->visible(fn () => auth()->user()->isAdministrator() || auth()->user()->isAdministrativeSupervisor()),
                Tables\Columns\TextColumn::make('total_participation_count')
                    ->label('Total mentoring participations')
                    ->sortable()
                    ->searchable(false),
                Tables\Columns\TextColumn::make('supervisor_count')
                    ->label('Supervising participations')
                    ->sortable()
                    ->searchable(false)
                    ->visible(fn () => auth()->user()->isAdministrator() || auth()->user()->isAdministrativeSupervisor()),
                Tables\Columns\TextColumn::make('total_reviewer_count')
                    ->label('Reviewing participations')
                    ->sortable()
                    ->searchable(false)
                    ->visible(fn () => auth()->user()->isAdministrator() || auth()->user()->isAdministrativeSupervisor()),
                Tables\Columns\TextColumn::make('reviewer1_count')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Reviewer1 participations')
                    ->sortable()
                    ->searchable(false)
                    ->visible(fn () => auth()->user()->isAdministrator() || auth()->user()->isAdministrativeSupervisor()),
                Tables\Columns\TextColumn::make('reviewer2_count')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Reviewer2 participations')
                    ->sortable()
                    ->searchable(false)
                    ->visible(fn () => auth()->user()->isAdministrator() || auth()->user()->isAdministrativeSupervisor()),

            ]);
    }
}
