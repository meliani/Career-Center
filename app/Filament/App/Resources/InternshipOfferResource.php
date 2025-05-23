<?php

namespace App\Filament\App\Resources;

use App\Enums;
use App\Filament\App\Resources\InternshipOfferResource\Pages;
use App\Filament\Core\StudentBaseResource;
use App\Models\InternshipOffer;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Parfaitementweb\FilamentCountryField\Forms\Components\Country;
use Parfaitementweb\FilamentCountryField\Tables\Columns\CountryColumn;

class InternshipOfferResource extends StudentBaseResource
{
    protected static ?string $model = InternshipOffer::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Internship offer';

    protected static ?string $pluralModelLabel = 'Internship offers';

    protected static ?string $title = 'Internship offers';

    protected static ?string $recordTitleAttribute = 'project_title';

    protected static ?string $navigationGroup = 'Internship Offers';

    // protected static ?string $navigationGroup = 'Internships and Projects';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('expire_at', '>', now())
            ->where('status', Enums\OfferStatus::Published);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->contentGrid(
                [
                    'md' => 2,
                    'lg' => 2,
                    'xl' => 2,
                    '2xl' => 2,
                ]
            )
            ->columns([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\TextColumn::make('organization_name')
                            // ->description(__('Organization'), position: 'above')
                        ->toggleable(false)
                        ->sortable(false)
                        ->grow(true)
                        ->weight(\Filament\Support\Enums\FontWeight::Bold),
                    Tables\Columns\TextColumn::make('internship_type')
                        ->toggleable(false)
                        ->sortable(false),
                    Tables\Columns\TextColumn::make('internship_duration')
                        ->numeric()
                        ->toggleable(false)
                        ->sortable(false)
                        ->suffix(__(' months')),
                ]),
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('project_title')
                            ->description(__('Subject'), position: 'above')
                            ->toggleable(false)
                            ->sortable(false),
                        // CountryColumn::make('country'),

                    ]),
                ]),

                Tables\Columns\Layout\Split::make([
                    Tables\Columns\TextColumn::make('application_email')
                        ->description(__('Application email'), position: 'above')
                        ->toggleable(false)
                        ->sortable(false),
                    Tables\Columns\TextColumn::make('expire_at')
                        ->description(__('Expiration date'), position: 'above')
                        ->date()
                        ->toggleable(false)
                        ->sortable(false)
                        ->badge(),
                    // \Mokhosh\FilamentRating\Columns\RatingColumn::make('averageRating')
                    //     ->label('')
                    //     ->color('primary')
                    //     ->theme(\Mokhosh\FilamentRating\RatingTheme::HalfStars),
                ]),
                Tables\Columns\IconColumn::make('viewed')
                    ->label(__('Viewed'))
                    ->boolean()
                    ->searchable(false)
                    ->sortable(false)
                    ->getStateUsing(fn ($record) => auth()->user()->hasViewedOffer($record->id))
                    ->icon(fn ($state) => $state ? 'heroicon-o-eye' : 'heroicon-o-eye-slash')
                    ->color(fn ($state) => $state ? 'success' : 'gray'),
            ])
            ->filters([
                // Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('ApplyToInternshipOffer')
                    ->label('Apply')
                    ->icon('heroicon-o-check')
                    ->color('primary')
                    ->disabled(fn ($record) => auth()->user()->hasAppliedToInternshipOffer($record))
                    ->visible(fn ($record) => $record->recruiting_type === Enums\RecruitingType::SchoolManaged)
                    ->requiresConfirmation()
                    ->modalIconColor('success')
                    ->modalIcon('heroicon-o-check')
                    ->modalHeading(__('Make sure you profile is up to date'))
                    ->modalDescription(__('Your CV and other information must be up to date to apply to this internship.'))
                    ->modalSubmitActionLabel(__('Apply'))
                    ->color('success')
                    ->action(function ($record) {
                        auth()->user()->applyToInternshipOffer($record);
                        \Filament\Notifications\Notification::make()
                            ->title(__('Internship offer applied'))
                            ->success()
                            ->icon('heroicon-s-check-circle')
                            ->send();
                    })
                    ->button(),
                Tables\Actions\ViewAction::make(),
            ], position: Tables\Enums\ActionsPosition::BeforeColumns);
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
            // 'create' => Pages\CreateInternshipOffer::route('/create'),
            'view' => Pages\ViewInternshipOffer::route('/{record}'),
            // 'edit' => Pages\EditInternshipOffer::route('/{record}/edit'),
        ];
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()
    //         ->withoutGlobalScopes([
    //             SoftDeletingScope::class,
    //         ]);
    // }

    public static function infolist(Infolist $infolist): Infolist
    {
        // Track the view
        if (auth()->check()) {
            auth()->user()->markOfferAsViewed($infolist->record->id);
        }

        // Existing view counting
        views($infolist->record)->record();

        return $infolist
            ->schema([
                Infolists\Components\Group::make([
                    Infolists\Components\Section::make(__('Organization Information'))
                        ->columns(3)
                        ->columnSpan(1)
                        ->schema([
                            Infolists\Components\TextEntry::make('organization_name'),
                            Infolists\Components\TextEntry::make('organization_type'),
                            Infolists\Components\TextEntry::make('country'),
                        ])
                        ->headerActions([
                            \Filament\Infolists\Components\Actions\Action::make('ApplyToInternshipOffer')
                                ->label('Apply to internship')
                                ->icon('heroicon-o-check')
                                ->color('primary')

                                ->disabled(fn ($record) => auth()->user()->hasAppliedToInternshipOffer($record))
                                ->visible(fn ($record) => $record->recruiting_type === Enums\RecruitingType::SchoolManaged)
                                ->requiresConfirmation()
                                ->modalIconColor('success')
                                ->modalIcon('heroicon-o-check')
                                ->modalHeading(__('Make sure you profile is up to date'))
                                ->modalDescription(__('Your CV and other information must be up to date to apply to this internship.'))
                                ->modalSubmitActionLabel(__('Apply'))
                                ->color('success')
                                ->action(function ($record) {
                                    auth()->user()->applyToInternshipOffer($record);
                                    \Filament\Notifications\Notification::make()
                                        ->title(__('Internship offer applied'))
                                        ->success()
                                        ->icon('heroicon-s-check-circle')
                                        ->send();
                                }),

                        ]),
                ]),
                Infolists\Components\Group::make([
                    Infolists\Components\Section::make(__('Responsible Information'))
                        ->columnSpan(1)
                        ->columns(2)
                        ->schema([
                            Infolists\Components\TextEntry::make('responsible_name'),
                            Infolists\Components\TextEntry::make('responsible_occupation'),
                            // Infolists\Components\TextEntry::make('responsible_phone'),
                            // Infolists\Components\TextEntry::make('responsible_email'),
                        ]),
                ]),

                Infolists\Components\Section::make(__('Internship Information'))
                    ->columnSpan(1)
                    ->columns(4)
                    ->schema([
                        // Infolists\Components\TextEntry::make('project_title')
                        // ->label(fn ($record) => $record->expertiseField->name)
                        //     ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('expertiseField.name')
                            ->badge()
                            ->label(fn ($record) => $record->project_title)
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('project_details')
                            ->columnSpanFull()
                            ->markdown(),
                    ]),

                Infolists\Components\Section::make(__('Internship Details'))
                    ->columnSpan(1)
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('internship_type'),
                        Infolists\Components\TextEntry::make('internship_location'),
                        // Infolists\Components\TextEntry::make('keywords')
                        //     ->badge(),
                        // Infolists\Components\TextEntry::make('attached_file'),
                        Infolists\Components\TextEntry::make('internship_duration')
                            ->suffix(__(' months'))
                            ->placeholder('No duration specified'),
                        Infolists\Components\TextEntry::make('remuneration')
                            ->money(fn ($record) => $record->currency->getLabel())
                            ->placeholder(__('No remuneration specified')),
                        // Infolists\Components\TextEntry::make('currency')
                        //     ->placeholder('No currency specified'),
                        Infolists\Components\TextEntry::make('workload')
                            ->suffix(__(' hours'))
                            ->placeholder(__('No workload specified')),
                        Infolists\Components\TextEntry::make('recruiting_type')
                            ->placeholder('No recruiting type specified'),
                        Infolists\Components\TextEntry::make('application_email')
                            ->placeholder('No application email specified'),
                        // Infolists\Components\TextEntry::make('status'),
                        // Infolists\Components\TextEntry::make('applyable'),
                        Infolists\Components\TextEntry::make('expire_at')
                            ->date()
                            ->placeholder('No expiration date specified')
                            ->badge(),
                        \Mokhosh\FilamentRating\Entries\RatingEntry::make('averageRating')
                            ->label('')
                            ->color('primary')
                            ->theme(\Mokhosh\FilamentRating\RatingTheme::HalfStars),
                    ]),
            ]);
    }
}
