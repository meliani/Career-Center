<?php

namespace App\Filament\Administration\Resources;

use App\Enums;
use App\Filament\Actions\BulkAction;
use App\Filament\Administration\Resources\StudentResource\Pages;
use App\Filament\Core;
use App\Mail;
use App\Models\Student;
use Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades;

class StudentResource extends Core\BaseResource
{
    protected static ?string $modelLabel = 'Student';

    protected static ?string $pluralModelLabel = 'Students';

    protected static ?string $model = Student::class;

    protected static ?string $title = 'Manage Students';

    protected static ?string $recordTitleAttribute = 'long_full_name';

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?int $sort = 3;

    protected static ?string $recordFirstNameAttribute = 'first_name';

    protected static ?string $navigationGroup = 'Students and projects';

    public static $User;

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

    public static function getnavigationGroup(): string
    {
        return __(self::$navigationGroup);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'first_name',
            'last_name',
            'program',
            'active_internship_agreement.id_pfe',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('title')
                    ->options(Enums\Title::class)
                    ->required(),
                Forms\Components\TextInput::make('first_name')
                    ->maxLength(191),
                Forms\Components\TextInput::make('last_name')
                    ->maxLength(191),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email_perso')
                    ->email()
                    ->maxLength(191),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(191),
                Forms\Components\TextInput::make('cv')
                    ->maxLength(191),
                Forms\Components\TextInput::make('lm')
                    ->maxLength(191),
                Forms\Components\TextInput::make('photo')
                    ->maxLength(191),
                Forms\Components\DatePicker::make('birth_date'),
                Forms\Components\Select::make('level')
                    ->options(Enums\StudentLevel::class)
                    ->required(),
                Forms\Components\Select::make('program')
                    ->options(Enums\Program::class)
                    ->required(),
                Forms\Components\Toggle::make('is_mobility'),
                Forms\Components\TextInput::make('abroad_school')
                    ->maxLength(191),
                Forms\Components\TextInput::make('pin')
                    ->numeric(),
                Forms\Components\Select::make('year_id')
                    ->label('Academic year')
                    ->relationship('year', 'title')
                    ->required(),
                Forms\Components\Toggle::make('is_active'),
                Forms\Components\DatePicker::make('graduated_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        self::$User = auth()->user();

        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('title')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('pin')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\ToggleColumn::make('is_verified'),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('name')
                    ->formatStateUsing(function ($record) {
                        return $record->long_full_name;
                    })
                    ->wrap(),
                Tables\Columns\TextColumn::make('email')
                    ->formatStateUsing(function ($record) {
                        return "{$record->email}, {$record->email_perso}";
                    })
                    ->wrap(),
                Tables\Columns\TextColumn::make('program')
                    // ->formatStateUsing(fn ($record) => $record->level->getLabel() . ',' . $record->program->getLabel())
                    ->tooltip(fn ($record) => $record->level->getLabel() . ',' . $record->program?->getDescription())
                    ->badge(),
                Tables\Columns\TextColumn::make('level')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->badge(),
                // Tables\Columns\TextColumn::make('email_perso')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\TextColumn::make('cv')
                    ->label('Curriculum vitae')
                    ->limit(20)
                    ->url(fn (Student $record): ?string => $record?->cv, true),
                Tables\Columns\TextColumn::make('lm')
                    ->label('Cover letter')
                    ->url(fn (Student $record): ?string => $record?->lm, true)
                    ->limit(20),
                Tables\Columns\TextColumn::make('photo')
                    ->url(fn (Student $record): ?string => $record?->photo, true)
                    ->limit(20),
                Tables\Columns\TextColumn::make('birth_date')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->date(),

                Tables\Columns\ToggleColumn::make('is_mobility')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('abroad_school')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('year.title')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('graduated_at')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->date(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('level')
                    ->options(Enums\StudentLevel::class)
                    ->label('Level')
                    ->default(Enums\StudentLevel::ThirdYear->value)
                    ->placeholder('All levels'),
                Tables\Filters\SelectFilter::make('program')
                    ->options(Enums\Program::class)
                    ->label('Program')
                    ->placeholder('All programs'),
            ])
            ->actions([
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

                BulkAction\Email\SendGenericEmail::make('Send Email')
                    ->outlined()
                    ->label(__('Send email')),
                Tables\Actions\BulkActionGroup::make([
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ])->hidden(fn () => auth()->user()->isAdministrator() === false)
                    ->label(__('actions')),
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
                Infolists\Components\Section::make(__('Student information'))
                    ->schema([
                        Infolists\Components\Fieldset::make(__('Internship agreement'))
                            ->schema([
                                Infolists\Components\TextEntry::make('long_full_name')
                                    ->label('Full name'),
                                Infolists\Components\TextEntry::make('email')
                                    ->label('Email'),
                                Infolists\Components\TextEntry::make('phone')
                                    ->label('Phone'),
                                Infolists\Components\TextEntry::make('program')
                                    ->label('Program'),
                            ]),
                        Infolists\Components\Fieldset::make(__('Student documents'))
                            ->schema([
                                Infolists\Components\TextEntry::make('cv')
                                    ->url(fn (Student $record): ?string => $record?->cv, true)
                                    ->label('Curriculum vitae'),
                                Infolists\Components\TextEntry::make('lm')
                                    ->url(fn (Student $record): ?string => $record?->lm, true)
                                    ->label('Cover letter'),
                                Infolists\Components\TextEntry::make('photo')
                                    ->url(fn (Student $record): ?string => $record?->photo, true)
                                    ->label('Photo'),
                            ]),
                    ]),
            ]);

    }
}
