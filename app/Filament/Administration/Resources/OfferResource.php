<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Administration\Resources\OfferResource\Pages;
use App\Models\Offer;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Core\BaseResource as Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OfferResource extends Resource
{
    protected static ?string $model = Offer::class;

    protected static ?string $navigationGroup = 'Internships';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::count();
    // }
    public static function getModelLabel(): string
    {
        return __('Internship offer');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Internship offers');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('year_id')
                    ->numeric(),
                Forms\Components\TextInput::make('level')
                    ->numeric(),
                Forms\Components\TextInput::make('organization_name')
                    ->maxLength(191),
                Forms\Components\TextInput::make('country')
                    ->maxLength(191),
                Forms\Components\TextInput::make('internship_type')
                    ->maxLength(10),
                Forms\Components\TextInput::make('responsible_fullname')
                    ->maxLength(191),
                Forms\Components\TextInput::make('responsible_occupation')
                    ->maxLength(191),
                Forms\Components\TextInput::make('responsible_phone')
                    ->tel()
                    ->maxLength(191),
                Forms\Components\TextInput::make('responsible_email')
                    ->email()
                    ->maxLength(191),
                Forms\Components\Textarea::make('project_title')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('project_details')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('internship_location')
                    ->maxLength(191),
                Forms\Components\TextInput::make('keywords')
                    ->maxLength(191),
                Forms\Components\TextInput::make('attached_file')
                    ->maxLength(191),
                Forms\Components\Textarea::make('link')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('internship_duration')
                    ->maxLength(10),
                Forms\Components\TextInput::make('remuneration')
                    ->maxLength(191),
                Forms\Components\TextInput::make('currency')
                    ->maxLength(10),
                Forms\Components\TextInput::make('recruting_type')
                    ->maxLength(10),
                Forms\Components\TextInput::make('application_email')
                    ->email()
                    ->maxLength(191),
                Forms\Components\TextInput::make('event_id')
                    ->numeric(),
                Forms\Components\TextInput::make('event_date')
                    ->maxLength(191),
                Forms\Components\TextInput::make('badge')
                    ->maxLength(191),
                Forms\Components\TextInput::make('display_permissions')
                    ->maxLength(191),
                Forms\Components\Toggle::make('status'),
                Forms\Components\Toggle::make('is_valid'),
                Forms\Components\Toggle::make('applyable'),
                Forms\Components\DatePicker::make('expire_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('year_id')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('level')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('organization_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country')
                    ->searchable(),
                Tables\Columns\TextColumn::make('internship_type')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('responsible_fullname')
                    ->searchable(),
                Tables\Columns\TextColumn::make('responsible_occupation')
                    ->searchable(),
                Tables\Columns\TextColumn::make('responsible_phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('responsible_email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('internship_location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('keywords')
                    ->searchable(),
                Tables\Columns\TextColumn::make('attached_file')
                    ->searchable(),
                Tables\Columns\TextColumn::make('internship_duration')
                    ->searchable(),
                Tables\Columns\TextColumn::make('remuneration')
                    ->searchable(),
                Tables\Columns\TextColumn::make('currency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('recruting_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('application_email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('event_id')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('event_date')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('badge')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('display_permissions')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_valid')
                    ->boolean(),
                Tables\Columns\IconColumn::make('applyable')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->boolean(),
                Tables\Columns\TextColumn::make('expire_at')
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
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageOffers::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
