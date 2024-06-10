<?php

namespace App\Services\Filament\Tables\Projects;

use Filament\Tables;

class ProjectsGrid
{
    public static function get()
    {
        return [

            Tables\Columns\Layout\Split::make([
                Tables\Columns\TextColumn::make('students.full_name')
                    ->label('Student name')
                    ->searchable(
                        ['first_name', 'last_name']
                    )
                    ->sortable(false),

            ]),

            Tables\Columns\Layout\Split::make([
                Tables\Columns\TextColumn::make('internship_agreements.id_pfe')
                    ->grow(false)
                    ->label('ID PFE')
                    ->sortableMany()
                    ->searchable()->badge(),
                Tables\Columns\TextColumn::make('students.program')
                    ->grow(false)
                    ->label('Program')
                    ->searchable()->sortableMany()->badge(),
                Tables\Columns\TextColumn::make('department')
                    ->grow(false)
                    ->label('Assigned department')
                    ->searchable(false)
                    ->sortable(false)->badge(),
            ]),
            Tables\Columns\TextColumn::make('organization_name')
                ->badge()
                ->grow(false)
                ->verticallyAlignCenter()
                ->alignRight()
                ->searchable(false)
                ->sortable(false),
            Tables\Columns\Layout\Split::make([
                Tables\Columns\TextColumn::make('address')
                    ->searchable(false)
                    ->sortable(false),
            ]),
            Tables\Columns\Layout\Split::make([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('project_dates')
                        ->description(__('Project dates'), position: 'above')
                        ->searchable(false)
                        ->sortable(false),
                    Tables\Columns\TextColumn::make('defense_plan')
                        ->description(__('Defense plan'), position: 'above')
                        ->searchable(false)
                        ->sortable(false),
                ]),
            ])->collapsible(),

            Tables\Columns\Layout\Split::make([
                Tables\Columns\TextColumn::make('title')
                    ->description(__('Subject'), position: 'above')
                    ->limit(150)
                    ->searchable()
                    ->sortable(false),
            ]),
            Tables\Columns\Layout\Split::make([
                Tables\Columns\TextColumn::make('supervisor.name')
                    ->searchable(false)
                    ->description(__('Supervisor'), position: 'above')
                    ->sortable(false),
            ]),
            Tables\Columns\Layout\Split::make([
                Tables\Columns\TextColumn::make('reviewers.name')
                    ->searchable(false)
                    ->sortable(false),
            ]),
            Tables\Columns\Layout\Split::make([
                Tables\Columns\Layout\Stack::make([

                    Tables\Columns\TextColumn::make('parrain')
                        ->description(__('Parrain'), position: 'above')
                        ->searchable(false)
                        ->sortable(false),
                    // Tables\Columns\TextColumn::make('parrain_contact')
                    //     ->searchable(false)
                    //     ->sortable(),
                    Tables\Columns\TextColumn::make('external_supervisor')
                        ->description(__('External supervisor'), position: 'above')
                        ->searchable(false)
                        ->sortable(false),
                    // Tables\Columns\TextColumn::make('external_supervisor_contact')
                    //     ->searchable(false)
                    //     ->sortable(),
                    // Tables\Columns\TextColumn::make('internship_agreements.keywords')
                    //     ->label('Keywords')
                    //     ->searchable()
                    //     ->limit(50),
                ]),
            ]),
        ];
    }
}
