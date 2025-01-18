<?php

namespace App\Services\Filament\Tables\Projects;

use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns;

class ProjectsGrid
{
    public static function get(): array
    {
        return [

            Tables\Columns\Layout\Split::make([
                Tables\Columns\TextColumn::make('students.full_name')
                    ->weight(FontWeight::Bold)
                    ->label('Student name')
                    ->searchable(
                        ['first_name', 'last_name']
                    )
                    ->sortable(false),

            ]),

            Tables\Columns\Layout\Panel::make([

                Tables\Columns\TextColumn::make('organization_name')
                    ->weight(FontWeight::Bold)
                    ->grow(true)
                    ->verticallyAlignCenter()
                    // ->alignRight()
                    ->searchable(false)
                    ->sortable(false),
                Tables\Columns\TextColumn::make('address')
                    // ->alignRight()
                    ->searchable(false)
                    ->sortable(false),
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
                Columns\TextColumn::make('title')
                    ->markdown()
                    ->label('Title')
                    ->html() // Add this line to enable HTML rendering
                    ->searchable(true)
                    ->sortable(false)
                    ->description(fn ($record) => 'From ' . $record->start_date->format('d/m/Y') . ' to ' . $record->end_date->format('d/m/Y'))
                    ->limit(100)
                    ->formatStateUsing(function ($record) {
                        $agreementTitle = $record->final_internship_agreements->first()?->title ?? '';
                        $projectTitle = $record->title ?? '';
                        $matches = trim(strtolower($agreementTitle)) === trim(strtolower($projectTitle));

                        if ($matches) {
                            return "{$projectTitle}";
                        }

                        return "{$projectTitle}<br>" .
                               '<strong>' . __('Agreement title') . ":</strong> {$agreementTitle}";
                    }),
            ]),
            Tables\Columns\Layout\Split::make([
                Tables\Columns\TextColumn::make('supervisor.name')
                    ->searchable(false)
                    ->description(__('Supervisor'), position: 'above')
                    ->sortable(false),
            ]),
            Tables\Columns\Layout\Split::make([
                Tables\Columns\TextColumn::make('reviewers.name')
                    ->description(__('Reviewers'), position: 'above')
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
                    Tables\Columns\TextColumn::make('timetable.timeslot.start_time')
                        ->description(__('Defense start time'), position: 'above')
                        ->searchable()
                        ->sortable(),
                ]),
            ])
                ->collapsible(),
        ];
    }
}
