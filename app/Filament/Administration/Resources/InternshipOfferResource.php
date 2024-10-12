<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Administration\Resources\InternshipOfferResource\Pages;
use App\Filament\Core\BaseResource;
use App\Models\InternshipOffer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Parfaitementweb\FilamentCountryField\Forms\Components\Country;
use Parfaitementweb\FilamentCountryField\Tables\Columns\CountryColumn;

class InternshipOfferResource extends BaseResource
{
    protected static ?string $model = InternshipOffer::class;

    protected static ?string $modelLabel = 'internship offer';

    protected static ?string $pluralModelLabel = 'internship offers';

    protected static ?string $title = 'Manage internship offers';

    protected static ?string $recordTitleAttribute = 'organization_name';

    protected static ?string $navigationGroup = 'Entreprises';

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?int $sort = 2;

    protected static ?string $navigationBadgeTooltip = 'Internship offers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\Select::make('year_id')
                //     ->relationship('year'),
                Forms\Components\TextInput::make('organization_name')
                    ->maxLength(191),
                Country::make('country')
                    ->searchable(),
                Forms\Components\TextInput::make('internship_type'),
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
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('project_details')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('internship_location')
                    ->maxLength(191),
                Forms\Components\TextInput::make('keywords')
                    ->maxLength(191),
                Forms\Components\TextInput::make('attached_file')
                    ->maxLength(191),
                Forms\Components\Textarea::make('link')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('internship_duration'),
                Forms\Components\TextInput::make('remuneration')
                    ->maxLength(191),
                Forms\Components\TextInput::make('currency')
                    ->maxLength(10),
                Forms\Components\TextInput::make('recruting_type'),
                Forms\Components\TextInput::make('application_email')
                    ->email()
                    ->maxLength(191),
                Forms\Components\TextInput::make('status'),
                Forms\Components\TextInput::make('applyable')
                    ->maxLength(1),
                Forms\Components\DatePicker::make('expire_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('year_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('organization_name')
                    ->searchable(),
                CountryColumn::make('country')
                    ->searchable(),
                Tables\Columns\TextColumn::make('internship_type'),
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
                Tables\Columns\TextColumn::make('internship_duration'),
                Tables\Columns\TextColumn::make('remuneration')
                    ->searchable(),
                Tables\Columns\TextColumn::make('currency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('recruting_type'),
                Tables\Columns\TextColumn::make('application_email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('applyable'),
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
            'index' => Pages\ListInternshipOffers::route('/'),
            'create' => Pages\CreateInternshipOffer::route('/create'),
            'view' => Pages\ViewInternshipOffer::route('/{record}'),
            'edit' => Pages\EditInternshipOffer::route('/{record}/edit'),
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
