<?php

namespace App\Filament\Administration\Resources;

use App\Enums;
use App\Filament\Actions\BulkAction\Email\SendBulkInvitationEmail;
use App\Filament\Actions\BulkAction\Email\SendUsersBulkEmail;
use App\Filament\Administration\Resources\UserResource\Pages;
use App\Filament\Core\BaseResource as Resource;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $modelLabel = 'User';

    protected static ?string $pluralModelLabel = 'Users';

    protected static ?string $title = 'System users and access control';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Administration';

    // protected static ?string $navigationParentItem = '';
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?int $navigationSort = 4;

    public static function getnavigationGroup(): string
    {
        return __(self::$navigationGroup);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('title')
                    ->options(Enums\Title::class),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('first_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('role')
                    ->options(Enums\Role::class)
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('department')
                    ->options(Enums\Department::class),
                Forms\Components\Select::make('assigned_program')
                    ->options(Enums\Program::class),
                Forms\Components\DateTimePicker::make('email_verified_at'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->maxLength(255)
                    ->dehydrated(fn (?string $state): bool => filled($state)),
                Forms\Components\Toggle::make('active_status'),
                Forms\Components\TextInput::make('avatar')
                    ->maxLength(255)
                    ->default('avatar.png'),
                Forms\Components\Toggle::make('dark_mode')
                    ->required(),
                Forms\Components\TextInput::make('messenger_color')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url')
                    ->label('Avatar')
                    ->circular(),
                Tables\Columns\TextColumn::make('long_full_name')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('first_name')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('last_name')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('department')
                    ->searchable(),
                Tables\Columns\TextColumn::make('assigned_program')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('active_status')
                    ->boolean(),
                // Tables\Columns\TextColumn::make('avatar')
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
                // Tables\Actions\ViewAction::make()
                //     ->label(false),
                Tables\Actions\EditAction::make()
                    ->label(false)
                    ->visible(fn (User $record) => auth()->user()->can('update', $record)),
                Tables\Actions\DeleteAction::make()
                    ->label(false)
                    ->visible(fn (User $record) => auth()->user()->can('delete', $record)),
                \STS\FilamentImpersonate\Tables\Actions\Impersonate::make()
                    ->hidden(fn ($record) => ! $record->canBeImpersonated()),
            ], position: Tables\Enums\ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    SendUsersBulkEmail::make('write_email')
                        ->label('Write Emails to selected users')
                        ->requiresConfirmation(),
                    SendBulkInvitationEmail::make('send_invitations')
                        ->label('Send Invitations to join the platform')
                        ->requiresConfirmation(),
                ]),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
