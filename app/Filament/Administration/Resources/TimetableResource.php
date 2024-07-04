<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Administration\Resources\TimetableResource\Pages;
use App\Filament\Core\BaseResource;
use App\Models\Project;
use App\Models\Timetable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class TimetableResource extends BaseResource
{
    protected static ?string $model = Timetable::class;

    protected static ?string $title = 'Defenses Timetable';

    protected static ?string $modelLabel = 'Defense Timetable';

    protected static ?string $pluralModelLabel = 'Defenses Timetable';

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationGroup = 'Planning';

    protected static ?string $navigationLabel = 'Defenses Timetable';

    public static function getNavigationLabel(): string
    {
        return __(self::$navigationLabel);
    }

    public static function getnavigationGroup(): string
    {
        return __(self::$navigationGroup);
    }

    public static function canViewAny(): bool
    {
        if (auth()->check()) {
            return auth()->user()->isAdministrator();
        }

        return false;
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
                        Project::join('internships', 'projects.id', '=', 'internships.project_id')
                            ->join('students', 'internships.student_id', '=', 'students.id')
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
                    ->label(__('Day of')),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('timeslot.start_time')
                    ->sortable(),
                Tables\Columns\TextColumn::make('room.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('project.title')
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('project.internship_agreements.student.name')
                    ->sortable()
                    ->limit(50)
                    ->label('Student'),
                Tables\Columns\TextColumn::make('project.internship_agreements.id_pfe')
                    ->sortable()
                    ->limit(50)
                    ->label('ID PFE'),

                // Tables\Columns\TextColumn::make('user_id')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\IconColumn::make('is_enabled')
                //     ->boolean(),
                // Tables\Columns\IconColumn::make('is_taken')
                //     ->boolean(),
                // Tables\Columns\IconColumn::make('is_confirmed')
                //     ->boolean(),
                // Tables\Columns\IconColumn::make('is_cancelled')
                //     ->boolean(),
                // Tables\Columns\IconColumn::make('is_rescheduled')
                //     ->boolean(),
                // Tables\Columns\IconColumn::make('is_deleted')
                //     ->boolean(),
                // Tables\Columns\TextColumn::make('confirmed_at')
                //     ->dateTime()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('cancelled_at')
                //     ->dateTime()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('rescheduled_at')
                //     ->dateTime()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('deleted_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('confirmed_by')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('cancelled_by')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('rescheduled_by')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('deleted_by')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('created_by')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('updated_by')
                //     ->numeric()
                //     ->sortable(),
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
                //
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
                        Infolists\Components\TextEntry::make('project.id_pfe')
                            ->label('PFE ID'),
                        Infolists\Components\TextEntry::make('project.students.long_full_name')
                            ->label('Student'),
                        Infolists\Components\TextEntry::make('project.students.program')
                            ->label('Program'),
                        Infolists\Components\TextEntry::make('project.organization')
                            ->label('Organization'),

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
                                Infolists\Components\TextEntry::make('project.internship_agreements.encadrant_ext_nom')
                                    ->label('')
                                    ->formatStateUsing(
                                        fn ($record) => $record->project->internship_agreements->map(
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
