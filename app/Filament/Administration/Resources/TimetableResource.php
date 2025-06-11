<?php

namespace App\Filament\Administration\Resources;

use App\Enums\JuryRole;
use App\Filament\Administration\Resources\TimetableResource\Pages;
use App\Filament\Core\BaseResource;
use App\Models\Project;
use App\Models\Timetable;
use App\Models\Year;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TimetableResource extends BaseResource
{
    protected static ?string $model = Timetable::class;

    protected static ?string $title = 'Defenses Timetable';

    protected static ?string $modelLabel = 'Defense Timetable';

    protected static ?string $pluralModelLabel = 'Defenses Timetable';

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Defenses Timetable';

    protected static ?string $navigationGroup = 'Defense Management';

    // protected static ?string $navigationParentItem = 'Final Projects';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'project.final_internship_agreements.student',
                'project.final_internship_agreements.organization',
                'project.professors',
                'project.externalSupervisor',
                'timeslot',
                'room'
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('timeslot_id')
                    ->label('Timeslot')
                    ->relationship('timeslot', 'start_time'),
                // ->options(Timetable::unplanned()->with('timeslot')->get()->pluck('timeslot.start_time', 'timeslot_id')),
                Forms\Components\Select::make('room_id')
                    ->label('Room')
                    ->relationship('room', 'name'),
                Forms\Components\Select::make('project_id')
                    ->label('Project')
                    ->searchable()
                    // ->options(
                    //     Project::pluck('title', 'id')
                    // )
                    ->options(
                        Project::join('project_agreements', 'projects.id', '=', 'project_agreements.project_id')
                            ->join('final_year_internship_agreements', function($join) {
                                $join->on('project_agreements.agreeable_id', '=', 'final_year_internship_agreements.id')
                                     ->where('project_agreements.agreeable_type', '=', 'App\\Models\\FinalYearInternshipAgreement');
                            })
                            ->join('students', 'final_year_internship_agreements.student_id', '=', 'students.id')
                            ->select(DB::raw("CONCAT(COALESCE(students.name, 'Unknown Student'), ' - ', COALESCE(projects.title, 'Untitled Project')) AS student_project"), 'projects.id')
                            ->pluck('student_project', 'id')
                    ),
                // Forms\Components\TextInput::make('user_id')
                //     ->numeric(),
                // Forms\Components\Toggle::make('is_enabled'),
                // Forms\Components\Toggle::make('is_taken'),
                // Forms\Components\Toggle::make('is_confirmed'),
                // Forms\Components\Toggle::make('is_cancelled'),
                // Forms\Components\Toggle::make('is_rescheduled'),
                // Forms\Components\Toggle::make('is_deleted'),
                // Forms\Components\DateTimePicker::make('confirmed_at'),
                // Forms\Components\DateTimePicker::make('cancelled_at'),
                // Forms\Components\DateTimePicker::make('rescheduled_at'),
                // Forms\Components\TextInput::make('confirmed_by')
                //     ->numeric(),
                // Forms\Components\TextInput::make('cancelled_by')
                //     ->numeric(),
                // Forms\Components\TextInput::make('rescheduled_by')
                //     ->numeric(),
                // Forms\Components\TextInput::make('deleted_by')
                //     ->numeric(),
                // Forms\Components\TextInput::make('created_by')
                //     ->numeric(),
                // Forms\Components\TextInput::make('updated_by')
                //     ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup('timeslot.start_time')
            ->groups([
                Tables\Grouping\Group::make('timeslot.start_time')
                    ->date()
                    ->collapsible()
                    ->label(__('Defense Day')),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('defense_date')
                    ->label('Date de soutenance')
                    ->getStateUsing(fn ($record) => $record->timeslot?->defense_date ?? '-')
                    ->searchable(false)
                    ->sortable(false),
                Tables\Columns\TextColumn::make('defense_time')
                    ->label('Heure de soutenance')
                    ->getStateUsing(fn ($record) => $record->timeslot?->defense_time ?? '-')
                    ->searchable(false)
                    ->sortable(false),
                Tables\Columns\TextColumn::make('room_name')
                    ->label('Salle')
                    ->getStateUsing(fn ($record) => $record->room?->name ?? '-')
                    ->searchable(false)
                    ->sortable(false),
                // Calculable field: disable search/sort, enable sort/search via relationship below
                Tables\Columns\TextColumn::make('project.final_internship_agreements.student.id_pfe')
                    ->label('ID PFE')
                    ->sortable()
                    ->searchable(),
                // Calculable field: disable search/sort, enable sort/search via relationship below
                Tables\Columns\TextColumn::make('project.final_internship_agreements.student.name')
                    ->label('Nom de l\'étudiant')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('student_name')
                    ->label('Nom de l\'étudiant')
                    ->getStateUsing(function ($record) {
                        $students = $record->project?->final_internship_agreements?->pluck('student.name')?->filter();
                        return $students?->isNotEmpty() ? $students->implode(', ') : '-';
                    })
                    ->sortable(false)
                    ->searchable(false)
                    ->limit(30),
                Tables\Columns\TextColumn::make('student_program')
                    ->label('Filière')
                    ->getStateUsing(function ($record) {
                        $programs = $record->project?->final_internship_agreements?->map(function ($agreement) {
                            return $agreement->student?->program?->getLabel();
                        })?->filter()?->unique();
                        return $programs?->isNotEmpty() ? $programs->implode(', ') : '-';
                    })
                    ->badge()
                    ->sortable(false)
                    ->searchable(false),
                Tables\Columns\TextColumn::make('organization_name')
                    ->label('Organisme d\'accueil')
                    ->getStateUsing(function ($record) {
                        $organizations = $record->project?->final_internship_agreements?->pluck('organization.name')?->filter()?->unique();
                        return $organizations?->isNotEmpty() ? $organizations->implode(', ') : '-';
                    })
                    ->sortable(false)
                    ->searchable(false)
                    ->limit(40),
                Tables\Columns\TextColumn::make('project_title')
                    ->label('Sujet de stage PFE')
                    ->getStateUsing(fn ($record) => $record->project?->title ?? '-')
                    ->searchable(false)
                    ->sortable(false)
                    ->limit(50),
                Tables\Columns\TextColumn::make('academic_supervisor')
                    ->label('Encadrant interne')
                    ->getStateUsing(fn ($record) => $record->project?->professors?->filter(fn ($prof) => 
                        $prof->pivot->jury_role->value === JuryRole::Supervisor->value
                    )?->first()?->name ?? '-')
                    ->sortable(false)
                    ->searchable(false)
                    ->limit(30),
                Tables\Columns\TextColumn::make('reviewers')
                    ->label('Examinateurs')
                    ->getStateUsing(function ($record) {
                        $reviewers = $record->project?->professors?->filter(fn ($prof) => 
                            in_array($prof->pivot->jury_role->value, [JuryRole::FirstReviewer->value, JuryRole::SecondReviewer->value])
                        )?->pluck('name')?->implode(', ');
                        return $reviewers ?: '-';
                    })
                    ->sortable(false)
                    ->searchable(false)
                    ->limit(40),
                Tables\Columns\TextColumn::make('external_supervisor')
                    ->label('Encadrant externe')
                    ->getStateUsing(fn ($record) => $record->project?->externalSupervisor?->full_name ?? '-')
                    ->sortable(false)
                    ->searchable(false)
                    ->limit(30),

                // ...existing code...
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->label('Year')
                    ->options(Year::getYearsForSelect(1))
                    ->default(Year::current()->id)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $yearId): Builder => $query->whereHas('timeslot', function (Builder $q) use ($yearId) {
                                $q->where('year_id', $yearId);
                            })
                        );
                    })
                    ->indicateUsing(fn (array $data): ?string => $data['value'] ? __('Year') . ': ' . Year::find($data['value'])->title : null)
                    ->visible(fn () => auth()->user()->isAdministrator() === true),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTimetables::route('/'),
            'create' => Pages\CreateTimetable::route('/create'),
            'view' => Pages\ViewTimetable::route('/{record}'),
            'edit' => Pages\EditTimetable::route('/{record}/edit'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('Defense information'))
                    // ->hidden(fn () => auth()->user()->isAdministrator() === false)
                    ->columns(3)
                    ->schema([
                        Infolists\Components\TextEntry::make('timeslot.start_time')
                            ->label('Defense start time')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('timeslot.end_time')
                            ->label('Defense end time')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('room.name')
                            ->label('Room'),
                        Infolists\Components\Fieldset::make('Jury')
                            ->schema([
                                // RepeatableEntry::make('supervisor')
                                //     ->label('')
                                //     ->schema([
                                //         Infolists\Components\TextEntry::make('name')
                                //             ->label(__('Supervisor')),
                                //     ])
                                //     ->contained(false),
                                // RepeatableEntry::make('reviewers')
                                //     ->label('')
                                //     ->schema([
                                //         Infolists\Components\TextEntry::make('name')
                                //             ->label(__('Reviewer')),
                                //     ])
                                //     ->contained(false),
                                Infolists\Components\TextEntry::make('project.professors.name')
                                    ->label('')
                                    ->formatStateUsing(
                                        fn ($record) => $record->project->professors->map(
                                            fn ($professor) => $professor->pivot->jury_role->getLabel() . ': ' . $professor->name
                                        )->join(', ')
                                    ),
                            ]),
                    ]),

                Infolists\Components\Section::make(__('Project information'))
                    ->headerActions([
                        // CommentsAction::make(),
                    ])
                    ->columns(3)
                    ->schema([
                        Infolists\Components\TextEntry::make('project.final_internship_agreements.student.id_pfe')
                            ->label('PFE ID')
                            ->formatStateUsing(function ($record) {
                                $ids = $record->project?->final_internship_agreements?->pluck('student.id_pfe')?->filter();
                                return $ids?->isNotEmpty() ? $ids->implode(', ') : '-';
                            }),
                        Infolists\Components\TextEntry::make('project.final_internship_agreements.student.name')
                            ->label('Student')
                            ->formatStateUsing(function ($record) {
                                $students = $record->project?->final_internship_agreements?->pluck('student.name')?->filter();
                                return $students?->isNotEmpty() ? $students->implode(', ') : '-';
                            }),
                        Infolists\Components\TextEntry::make('project.final_internship_agreements.student.program')
                            ->label('Program')
                            ->formatStateUsing(function ($record) {
                                $programs = $record->project?->final_internship_agreements?->map(function ($agreement) {
                                    return $agreement->student?->program?->getLabel();
                                })?->filter()?->unique();
                                return $programs?->isNotEmpty() ? $programs->implode(', ') : '-';
                            }),
                        Infolists\Components\TextEntry::make('project.final_internship_agreements.organization.name')
                            ->label('Organization')
                            ->formatStateUsing(function ($record) {
                                $organizations = $record->project?->final_internship_agreements?->map(function ($agreement) {
                                    $org = $agreement->organization;
                                    if ($org) {
                                        $info = $org->name;
                                        if ($org->city) $info .= " ({$org->city})";
                                        if ($org->website) $info .= " - {$org->website}";
                                        return $info;
                                    }
                                    return null;
                                })?->filter()?->unique();
                                return $organizations?->isNotEmpty() ? $organizations->implode(', ') : '-';
                            }),

                        Infolists\Components\TextEntry::make('project.title')
                            ->label('Project title'),

                        Infolists\Components\TextEntry::make('project.description')
                            ->label('Project description')
                            ->formatStateUsing(
                                fn ($record) => "{$record->description}"
                            )
                            ->html()
                            ->columnSpanFull(),
                        Infolists\Components\Fieldset::make('Dates')
                            ->schema([
                                Infolists\Components\TextEntry::make('project.start_date')
                                    ->label('Project start date')
                                    ->date(),
                                Infolists\Components\TextEntry::make('project.end_date')
                                    ->label('Project end date')
                                    ->date(),
                            ]),

                        Infolists\Components\Fieldset::make('Entreprise supervisor')
                            ->schema([
                                Infolists\Components\TextEntry::make('project.final_year_internship_agreements.encadrant_ext_nom')
                                    ->label('')
                                    ->formatStateUsing(
                                        fn ($record) => $record->project->final_year_internship_agreements->map(
                                            fn ($internship_agreement) => "**{$internship_agreement->encadrant_ext_name}**" . PHP_EOL . $internship_agreement->encadrant_ext_mail . PHP_EOL . $internship_agreement->encadrant_ext_tel
                                        )->join(', ')
                                    )
                                    ->markdown(),

                            ]),
                        // CommentsEntry::make('Comments')
                        //     ->columnSpanFull(),
                    ]),

            ]);

    }
}
