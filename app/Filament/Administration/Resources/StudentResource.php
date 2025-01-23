<?php

namespace App\Filament\Administration\Resources;

use App\Enums;
use App\Filament\Actions\BulkAction;
use App\Filament\Administration\Resources\StudentResource\Pages;
use App\Filament\Core;
use App\Mail;
use App\Models\Student;
use App\Models\Year;
use App\Notifications\CollaborationReminderNotification;
use Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades;

class StudentResource extends Core\BaseResource
{
    protected static ?string $modelLabel = 'Student';

    protected static ?string $pluralModelLabel = 'Students';

    protected static ?string $model = Student::class;

    protected static ?string $title = 'Manage Students';

    protected static ?string $recordTitleAttribute = 'long_full_name';

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $recordFirstNameAttribute = 'first_name';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 3;

    public static $User;

    public static function getPolicy(): string
    {
        return \App\Policies\YourModelPolicy::class;
    }

    public static function canAccess(): bool
    {
        return auth()->user()->isAdministrator() || auth()->user()->isAdministrativeSupervisor();
    }

    public static function canViewAny(): bool
    {
        if (auth()->check()) {
            return auth()->user()->isAdministrator() || auth()->user()->isDirection();
        }

        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('level', 'ThirdYear')->count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'first_name',
            'last_name',
            'program',
            'id_pfe',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->description('Student personal information')
                    ->icon('heroicon-o-user')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('title')
                            ->options(Enums\Title::class)
                            ->required(),
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->columnSpan(2)
                            ->maxLength(191),
                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->columnSpan(2)
                            ->maxLength(191),
                    ]),

                Forms\Components\Section::make('Contact Details')
                    ->description('Student contact information')
                    ->icon('heroicon-o-envelope')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email_perso')
                            ->label('Personal Email')
                            ->email()
                            ->maxLength(191),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(191),
                    ]),

                Forms\Components\Section::make('Academic Information')
                    ->description('Academic status and program details')
                    ->icon('heroicon-o-academic-cap')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('level')
                            ->options(Enums\StudentLevel::class)
                            ->required(),
                        Forms\Components\Select::make('program')
                            ->options(Enums\Program::class),
                        Forms\Components\Select::make('year_id')
                            ->label('Academic year')
                            ->relationship('year', 'title')
                            ->required(),
                        Forms\Components\TextInput::make('id_pfe')
                            ->numeric(),
                        Forms\Components\DatePicker::make('birth_date')
                            ->native(false),
                    ]),

                Forms\Components\Section::make('Documents')
                    ->description('Student documents and files')
                    ->icon('heroicon-o-document')
                    ->columns(3)
                    ->collapsed()
                    ->schema([
                        Forms\Components\FileUpload::make('cv')
                            ->label('CV')
                            ->directory('students/cv')
                            ->acceptedFileTypes(['application/pdf']),
                        Forms\Components\FileUpload::make('lm')
                            ->label('Cover Letter')
                            ->directory('students/lm')
                            ->acceptedFileTypes(['application/pdf']),
                        Forms\Components\FileUpload::make('photo')
                            ->image()
                            ->directory('students/photos'),
                    ]),

                Forms\Components\Section::make('Additional Information')
                    ->description('Mobility and status information')
                    ->icon('heroicon-o-information-circle')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->inline(false),
                        Forms\Components\DatePicker::make('graduated_at')
                            ->native(false),
                    ]),

                Forms\Components\Section::make('Mobility Information')
                    ->description('Student exchange details')
                    ->icon('heroicon-o-globe-alt')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        Forms\Components\Toggle::make('is_mobility')
                            ->label('Exchange Student')
                            ->inline(false)
                            ->reactive(),
                        Forms\Components\Select::make('exchange_type')
                            ->options(Enums\ExchangeType::class)
                            ->visible(fn (Forms\Get $get) => $get('is_mobility'))
                            ->required(fn (Forms\Get $get) => $get('is_mobility')),
                        Forms\Components\Select::make('student_exchange_partner_id')
                            ->required(fn (Forms\Get $get) => $get('is_mobility'))
                            ->label('Exchange Partner')
                            ->relationship('exchangePartner', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                Forms\Components\TextInput::make('country'),
                                Forms\Components\TextInput::make('city'),
                                Forms\Components\TextInput::make('website')
                                    ->url(),
                                Forms\Components\TextInput::make('email')
                                    ->email(),
                                Forms\Components\TextInput::make('phone_number'),
                            ])
                            ->visible(fn (Forms\Get $get) => $get('is_mobility')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        self::$User = auth()->user();

        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->full_name))
                    ->size(40),

                Tables\Columns\TextColumn::make('id_pfe')
                    ->label('ID')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Student')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable()
                    ->description(fn ($record) => $record->email)
                    ->view('filament.tables.columns.name-with-id'),

                Tables\Columns\TextColumn::make('contact_info')
                    ->label('Contact')
                    ->view('filament.tables.columns.contact-info')
                    ->searchable(false),

                Tables\Columns\TextColumn::make('academic_info')
                    ->label('Academic Info')
                    ->view('filament.tables.columns.academic-info')
                    ->searchable(false),

                Tables\Columns\ViewColumn::make('documents')
                    ->label('Documents')
                    ->view('filament.tables.columns.student-documents')
                    ->searchable(false),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Status')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('student_exchange_info')
                    ->label('Exchange Information')
                    ->view('filament.tables.columns.exchange-info')
                    ->searchable(false),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year_id')
                    ->options(Year::getYearsForSelect(2))
                    ->label(__('Academic year'))
                    ->default(Year::current()->id)
                    ->placeholder(__('All years')),
                Tables\Filters\SelectFilter::make('level')
                    ->options(Enums\StudentLevel::class)
                    ->label(__('Level'))
                    ->default(Enums\StudentLevel::ThirdYear->value)
                    ->placeholder(__('All levels')),
                Tables\Filters\SelectFilter::make('program')
                    ->options(Enums\Program::class)
                    ->label(__('Program'))
                    ->placeholder(__('All programs')),
                Tables\Filters\TrashedFilter::make()
                    ->visible(fn () => auth()->user()->isAdministrator()),

                Tables\Filters\SelectFilter::make('student_exchange_partner')
                    ->relationship('exchangePartner', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Exchange Partner')
                    ->visible(fn () => auth()->user()->isAdministrator()),

                Tables\Filters\Filter::make('is_mobility')
                    ->label('Exchange Students Only')
                    ->toggle()
                    ->query(fn (Builder $query) => $query->where('is_mobility', true)),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->actions([
                Tables\Actions\RestoreAction::make()
                    ->hidden(fn () => auth()->user()->isAdministrator() === false),

                \STS\FilamentImpersonate\Tables\Actions\Impersonate::make()
                    ->hidden(fn ($record) => ! $record->canBeImpersonated())
                    ->guard('students'),
                Tables\Actions\Action::make('sendEmail')
                    ->form([
                        Forms\Components\TextInput::make('subject')->required(),
                        Forms\Components\RichEditor::make('body')->required(),
                    ])
                    ->action(
                        fn (array $data, Student $student) => Facades\Mail::to([$student?->email_perso, $student?->email])
                            ->send(
                                new Mail\GenericEmail(
                                    self::$User,
                                    $data['subject'],
                                    $data['body'],
                                ),
                                Notification::make()
                                    ->title(__('Email sent to :email and :email_perso', ['email' => $student?->email, 'email_perso' => $student?->email_perso]))
                                    ->send()
                            )
                        // ->notification(__('Email sent to :email', ['email' => $student?->email_perso]))
                    )
                    ->label('')
                    ->icon('heroicon-o-envelope')
                    ->size(Filament\Support\Enums\ActionSize::ExtraLarge)
                    ->tooltip(__('Send an email to student')),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()->hidden(fn () => auth()->user()->isAdministrator() === false),
                ])->hidden(fn () => auth()->user()->isAdministrator() === false),
            ], position: Tables\Enums\ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\RestoreBulkAction::make()
                    ->hidden(fn () => auth()->user()->isAdministrator() === false),

                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ])->hidden(fn () => auth()->user()->isAdministrator() === false)
                    ->label(false)
                    ->icon('heroicon-o-ellipsis-horizontal-circle'),

                BulkAction\Email\SendGenericEmail::make('Send Email')
                    ->outlined()
                    ->label(__('Send email')),

                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('PassToNextLevel')
                        ->label('Pass to next level')
                        ->icon('heroicon-o-arrow-right-circle')
                        ->color('primary')
                        // ->disabled(fn ($records) => $records->contains(fn ($record) => $record->level->is(Enums\StudentLevel::FifthYear)))
                        ->form([
                            Forms\Components\ToggleButtons::make('level')
                                ->options(Enums\StudentLevel::class)
                                ->required(),
                        ])
                        ->action(fn ($records) => $records->each->passToNextLevel()),
                    Tables\Actions\BulkAction::make('ChangeAcademicYear')
                        ->label('Change academic year')
                        ->icon('heroicon-o-calendar')
                        ->color('primary')
                        ->form([
                            Forms\Components\Select::make('year_id')
                                ->options(\App\Models\Year::getYearsForSelect(2))
                                ->default(\App\Models\Year::current()->id)
                                ->searchable()
                                ->required(),
                        ])
                        ->action(fn ($records) => $records->each->changeAcademicYear(request('year_id'))),
                ])
                    ->outlined()
                    ->icon('heroicon-o-ellipsis-horizontal-circle')
                    ->color('primary')
                    ->size(Filament\Support\Enums\ActionSize::Small)
                    ->label(__('Mass prossessing'))
                    ->hidden(fn () => auth()->user()->cannot('manage-students')),
                \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make()
                    ->label('Export to Excel')
                    ->icon('heroicon-o-document')
                    ->color('primary')
                    ->hidden(fn () => auth()->user()->cannot('manage-students')),
                Tables\Actions\BulkAction::make('sendCollaborationReminder')
                    ->label(__('Send Collaboration Reminder'))
                    ->action(function (Collection $records) {
                        foreach ($records as $record) {
                            $record->notify(new CollaborationReminderNotification);
                        }
                    })
                    ->requiresConfirmation(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'view' => Pages\ViewStudent::route('/{record}'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('Personal Information'))
                    ->icon('heroicon-o-user')
                    ->columns(3)
                    ->schema([
                        Infolists\Components\ImageEntry::make('avatar_url')
                            ->label('Photo')
                            ->circular()
                            ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->full_name))
                            ->columnSpan(1),

                        Infolists\Components\Grid::make(2)
                            ->columnSpan(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('full_name')
                                    ->label('Name')
                                    ->weight('bold'),
                                Infolists\Components\TextEntry::make('id_pfe')
                                    ->label('ID')
                                    ->badge(),
                                Infolists\Components\TextEntry::make('email')
                                    ->icon('heroicon-m-envelope'),
                                Infolists\Components\TextEntry::make('phone')
                                    ->icon('heroicon-m-phone'),
                            ]),
                    ]),

                Infolists\Components\Section::make(__('Academic Information'))
                    ->icon('heroicon-o-academic-cap')
                    ->columns(3)
                    ->schema([
                        Infolists\Components\TextEntry::make('level')
                            ->badge()
                            ->color('primary'),
                        Infolists\Components\TextEntry::make('program')
                            ->badge()
                            ->color('success'),
                        Infolists\Components\TextEntry::make('year.title')
                            ->badge()
                            ->color('info'),
                        Infolists\Components\Grid::make(2)
                            ->columnSpan(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('is_active')
                                    ->label('Active Status')
                                    ->badge()
                                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                                Infolists\Components\TextEntry::make('graduated_at')
                                    ->label('Graduation Date')
                                    ->date(),
                            ]),
                    ]),

                Infolists\Components\Section::make(__('Documents'))
                    ->icon('heroicon-o-document')
                    ->columns(3)
                    ->schema([
                        Infolists\Components\TextEntry::make('cv')
                            ->label('CV')
                            ->url(fn ($record) => $record->cv, true)
                            ->hidden(fn ($record) => empty($record->cv))
                            ->badge()
                            ->color('success')
                            ->icon('heroicon-m-document'),
                        Infolists\Components\TextEntry::make('lm')
                            ->label('Cover Letter')
                            ->url(fn ($record) => $record->lm, true)
                            ->hidden(fn ($record) => empty($record->lm))
                            ->badge()
                            ->color('success')
                            ->icon('heroicon-m-document'),
                        Infolists\Components\TextEntry::make('photo')
                            ->label('Photo')
                            ->url(fn ($record) => $record->photo, true)
                            ->hidden(fn ($record) => empty($record->photo))
                            ->badge()
                            ->color('success')
                            ->icon('heroicon-m-photo'),
                    ]),

                Infolists\Components\Section::make('Exchange Information')
                    ->icon('heroicon-o-globe-alt')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('exchangePartner.name')
                            ->label('Partner Institution')
                            ->visible(fn ($record) => $record->is_mobility),
                        Infolists\Components\TextEntry::make('exchangePartner.full_address')
                            ->label('Location')
                            ->visible(fn ($record) => $record->is_mobility),
                        Infolists\Components\TextEntry::make('exchangePartner.website')
                            ->label('Website')
                            ->url(fn ($record) => $record->exchangePartner?->website)
                            ->visible(fn ($record) => $record->is_mobility),
                        Infolists\Components\TextEntry::make('exchangePartner.email')
                            ->label('Contact Email')
                            ->visible(fn ($record) => $record->is_mobility),
                    ])
                    ->visible(fn ($record) => $record->is_mobility),
            ]);
    }
}
