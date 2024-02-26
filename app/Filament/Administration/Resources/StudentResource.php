<?php

namespace App\Filament\Administration\Resources;

use App\Enums;
use App\Filament\Actions\SendBulkEmail;
use App\Filament\Administration\Resources\StudentResource\Pages;
use App\Filament\Core\BaseResource as Resource;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\GlobalSearch\Actions\Action;
// use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $title = 'Manage Students';

    protected static ?string $recordTitleAttribute = 'full_name';

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?int $sort = 3;

    protected static ?string $recordFirstNameAttribute = 'first_name';

    public static function canAccess(): bool
    {
        return auth()->user()->isAdministrator();
    }
    // public static function getGlobalSearchResultTitle(Model $record): string
    // {
    //     return $record->name;
    // }

    // public static function getGlobalSearchResultActions(Model $record): array
    // {
    //     return [
    //         Action::make('edit')
    //             ->url(static::getUrl('edit', ['record' => $record])),
    //     ];
    // }

    public static function getnavigationGroup(): string
    {
        return __('Students and projects');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['full_name', 'program', 'internship.id_pfe'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('year_id')
                    ->relationship('year', 'title')
                    ->required(),
                Forms\Components\Select::make('title')
                    ->options(Enums\Title::class)
                    ->required(),
                Forms\Components\TextInput::make('pin')
                    ->numeric(),
                Forms\Components\TextInput::make('full_name')
                    ->maxLength(191),
                Forms\Components\TextInput::make('first_name')
                    ->maxLength(191),
                Forms\Components\TextInput::make('last_name')
                    ->maxLength(191),
                Forms\Components\TextInput::make('email_perso')
                    ->email()
                    ->maxLength(191),
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
                Forms\Components\Toggle::make('is_active'),
                Forms\Components\DatePicker::make('graduated_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup('program')
            ->groups([
                Tables\Grouping\Group::make('Level')
                    ->label(__('Level'))
                    ->collapsible()
                    ->titlePrefixedWithLabel(false),
                Tables\Grouping\Group::make('program')
                    ->label(__('Program'))
                    ->collapsible(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('ID')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->label(__('Full name'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        return $record->long_full_name;
                    }),
                Tables\Columns\TextColumn::make('email_perso')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cv')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('lm')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('photo')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('birth_date')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('level')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('program')
                    ->formatStateUsing(function ($record) {
                        return $record->program->value;
                    })
                    ->searchable(),
                // Tables\Columns\IconColumn::make('is_mobility')
                //     ->boolean(),
                // Tables\Columns\TextColumn::make('abroad_school')
                //     ->searchable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\IconColumn::make('is_active')
                //     ->boolean()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('graduated_at')
                //     ->date()
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
            ])

            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])->hidden(fn () => auth()->user()->isAdministrator() === false),
            ], position: Tables\Enums\ActionsPosition::BeforeColumns)
            ->bulkActions([
                ExportBulkAction::make(),
                SendBulkEmail::make('send_emails'),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageStudents::route('/'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('Student');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Students');
    }
}
