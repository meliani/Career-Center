<?php

namespace App\Filament\Widgets;

use App\Models\Professor;
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

        // we'll professors and their participation in defenses from pivot table there is jury_role(Reviewer1, Reviewer2, Supervisor) so we want to count all of them and count eveny one seperately
        $query = Professor::query()
            ->withCount([
                // Assuming 'projects' is the name of the relationship on the Professor model
                'allProjects as total_participation_count', // Count total participations
                'allProjects as total_presence_count' => function (Builder $query) {
                    $query->where('professor_project.was_present', true); // Adjust 'pivot_table_name' to your actual pivot table name
                },
                'allProjects as supervisor_count' => function (Builder $query) {
                    $query->where('professor_project.jury_role', 'Supervisor'); // Adjust 'pivot_table_name' to your actual pivot table name
                },
                'allProjects as reviewer1_count' => function (Builder $query) {
                    $query->where('professor_project.jury_role', 'Reviewer1'); // Adjust 'pivot_table_name' to your actual pivot table name
                },
                'allProjects as reviewer2_count' => function (Builder $query) {
                    $query->where('professor_project.jury_role', 'Reviewer2'); // Adjust 'pivot_table_name' to your actual pivot table name
                },
            ]);

        return $table
            ->query(
                $query
            )
            ->deferLoading()
            // ->paginated(false)
            // ->extremePaginationLinks(true)
            ->paginationPageOptions([5, 10])
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
                    ->visible(fn () => auth()->user()->isAdministrator() || auth()->user()->isAdministrativeSupervisor()),
                Tables\Columns\TextColumn::make('total_participation_count')
                    ->label('Total participations')
                    ->sortable()
                    ->searchable(false),
                Tables\Columns\TextColumn::make('supervisor_count')
                    ->label('Supervisor participations')
                    ->sortable()
                    ->searchable(false)
                    ->visible(fn () => auth()->user()->isAdministrator() || auth()->user()->isAdministrativeSupervisor()),
                Tables\Columns\TextColumn::make('reviewer1_count')
                    ->label('Reviewer1 participations')
                    ->sortable()
                    ->searchable(false)
                    ->visible(fn () => auth()->user()->isAdministrator() || auth()->user()->isAdministrativeSupervisor()),
                Tables\Columns\TextColumn::make('reviewer2_count')
                    ->label('Reviewer2 participations')
                    ->sortable()
                    ->searchable(false)
                    ->visible(fn () => auth()->user()->isAdministrator() || auth()->user()->isAdministrativeSupervisor()),

            ]);
    }
}
