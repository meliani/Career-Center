<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Administration\Resources\JobOfferResource\Pages;
use App\Filament\Core\BaseResource;
use App\Models\JobOffer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JobOfferResource extends BaseResource
{
    protected static ?string $model = JobOffer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Job offer';

    protected static ?string $pluralModelLabel = 'Job offers';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationParentItem = 'Organization Accounts';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('organization_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('organization_type')
                    ->required(),
                Forms\Components\TextInput::make('organization_id')
                    ->numeric(),
                Forms\Components\TextInput::make('country')
                    ->maxLength(255),
                Forms\Components\TextInput::make('responsible_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('responsible_occupation')
                    ->maxLength(255),
                Forms\Components\TextInput::make('responsible_phone')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('responsible_email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\Textarea::make('job_title')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('job_details')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('is_remote')
                    ->maxLength(255),
                Forms\Components\TextInput::make('job_location')
                    ->maxLength(255),
                Forms\Components\TextInput::make('keywords')
                    ->maxLength(255),
                Forms\Components\TextInput::make('attached_file')
                    ->maxLength(255),
                Forms\Components\Textarea::make('application_link')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('job_duration')
                    ->numeric(),
                Forms\Components\TextInput::make('remuneration')
                    ->maxLength(255),
                Forms\Components\TextInput::make('currency')
                    ->maxLength(255),
                Forms\Components\TextInput::make('workload')
                    ->numeric(),
                Forms\Components\TextInput::make('recruting_type'),
                Forms\Components\TextInput::make('application_email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\Toggle::make('applyable'),
                Forms\Components\DatePicker::make('expire_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('organization_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('organization_type'),
                Tables\Columns\TextColumn::make('organization_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('country')
                    ->searchable(),
                Tables\Columns\TextColumn::make('responsible_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('responsible_occupation')
                    ->searchable(),
                Tables\Columns\TextColumn::make('responsible_phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('responsible_email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('is_remote')
                    ->searchable(),
                Tables\Columns\TextColumn::make('job_location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('keywords')
                    ->searchable(),
                Tables\Columns\TextColumn::make('attached_file')
                    ->searchable(),
                Tables\Columns\TextColumn::make('job_duration')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('remuneration')
                    ->searchable(),
                Tables\Columns\TextColumn::make('currency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('workload')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('recruting_type'),
                Tables\Columns\TextColumn::make('application_email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\IconColumn::make('applyable')
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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListJobOffers::route('/'),
            'create' => Pages\CreateJobOffer::route('/create'),
            'view' => Pages\ViewJobOffer::route('/{record}'),
            'edit' => Pages\EditJobOffer::route('/{record}/edit'),
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
