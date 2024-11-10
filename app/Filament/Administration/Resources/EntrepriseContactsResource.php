<?php

namespace App\Filament\Administration\Resources;

use App\Enums\EntrepriseContactCategory;
use App\Enums\Title;
use App\Filament\Actions\BulkAction;
use App\Filament\Administration\Resources\EntrepriseContactsResource\Pages;
use App\Filament\Core\BaseResource;
use App\Models\EntrepriseContacts;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EntrepriseContactsResource extends BaseResource
{
    protected static ?string $model = EntrepriseContacts::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $modelLabel = 'Entreprise Contact';

    protected static ?string $pluralModelLabel = 'Entreprise Contacts';

    protected static ?string $navigationGroup = 'Mailing';

    // Use the correct records per page option
    protected static ?int $defaultTableRecordsPerPageSelectOption = 25;

    // protected static ?string $navigationParentItem = 'Internships and Projects';

    public static function canAccess(): bool
    {
        if (auth()->check()) {
            return auth()->user()->isSuperAdministrator();
        }

        return false;
    }

    public static function canViewAny(): bool
    {
        return self::canAccess();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('title')
                    ->options(Title::class)
                    ->required(),
                Forms\Components\TextInput::make('first_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('company')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('position')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('alumni_promotion')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('category')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('years_of_interactions_with_students')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('number_of_bounces')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('is_account_disabled')
                    ->required(),
                Forms\Components\DateTimePicker::make('last_time_contacted'),
                Forms\Components\Select::make('last_year_id_supervised')
                    ->relationship('year', 'title')
                    ->label('Last Year Supervised a student'),
                Forms\Components\TextInput::make('interactions_count')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Primary Information Group
                Tables\Columns\TextColumn::make('long_full_name')
                    ->label('Full Name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('company')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('position')
                    ->searchable(),

                // Contact Status Group
                Tables\Columns\IconColumn::make('is_account_disabled')
                    ->label('Disabled')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('number_of_bounces')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success')
                    ->numeric()
                    ->sortable(),

                // Interaction Details Group
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('interactions_count')
                    ->label('Interactions')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_time_contacted')
                    ->label('Last Contact')
                    ->dateTime('Y-m-d')
                    ->sortable(),

                // Additional Information (Hidden by Default)
                Tables\Columns\TextColumn::make('title')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('alumni_promotion')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('years_of_interactions_with_students')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('year.title')
                    ->label('Last Year Supervised')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime(),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            // Remove the ->condensed() call as it doesn't exist
            ->paginated([10, 25, 50, 100])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options(EntrepriseContactCategory::class),
                Tables\Filters\TrashedFilter::make(),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction\Email\SendSecondYearMailingCampaign::make('Send Second Year Mailing Campaign')
                        ->requiresConfirmation(),
                    BulkAction\Email\SendFinalProjectsMailingCampaign::make('Send Final Projects Mailing Campaign')
                        ->requiresConfirmation(),
                ])
                    // ->size(\Filament\Support\Enums\ActionSize::ExtraLarge)
                    ->dropdownWidth(\Filament\Support\Enums\MaxWidth::Small)
                    ->label('Email Campaigns'),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
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
            'index' => Pages\ListEntrepriseContacts::route('/'),
            'create' => Pages\CreateEntrepriseContacts::route('/create'),
            'edit' => Pages\EditEntrepriseContacts::route('/{record}/edit'),
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
