<?php

namespace App\Filament\Administration\Resources;

use App\Enums\EntrepriseContactCategory;
use App\Enums\Title;
use App\Filament\Actions\BulkAction;
use App\Filament\Administration\Resources\EntrepriseContactsResource\Pages;
use App\Filament\Core\BaseResource;
use App\Models\EntrepriseContacts;
use Filament\Forms;
use Filament\Forms\Components\Section as FormSection;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
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
                FormSection::make('Personal Information')
                    ->schema([
                        Forms\Components\Select::make('title')
                            ->enum(Title::class)
                            ->options(Title::class)
                            ->required(),
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),

                FormSection::make('Professional Information')
                    ->schema([
                        Forms\Components\TextInput::make('company')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('position')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('category')
                            ->enum(EntrepriseContactCategory::class)
                            ->options(EntrepriseContactCategory::class)
                            ->required(),
                    ])->columns(2),

                FormSection::make('Academic & Interaction Details')
                    ->schema([
                        Forms\Components\TextInput::make('alumni_promotion')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('years_of_interactions_with_students')
                            ->maxLength(255),
                        Forms\Components\Select::make('last_year_id_supervised')
                            ->relationship('year', 'title')
                            ->label('Last Year Supervised a student'),
                        Forms\Components\TextInput::make('interactions_count')
                            ->numeric()
                            ->disabled(),
                    ])->columns(2),

                FormSection::make('Account Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_account_disabled')
                            ->required(),
                        Forms\Components\TextInput::make('number_of_bounces')
                            ->required()
                            ->numeric()
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('last_time_contacted')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Contact Information')
                    ->schema([
                        TextEntry::make('title'),
                        TextEntry::make('long_full_name')
                            ->label('Full Name'),
                        TextEntry::make('email')
                            ->copyable(),
                    ])->columns(3),

                Section::make('Professional Details')
                    ->schema([
                        TextEntry::make('company'),
                        TextEntry::make('position'),
                        TextEntry::make('category')
                            ->badge(),
                    ])->columns(3),

                Section::make('Interaction History')
                    ->schema([
                        TextEntry::make('interactions_count')
                            ->label('Total Interactions'),
                        TextEntry::make('last_time_contacted')
                            ->dateTime('Y-m-d H:i'),
                        TextEntry::make('years_of_interactions_with_students'),
                        TextEntry::make('year.title')
                            ->label('Last Year Supervised'),
                    ])->columns(2),

                Section::make('Account Status')
                    ->schema([
                        TextEntry::make('is_account_disabled')
                            ->label('Account Status')
                            ->badge()
                            ->color(fn ($state) => $state ? 'danger' : 'success')
                            ->formatStateUsing(fn ($state) => $state ? 'Disabled' : 'Active'),
                        TextEntry::make('number_of_bounces')
                            ->badge()
                            ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->dateTime(),
                    ])->columns(2),
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
                // Contact Status Group
                Tables\Columns\IconColumn::make('is_account_disabled')
                    ->label('Disabled')
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('number_of_bounces')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success')
                    ->numeric()
                    ->sortable()
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
                Tables\Actions\ViewAction::make(),
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
            'view' => Pages\ViewEntrepriseContact::route('/{record}'),
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
