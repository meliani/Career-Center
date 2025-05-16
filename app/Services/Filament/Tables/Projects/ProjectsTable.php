<?php

namespace App\Services\Filament\Tables\Projects;

use App\Filament\Actions\Action\AddOrganizationEvaluationSheetAction;
use App\Models\FinalYearInternshipAgreement;
use App\Models\InternshipAgreement;
use App\Models\ProjectAgreement;
use Carbon\Carbon;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class ProjectsTable
{
    public static function get()
    {
        // $closures = ['evaluation_sheet_url' => (fn ($record) => $record->evaluation_sheet_url)];

        // dd($closures['evaluation_sheet_url']($record));

        return [
            // Tables\Columns\TextColumn::make('agreement_types')
            //     ->label('Agreement Type')
            //     ->searchable(false),
            // ->formatStateUsing(function ($state) {
            //     return implode(', ', $state);
            // }),
            Tables\Columns\ColumnGroup::make(__('Student information'))
                ->columns([
                    Tables\Columns\TextColumn::make('id_pfe')
                        ->searchable(
                            query: fn (Builder $query, string $search): Builder => $query
                                ->whereHas('agreements', function (Builder $query) use ($search) {
                                    $query->whereMorphRelation(
                                        'agreeable',
                                        [InternshipAgreement::class, FinalYearInternshipAgreement::class],
                                        function (Builder $query) use ($search) {
                                            $query->whereHas('student', function (Builder $query) use ($search) {
                                                $query->where('id_pfe', 'like', "%{$search}%")
                                                    ->orWhere('first_name', 'like', "%{$search}%")
                                                    ->orWhere('last_name', 'like', "%{$search}%");
                                            });
                                        }
                                    );
                                })
                        )
                        ->wrap()
                        ->label('ID PFE')
                        ->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('agreements.agreeable.student.full_name')
                        ->label('Student name')
                        ->searchable(false)
                        ->sortable(false)
                        ->description(fn ($record) => $record->id_pfe),
                    Tables\Columns\TextColumn::make('agreements.agreeable.student.email')
                        ->label('Student emails')
                        ->copyable()
                        ->toggleable()
                        ->searchable(false)
                        ->sortable(false)
                        ->description(fn ($record) => $record->final_internship_agreements->first()->student->email_perso),
                    Tables\Columns\TextColumn::make('agreements.agreeable.student.phone')
                        ->label('Student phone')
                        ->copyable()
                        ->toggleable()
                        ->searchable(false)
                        ->sortable(false),
                    Tables\Columns\TextColumn::make('agreements.agreeable.student.program')
                        ->toggleable(isToggledHiddenByDefault: false)
                        ->label('Program')
                        ->searchable(false)
                        ->sortable(query: function (Builder $query, string $direction) {
                            return $query->orderBy(
                                ProjectAgreement::query()
                                    ->select('students.program')
                                    ->join('final_year_internship_agreements', 'project_agreements.agreeable_id', '=', 'final_year_internship_agreements.id')
                                    ->join('students', 'final_year_internship_agreements.student_id', '=', 'students.id')
                                    ->whereColumn('project_agreements.project_id', 'projects.id')
                                    ->where('project_agreements.agreeable_type', FinalYearInternshipAgreement::class)
                                    ->limit(1),
                                $direction
                            );
                        })
                        ->badge(),
                    // Tables\Columns\TextColumn::make('internship_agreements.assigned_department')
                    //     ->label('Assigned department')
                    //     // ->sortable(false)
                    //     ->sortableMany()
                    //     ->searchable(),
                    Tables\Columns\TextColumn::make('title')
                        ->markdown()
                        ->label('Title')
                        ->html() // Add this line to enable HTML rendering
                        ->searchable(true)
                        ->sortable(false)
                        ->description(fn ($record) => __('From') . ' ' . $record->start_date->format('d/m/Y') . ' ' . __('to') . ' ' . $record->end_date->format('d/m/Y'))
                        // ->limit(90)
                        ->extraAttributes([
                            'class' => 'text-truncate text-break text-wrap max-width-20',
                            'style' => 'text-color: red',
                        ])
                        ->formatStateUsing(function ($record) {
                            $agreementTitle = $record->final_internship_agreements->first()?->title ?? '';
                            $projectTitle = $record->title ?? '';
                            $matches = trim(strtolower($agreementTitle)) === trim(strtolower($projectTitle));

                            if ($matches) {
                                return "{$projectTitle}";
                            }

                            return "{$projectTitle}<br>" .
                                   '<p class="text-warning-400" ><strong>' . __('Agreement title') . ":</strong> {$agreementTitle}</p>";
                        }),
                    Tables\Columns\TextColumn::make('assigned_departments')
                        ->toggleable(isToggledHiddenByDefault: true)
                        // ->formatStateUsing(fn ($record) => $record->assigned_departments)
                        ->label('Assigned department')
                        ->searchable(false)
                        ->sortable(false),
                ]),

            Tables\Columns\ColumnGroup::make(__('Defense authorization'))
                ->columns([
                    Tables\Columns\TextColumn::make('defense_status')
                        ->label('Status')
                        ->searchable(false)
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->badge(),
                    Tables\Columns\TextColumn::make('organization_evaluation_sheet_url')
                        // ->disabled(true)
                        ->toggleable(isToggledHiddenByDefault: true)
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
                        ->toggleable(isToggledHiddenByDefault: true)
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
                    // Tables\Columns\TextColumn::make('defense_authorized_by_user.name')
                    //     ->toggleable(isToggledHiddenByDefault: true)
                    //     ->label('Authorized by')
                    //     ->badge(),
                ]),

            Tables\Columns\ColumnGroup::make(__('Supervision & Defense'))
                ->columns([
                    // Tables\Columns\TextColumn::make('supervisor.name')
                    //     ->label('Supervisor')
                    //     ->searchable(
                    //         ['first_name', 'last_name']
                    //     ),
                    Tables\Columns\TextColumn::make('externalSupervisor.full_name')
                        ->label('External Supervisor')
                        ->limit(30)
                        ->searchable(false)
                        ->sortable(false),
                    Tables\Columns\TextColumn::make('external_supervisor_contact')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Contacts External Supervisor')
                        ->searchable(false)
                        ->sortable(false),
                    Tables\Columns\TextColumn::make('academic_supervisor_name')
                        ->label('Academic Supervisor')
                        // ->searchable(
                        //     ['first_name', 'last_name']
                        // )
                        ->sortable(false)
                        ->searchable(false),
                    Tables\Columns\TextColumn::make('reviewers.name')
                        ->label('Reviewers')
                        ->searchable(false)
                        ->sortable(false),
                    Tables\Columns\TextColumn::make('professors')
                        ->label('Assigned by')
                        ->searchable(false)
                        ->sortable(false)
                        ->formatStateUsing(function ($record) {
                            return $record->professors
                                ->map(function ($professor) {
                                    return $professor->pivot->createdBy->name; // change this to any pivot attribute you wish to show
                                })
                                ->implode(', ');
                        })
                        ->badge()
                        ->visible(fn ($record) => auth()->user()->isAdministrator()),

                    Tables\Columns\TextColumn::make('language')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Detected language')
                        ->searchable(false)
                        ->sortable(),
                    Tables\Columns\TextColumn::make('defense_plan')
                        ->label('Defense plan')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->sortable(false)
                        ->searchable(false),
                    Tables\Columns\TextColumn::make('timetable.timeslot.start_time')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Defense start date')
                        ->searchable(false)
                        ->sortable(true)
                        //->dateTime('d M Y H:i')
                        ->formatStateUsing(function ($record) {
                        return $record->timetable->timeslot->start_time ? Carbon::parse($record->timetable->timeslot->start_time)->format('d/m/Y H:i') : __('not defined');
                        }),
                    Tables\Columns\TextColumn::make('timetable.timeslot.end_time')
                        ->label('Defense end date')
                        ->searchable(false)
                        ->sortable(false)
                        ->toggleable(isToggledHiddenByDefault: true)
                        //->dateTime('d M Y H:i')
                        ->formatStateUsing(function ($record) {
                        return $record->timetable->timeslot->end_time ? Carbon::parse($record->timetable->timeslot->end_time)->format('d/m/Y H:i') : __('not defined');
                        }),
                    // Tables\Columns\TextColumn::make('defense_start_time')
                    //     ->toggleable(isToggledHiddenByDefault: true)
                    //     ->label('Defense start time')
                    //     ->searchable(false)
                    //     ->sortable(false)
                    //     ->formatStateUsing(function ($record) {
                    //         return $record->timetable->timeslot->start_time ? Carbon::parse($record->timetable->timeslot->start_time)->format('H:i') : __('not defined');
                    //     }),
                    // Tables\Columns\TextColumn::make('defense_end_time')
                    //     ->label('Defense end time')
                    //     ->searchable(false)
                    //     ->sortable(false)
                    //     ->toggleable(isToggledHiddenByDefault: true)
                    //     ->formatStateUsing(function ($record) {
                    //         return $record->timetable->timeslot->end_time ? Carbon::parse($record->timetable->timeslot->end_time)->format('H:i') : __('not defined');
                    //     }),
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
                    Tables\Columns\TextColumn::make('organization.name')
                        ->label('Organization')
                        ->searchable(false)
                        ->sortable(false)
                        ->description(fn ($record) => $record->organization->city . ', ' . $record->organization->country)
                        ->tooltip(fn ($record) => __('Organization Representative') . ': ' . $record->parrain->full_name),
                    // Tables\Columns\TextColumn::make('organization.address')
                    //     ->toggleable(isToggledHiddenByDefault: true)
                    //     ->label('Address')
                    //     ->searchable(false)
                    //     ->sortable(false),
                    Tables\Columns\TextColumn::make('parrain.full_name')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Le Parrain')
                        ->searchable(false)
                        ->sortable(false),
                ]),

            Tables\Columns\TextColumn::make('start_date')
                ->label('Start Date')
                ->date('d/m/Y')
                ->formatStateUsing(function ($record) {
                    return $record->start_date ? Carbon::parse($record->start_date)->format('d/m/Y') : 'not defined';
                })
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('end_date')
                ->label('End Date')
                ->date('d/m/Y')
                ->formatStateUsing(function ($record) {
                    return $record->end_date ? Carbon::parse($record->end_date)->format('d/m/Y') : 'not defined';
                })
                ->toggleable(isToggledHiddenByDefault: true),

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
