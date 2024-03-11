<?php

namespace App\Filament\Administration\Resources;

use App\Enums\Department;
use App\Enums\Program;
use App\Enums\Role;
use App\Enums\Title;
use App\Filament\Actions\BulkAction;
use App\Filament\Administration\Resources\ProfessorResource\Pages;
use App\Filament\Core;
use App\Models\Professor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums as FilamentEnums;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ProfessorResource extends Core\BaseResource
{
    protected static ?string $model = Professor::class;

    protected static ?string $modelLabel = 'Professor';

    protected static ?string $pluralModelLabel = 'Professors';

    protected static ?string $title = 'Manage professors';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Juries';

    // protected static ?string $navigationParentItem = '';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $sort = 9;

    public static function canAccess(): bool
    {
        return auth()->user()->isAdministrator() || auth()->user()->isDirection();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->numeric()
                    ->required()
                    ->default(
                        Professor::max('id') + 1
                    ),
                Forms\Components\Select::make('title')
                    ->options(Title::class)
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label(__('Username'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('first_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('department')
                    ->options(Department::class),
                Forms\Components\Select::make('role')
                    ->options(Role::class)
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('assigned_program')
                    ->label(__('Program Coordinator'))
                    ->options(Program::class),
                Forms\Components\Toggle::make('is_enabled')
                    ->label(__('Account enabled'))
                    ->required(),
                // Forms\Components\DateTimePicker::make('email_verified_at'),
                // Forms\Components\TextInput::make('password')
                //     ->password()
                //     ->required()
                //     ->maxLength(255),
                Forms\Components\Toggle::make('active_status')
                    ->required(),
                Forms\Components\FileUpload::make('avatar')
                    ->default('avatar.png'),
                // Forms\Components\Toggle::make('dark_mode')
                //     ->required(),
                // Forms\Components\TextInput::make('messenger_color')
                //     ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(20)
            ->striped()
            ->columns([
                // Tables\Columns\TextColumn::make('title')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(
                        ['first_name', 'last_name']
                    )
                    ->formatStateUsing(function ($record) {
                        return $record->long_full_name;
                    }),
                // Tables\Columns\TextColumn::make('first_name')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('last_name')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('department')
                    ->searchable(),
                Tables\Columns\TextColumn::make('projects_count')
                    ->alignment(Alignment::Center)
                    ->searchable(false)

                    ->summarize([
                        Summarizers\Average::make()->numeric(
                            decimalPlaces: 0,
                        ),
                        Summarizers\Range::make(),
                        Summarizers\Sum::make(),
                    ])
                    ->label(__('Number of Projects Participations'))
                    // ->label(new HtmlString(__('Number of <br /> Projects Participations')))
                    // ->label(new HtmlString(nl2br("Home \n number")))
                    // ->translateLabel(false)
                    ->alignCenter()
                    ->sortable()
                    ->counts('projects'),
                Tables\Columns\TextColumn::make('projects.id_pfe')
                    // ->formatStateUsing(function ($record) {
                    //     return $record->projects->pluck('internship_agreements')->flatten()->pluck('id_pfe')->implode(', ');
                    // })
                    // ->listWithLineBreaks()
                    ->bulleted()
                    // ->action(fn ($record) => Pages\EditProfessor::route('/{record}/edit'))
                    ->label(__('Projects')),
                Tables\Columns\TextColumn::make('role')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('assigned_program')
                    ->label(__('Program Coordinator Program'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('is_enabled')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                // Tables\Columns\TextColumn::make('email_verified_at')
                //     ->dateTime()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\IconColumn::make('active_status')
                //     ->boolean(),
                // Tables\Columns\TextColumn::make('avatar')
                //     ->toggleable(isToggledHiddenByDefault: true)
                //     ->searchable(),
                // Tables\Columns\IconColumn::make('dark_mode')
                //     ->boolean(),
                // Tables\Columns\TextColumn::make('messenger_color')
                //     ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction\Email\SendProfessorsProjectsOverview::make('Send Professors Projects Overview')
                        ->label(__('Send Professors Projects Overview'))
                        ->hidden(fn () => auth()->user()->isAdministrator() === false)
                        ->outlined(),
                ])
                    ->label(__('Send email'))
                    ->dropdownWidth(FilamentEnums\MaxWidth::Small)
                    ->hidden(fn () => auth()->user()->isAdministrator() === false),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ])
                    ->label(__('actions'))
                    ->hidden(fn () => auth()->user()->isAdministrator() === false),

            ])
            ->defaultSort('projects_count', 'desc')
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make(),
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
            'index' => Pages\ListProfessors::route('/'),
            'create' => Pages\CreateProfessor::route('/create'),
            'edit' => Pages\EditProfessor::route('/{record}/edit'),
        ];
    }
}
