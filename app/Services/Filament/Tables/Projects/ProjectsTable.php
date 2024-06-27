<?php

namespace App\Services\Filament\Tables\Projects;

use Filament\Tables;

class ProjectsTable
{
    public static function get()
    {
        return [
            Tables\Columns\ColumnGroup::make(__('The student'))
                ->columns([
                    Tables\Columns\TextColumn::make('internship_agreements.id_pfe')
                        ->label('ID PFE')
                        ->sortable()
                        ->sortableMany()
                        ->searchable(true),
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
                        ->searchable(false)
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Project start date')
                        ->date('d/m/Y'),
                    Tables\Columns\TextColumn::make('end_date')
                        ->searchable(false)
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Project end date')
                        ->date('d/m/Y'),
                ]),
            Tables\Columns\ColumnGroup::make(__('Defense authorization'))
                ->columns([
                    Tables\Columns\TextColumn::make('defense_status')
                        ->label('Status')
                        ->sortable()
                        ->searchable()
                        ->badge(),
                    /*      like this
                        Tables\Columns\TextColumn::make('pdf_file_name')
                    ->label('Agreement PDF')
                    ->limit(20)
                    ->url(fn (Apprenticeship $record) => URL::to($record->pdf_path . '/' . $record->pdf_file_name), shouldOpenInNewTab: true), */
                    Tables\Columns\TextColumn::make('evaluation_sheet_url')
                        ->label('Evaluation Sheet')
                        ->limit(20)
                        ->url(fn ($record) => $record->evaluation_sheet_url, shouldOpenInNewTab: true),
                    Tables\Columns\TextColumn::make('defense_authorized_by_user.name')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Authorized by')
                        ->searchable(false)
                        ->badge()
                        ->sortable(),
                ]),

            Tables\Columns\ColumnGroup::make(__('Defense information'))
                ->columns([
                    // Tables\Columns\TextColumn::make('supervisor.name')
                    //     ->label('Supervisor')
                    //     ->searchable(
                    //         ['first_name', 'last_name']
                    //     ),
                    Tables\Columns\TextColumn::make('external_supervisor_name')
                        ->label('External Supervisor')
                        ->limit(30)
                        ->searchable(false),
                    Tables\Columns\TextColumn::make('external_supervisor_contact')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Contacts External Supervisor')
                        ->searchable(false)
                        ->sortable(),
                    Tables\Columns\TextColumn::make('supervisor.name')
                        ->label('Supervisor'),
                    Tables\Columns\TextColumn::make('reviewers.name')
                        ->label('Reviewers')
                        ->searchable(false)
                        // ->searchable(
                        //     ['first_name', 'last_name']
                        // )
                        ->sortableMany(),

                    Tables\Columns\TextColumn::make('language')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Detected language')
                        ->searchable(false)
                        ->sortable(),
                    Tables\Columns\TextColumn::make('title')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->limit(90)
                        ->searchable(false)
                        ->sortable(),
                    Tables\Columns\TextColumn::make('defense_plan')
                        ->label('Defense Plan')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->sortable(false)
                        ->searchable(false),
                    Tables\Columns\TextColumn::make('timetable.timeslot.start_time')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Defense start time')
                        ->searchable(false)
                        ->sortable()
                        ->dateTime('d M Y H:i'),
                    Tables\Columns\TextColumn::make('timetable.timeslot.end_time')
                        ->label('Defense end time')
                        ->searchable(false)
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->dateTime('d M Y H:i'),
                    Tables\Columns\TextColumn::make('timetable.room.name')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->searchable(false)
                        ->label('Room'),
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
                ->searchable(false)
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
                ->searchable(false)
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }
}
