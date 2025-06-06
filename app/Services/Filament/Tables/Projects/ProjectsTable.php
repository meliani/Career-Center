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
use App\Settings\DisplaySettings;
class ProjectsTable
{
    public static function get()
    {
        // $closures = ['evaluation_sheet_url' => (fn ($record) => $record->evaluation_sheet_url)];

        // dd($closures['evaluation_sheet_url']($record));

        return [
            // Primary columns in the requested order
            // Date de soutenance
            Tables\Columns\TextColumn::make('timetable.timeslot.defense_day')
                ->label('Defense Date')
                ->searchable(false)
                ->sortable(false)
                ->visible(fn (DisplaySettings $displaySettings) => $displaySettings->display_plannings || auth()->user()->isAdministrator()),
            
            // Heure de soutenance  
            Tables\Columns\TextColumn::make('timetable.timeslot.defense_time')
                ->label('Defense Time')
                ->searchable(false)
                ->sortable(false)
                ->visible(fn (DisplaySettings $displaySettings) => $displaySettings->display_plannings || auth()->user()->isAdministrator()),
            
            // Defense DateTime (sortable)
            Tables\Columns\TextColumn::make('timetable.timeslot.start_time')
                ->label('Defense Date/Time')
                ->searchable(false)
                ->sortable(true)
                ->dateTime('d/m/Y H:i')
                ->toggleable(isToggledHiddenByDefault: true)
                ->visible(fn (DisplaySettings $displaySettings) => $displaySettings->display_plannings || auth()->user()->isAdministrator()),
            
            // Salle
            Tables\Columns\TextColumn::make('timetable.room.name')
                ->label('Room')
                ->searchable(false)
                ->sortable(false)
                ->visible(fn (DisplaySettings $displaySettings) => $displaySettings->display_plannings || auth()->user()->isAdministrator()),
            
            // Defense plan
            Tables\Columns\TextColumn::make('defense_plan')
                ->label('Defense plan')
                ->sortable(false)
                ->visible(fn (DisplaySettings $displaySettings) => $displaySettings->display_plannings || auth()->user()->isAdministrator())
                ->searchable(false),
            
            // ID PFE
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
                ->label('PFE ID')
                ->sortable(query: function (Builder $query, string $direction) {
                    return $query->orderBy(
                        ProjectAgreement::query()
                            ->select('students.id_pfe')
                            ->join('final_year_internship_agreements', 'project_agreements.agreeable_id', '=', 'final_year_internship_agreements.id')
                            ->join('students', 'final_year_internship_agreements.student_id', '=', 'students.id')
                            ->whereColumn('project_agreements.project_id', 'projects.id')
                            ->where('project_agreements.agreeable_type', FinalYearInternshipAgreement::class)
                            ->limit(1),
                        $direction
                    );
                }),
            
            // Nom de l'étudiant
            Tables\Columns\TextColumn::make('agreements.agreeable.student.full_name')
                ->label('Student Name')
                ->searchable(false)
                ->sortable(false)
                ->description(fn ($record) => $record->id_pfe),
            
            // Filière
            Tables\Columns\TextColumn::make('agreements.agreeable.student.program')
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
            
            // Organisme d'accueil
            Tables\Columns\TextColumn::make('organization.name')
                ->label('Host Organization')
                ->searchable(false)
                ->sortable(false)
                ->description(fn ($record) => ($record->organization?->city ?? '') . ', ' . ($record->organization?->country ?? ''))
                ->tooltip(fn ($record) => __('Organization Representative') . ': ' . ($record->parrain?->full_name ?? '')),
            
            // Sujet de stage PFE
            Tables\Columns\TextColumn::make('title')
                ->label('Final Project Subject')
                ->searchable()
                ->limit(50)
                ->tooltip(fn ($record) => $record->title)
                ->description(fn ($record) => __('Start date') . ': ' . $record->start_date->format('d/m/Y') . ' - ' . __('End date') . ': ' . $record->end_date->format('d/m/Y')),
            
            // Encadrant interne
            Tables\Columns\TextColumn::make('academic_supervisor_name')
                ->label('Internal Supervisor')
                ->sortable(false)
                ->searchable(false),
            
            // Examinateurs
            Tables\Columns\TextColumn::make('reviewers.name')
                ->label('Reviewers')
                ->searchable(false)
                ->visible(fn (DisplaySettings $displaySettings) => $displaySettings->display_project_reviewers || auth()->user()->isAdministrator())
                ->sortable(false),
            
            // Encadrant externe
            Tables\Columns\TextColumn::make('externalSupervisor.full_name')
                ->label('External Supervisor')
                ->limit(30)
                ->searchable(false)
                ->sortable(false),

            // Additional columns (previously organized in groups)
            Tables\Columns\ColumnGroup::make(__('Additional Student Information'))
                ->columns([
                    Tables\Columns\TextColumn::make('agreements.agreeable.student.email')
                        ->label('Student emails')
                        ->copyable()
                        ->toggleable()
                        ->searchable(false)
                        ->sortable(false)
                        ->description(fn ($record) => $record->final_internship_agreements->first()?->student?->email_perso),
                    Tables\Columns\TextColumn::make('agreements.agreeable.student.phone')
                        ->label('Student phone')
                        ->copyable()
                        ->toggleable()
                        ->searchable(false)
                        ->sortable(false),
                    Tables\Columns\TextColumn::make('assigned_departments')
                        ->toggleable(isToggledHiddenByDefault: true)
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
                ]),

            Tables\Columns\ColumnGroup::make(__('Additional Defense Details'))
                ->columns([
                    Tables\Columns\TextColumn::make('external_supervisor_contact')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->label('Contacts External Supervisor')
                        ->searchable(false)
                        ->sortable(false),
                    Tables\Columns\TextColumn::make('professors')
                        ->label('Assigned by')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->searchable(false)
                        ->sortable(false)
                        ->formatStateUsing(function ($record) {
                            return $record->professors
                                ->map(function ($professor) {
                                    return $professor->pivot?->createdBy?->name ?? 'Unknown';
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
                ]),

            Tables\Columns\ColumnGroup::make(__('Enterprise Information'))
                ->columns([
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
                    return $record->start_date ? Carbon::parse($record->start_date)->format('d/m/Y') : __('not defined');
                })
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('end_date')
                ->label('End Date')
                ->date('d/m/Y')
                ->formatStateUsing(function ($record) {
                    return $record->end_date ? Carbon::parse($record->end_date)->format('d/m/Y') : __('not defined');
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
            Tables\Columns\TextColumn::make('writing_language')
                ->label('Writing Language')
                ->badge()
                ->formatStateUsing(fn ($state) => $state?->getLabel())
                ->color(fn ($state) => $state?->getColor())
                ->sortable(),
            Tables\Columns\TextColumn::make('presentation_language')
                ->label('Presentation Language')
                ->badge()
                ->formatStateUsing(fn ($state) => $state?->getLabel())
                ->color(fn ($state) => $state?->getColor())
                ->sortable(),
        ];
    }
}
