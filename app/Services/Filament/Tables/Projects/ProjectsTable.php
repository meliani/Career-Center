<?php

namespace App\Services\Filament\Tables\Projects;

use App\Filament\Actions\Action\AddOrganizationEvaluationSheetAction;
use Filament\Tables;
use Illuminate\Support\Facades\Storage;

class ProjectsTable
{
    public static function get()
    {
        // $closures = ['evaluation_sheet_url' => (fn ($record) => $record->evaluation_sheet_url)];

        // dd($closures['evaluation_sheet_url']($record));

        return [
            Tables\Columns\TextColumn::make('agreement_types')
                ->label('Agreement Type'),
            // ->formatStateUsing(function ($state) {
            //     return implode(', ', $state);
            // }),
            Tables\Columns\ColumnGroup::make(__('The student'))
                ->columns([
                    Tables\Columns\TextColumn::make('agreements.agreeable.id_pfe')
                        ->label('ID PFE')
                        ->searchable(true),
                    Tables\Columns\TextColumn::make('agreements.agreeable.student.full_name')
                        ->label('Student name')
                        ->searchable(
                            ['first_name', 'last_name']
                        )
                        ->limit(20),
                    Tables\Columns\TextColumn::make('students.program')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Program')
                        ->searchable(false)
                        ->badge(),
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
                        ->searchable(false)

                        ->badge(),
                    Tables\Columns\TextColumn::make('organization_evaluation_sheet_url')
                        // ->disabled(true)
                        ->searchable(false)
                        ->sortable(false)
                        ->label('Organization Evaluation Sheet')
                        ->action(AddOrganizationEvaluationSheetAction::make())
                        ->Placeholder(__('Click to add'))
                        ->icon('heroicon-o-document-magnifying-glass')
                        ->badge()
                        ->visible(function ($record) {
                            return auth()->user()->can('manage-projects');
                        })
                        ->color(fn ($record) => $record->organization_evaluation_sheet_url ? 'info' : 'primary')
                        ->formatStateUsing(fn ($record) => $record->organization_evaluation_sheet_url ? __('Open in new tab') : __('Click to add'))
                        ->tooltip(fn ($record) => $record->organization_evaluation_sheet_url ? __('Open document in a new tab') : __('Click to add an evaluation sheet'))
                        ->url(fn ($record) => $record->organization_evaluation_sheet_url, shouldOpenInNewTab: true),
                    // ->simpleLightbox(fn ($record) => $record->organization_evaluation_sheet_url),

                    /*      like this
                        Tables\Columns\TextColumn::make('pdf_file_name')
                    ->label('Agreement PDF')
                    ->limit(20)
                    ->url(fn (Apprenticeship $record) => URL::to($record->pdf_path . '/' . $record->pdf_file_name), shouldOpenInNewTab: true), */
                    Tables\Columns\TextColumn::make('evaluation_sheet_url')
                        ->searchable(false)
                        ->sortable(false)
                        ->label('Evaluation Sheet')
                        ->Placeholder(__('Not generated yet'))
                        ->icon('heroicon-o-document-magnifying-glass')
                        ->color(fn ($record) => $record->evaluation_sheet_url ? 'info' : 'gray')
                        ->formatStateUsing(fn ($record) => $record->evaluation_sheet_url ? __('Open in new tab') : __('Not generated yet'))
                        ->badge()
                        ->tooltip(fn ($record) => $record->evaluation_sheet_url ? __('Open document in a new tab') : __('Click to view the project'))
                        ->url(fn ($record) => $record->evaluation_sheet_url, shouldOpenInNewTab: true),
                    // ->simpleLightbox(Storage::disk('public')->url($closures['evaluation_sheet_url'](fn ($record) => $record->evaluation_sheet_url))),
                    // ->simpleLightbox(fn ($record) => $record->evaluation_sheet_url),
                    Tables\Columns\TextColumn::make('defense_authorized_by_user.name')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Authorized by')
                        ->badge(),
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
                        ->sortable(false),
                    Tables\Columns\TextColumn::make('supervisor.name')
                        ->label('Academic Supervisor')
                        // ->searchable(
                        //     ['first_name', 'last_name']
                        // )
                        ->sortable(false),
                    Tables\Columns\TextColumn::make('reviewers.name')
                        ->label('Reviewers')
                        ->searchable(false)
                        ->sortable(false),

                    Tables\Columns\TextColumn::make('language')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Detected language')
                        ->searchable(false)
                        ->sortable(),
                    Tables\Columns\TextColumn::make('title')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->limit(90)
                        ->searchable(false)
                        ->sortable(false),
                    Tables\Columns\TextColumn::make('defense_plan')
                        ->label('Defense plan')
                        ->toggleable(isToggledHiddenByDefault: false)
                        ->sortable(false)
                        ->searchable(false),
                    Tables\Columns\TextColumn::make('timetable.timeslot.start_time')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Defense start time')
                        ->searchable(false)
                        ->sortable(true)
                        ->dateTime('d M Y H:i'),
                    Tables\Columns\TextColumn::make('timetable.timeslot.end_time')
                        ->label('Defense end time')
                        ->searchable(false)
                        ->sortable(false)
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->dateTime('d M Y H:i'),
                    Tables\Columns\TextColumn::make('timetable.room.name')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->searchable(false)
                        ->sortable(false)
                        ->label('Room'),
                ]),

            // Tables\Columns\TextColumn::make('professors.department')
            //     ->label('department of supervisor'),

            Tables\Columns\ColumnGroup::make(__('Entreprise information'))
                ->columns([
                    Tables\Columns\TextColumn::make('organization_name')
                        ->label('Organization')
                        ->searchable(false)
                        ->sortable(false),
                    Tables\Columns\TextColumn::make('address')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Address')
                        ->searchable(false)
                        ->sortable(false),
                    Tables\Columns\TextColumn::make('parrain')
                        ->label('Le Parrain')
                        ->searchable(false)
                        ->sortable(false),
                    Tables\Columns\TextColumn::make('parrain_contact')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Contacts Parrain')
                        ->searchable(false)
                        ->sortable(false),

                    Tables\Columns\TextColumn::make('keywords')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Keywords')
                        ->searchable(false)
                        ->sortable(false)
                        ->limit(50),
                ]),

            Tables\Columns\TextColumn::make('created_at')
                ->searchable(false)
                ->sortable(false)
                ->dateTime()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
                ->searchable(false)
                ->sortable(false)
                ->dateTime()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }
}
