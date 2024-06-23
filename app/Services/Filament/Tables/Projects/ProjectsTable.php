<?php

namespace App\Services\Filament\Tables\Projects;

use Filament\Tables;

class ProjectsTable
{
    public static function get()
    {
        return [
            Tables\Columns\ColumnGroup::make(__('Defense authorization'))
                ->columns([
                    Tables\Columns\TextColumn::make('defense_status')
                        ->label('Status')
                        ->sortable()
                        ->searchable()
                        ->badge(),
                    Tables\Columns\TextColumn::make('evaluation_sheet_url')
                        ->label('Evaluation sheet')
                        ->searchable(false),
                ]),

            Tables\Columns\ColumnGroup::make(__('Defense information'))
                ->columns([
                    // Tables\Columns\TextColumn::make('supervisor.name')
                    //     ->label('Supervisor')
                    //     ->searchable(
                    //         ['first_name', 'last_name']
                    //     ),
                    Tables\Columns\TextColumn::make('external_supervisor')
                        ->label('External Supervisor')
                        ->limit(30)
                        ->searchable(true),
                    Tables\Columns\TextColumn::make('external_supervisor_contact')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Contacts External Supervisor')
                        ->searchable(false)
                        ->sortable(),
                    Tables\Columns\TextColumn::make('supervisor.name')
                        ->label('Supervisor'),
                    Tables\Columns\TextColumn::make('reviewers.name')
                        ->label('Reviewers')
                        ->searchable(
                            ['first_name', 'last_name']
                        )
                        ->sortableMany(),

                    Tables\Columns\TextColumn::make('language')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Detected language')
                        ->searchable(false)
                        ->sortable(),
                    Tables\Columns\TextColumn::make('title')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->limit(90)
                        ->searchable()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('defense_plan')
                        ->label('Defense Plan')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->searchable(false),
                    Tables\Columns\TextColumn::make('timetable.timeslot.start_time')
                        ->label('Defense start time')
                        ->searchable(false)
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->dateTime('d M Y H:i'),
                    Tables\Columns\TextColumn::make('timetable.timeslot.end_time')
                        ->label('Defense end time')
                        ->searchable(false)
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->dateTime('d M Y H:i'),
                    Tables\Columns\TextColumn::make('timetable.room.name')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Room'),
                ]),
            Tables\Columns\ColumnGroup::make(__('The student'))
                ->columns([
                    Tables\Columns\TextColumn::make('internship_agreements.id_pfe')
                        ->label('ID PFE')
                        ->sortable()
                        ->sortableMany()
                        ->searchable(),
                    Tables\Columns\TextColumn::make('students.full_name')
                        ->label('Student name')
                        ->searchable(
                            ['first_name', 'last_name']
                        )
                        ->sortableMany(),
                    Tables\Columns\TextColumn::make('students.program')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Program')
                        ->searchable()->sortableMany()->badge(),
                    // Tables\Columns\TextColumn::make('internship_agreements.assigned_department')
                    //     ->label('Assigned department')
                    //     // ->sortable(false)
                    //     ->sortableMany()
                    //     ->searchable(),
                    Tables\Columns\TextColumn::make('department')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Assigned department')
                        ->searchable(false)
                        ->sortableMany(),
                    Tables\Columns\TextColumn::make('start_date')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Project start date')
                        ->date('d/m/Y')
                        ->sortable(),
                    Tables\Columns\TextColumn::make('end_date')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Project end date')
                        ->date('d/m/Y')
                        ->sortable(),
                ]),
            // Tables\Columns\TextColumn::make('professors.department')
            //     ->label('department of supervisor'),

            Tables\Columns\ColumnGroup::make(__('Entreprise information'))
                ->columns([
                    Tables\Columns\TextColumn::make('organization_name')
                        ->label('Organization')
                        ->searchable(false),
                    Tables\Columns\TextColumn::make('address')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Address')
                        ->searchable(false),
                    Tables\Columns\TextColumn::make('parrain')
                        ->label('Le Parrain')
                        ->searchable(false)
                        ->sortable(),
                    Tables\Columns\TextColumn::make('parrain_contact')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Contacts Parrain')
                        ->searchable(false)
                        ->sortable(),

                    Tables\Columns\TextColumn::make('keywords')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Keywords')
                        ->searchable(false)
                        ->limit(50),
                ]),

            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }
}
