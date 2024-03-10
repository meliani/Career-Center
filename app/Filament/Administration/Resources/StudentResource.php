<?php

namespace App\Filament\Administration\Resources;

use App\Enums;
use App\Filament\Actions\BulkAction;
use App\Filament\Administration\Resources\StudentResource\Pages;
use App\Filament\Core\BaseResource;
use App\Mail;
use App\Models\InternshipAgreement;
use App\Models\Student;
use Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades;

class StudentResource extends BaseResource
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

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
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
            'activeInternshipAgreement.id_pfe',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->maxLength(5),
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
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('title')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('pin')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->formatStateUsing(function ($record) {
                        return $record->long_full_name;
                    })
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->formatStateUsing(function ($record) {
                        return "{$record->email}, {$record->email_perso}";
                    })
                    ->wrap()
                    ->searchable(),

                // Tables\Columns\TextColumn::make('email_perso')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cv')
                    ->label('Curriculum vitae')
                    ->limit(20)
                    ->url(fn (Student $record): ?string => $record?->cv)
                    ->searchable(),
                Tables\Columns\TextColumn::make('lm')
                    ->label('Cover letter')
                    ->url(fn (Student $record): ?string => $record?->lm)
                    ->limit(20)
                    ->searchable(),
                Tables\Columns\TextColumn::make('photo')
                    ->url(fn (Student $record): ?string => $record?->photo)
                    ->limit(20)
                    ->searchable(),
                Tables\Columns\TextColumn::make('birth_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('level')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\SelectColumn::make('program')
                    ->options(Enums\Program::class)
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('is_mobility')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('abroad_school')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('year.title')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('graduated_at')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->date()
                    ->sortable(),
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
                Tables\Actions\Action::make('sendEmail')
                    ->form([
                        Forms\Components\TextInput::make('subject')->required(),
                        Forms\Components\RichEditor::make('body')->required(),
                    ])
                    ->action(
                        fn (array $data, InternshipAgreement $internship) => Facades\Mail::to($internship->student->email_perso)
                            ->send(new Mail\GenericEmail(
                                $internship->student,
                                $data['subject'],
                                $data['body'],
                            ))
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
}
