<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\InternshipOfferResource\Pages;
use App\Models\InternshipOffer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Parfaitementweb\FilamentCountryField\Forms\Components\Country;
use Parfaitementweb\FilamentCountryField\Tables\Columns\CountryColumn;

class InternshipOfferResource extends Resource
{
    protected static ?string $model = InternshipOffer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('year_id')
                    ->numeric(),
                Forms\Components\TextInput::make('organization_name')
                    ->maxLength(191),
                Forms\Components\TextInput::make('organization_type')
                    ->maxLength(191),
                Forms\Components\TextInput::make('organization_id')
                    ->numeric(),
                Country::make('country'),
                Forms\Components\TextInput::make('internship_type'),
                Forms\Components\TextInput::make('responsible_name')
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
                Forms\Components\Textarea::make('application_link')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('internship_duration')
                    ->numeric(),
                Forms\Components\TextInput::make('remuneration')
                    ->maxLength(191),
                Forms\Components\TextInput::make('currency')
                    ->maxLength(10),
                Forms\Components\TextInput::make('workload')
                    ->numeric(),
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
                // Tables\Columns\TextColumn::make('year_id')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('organization_name')
                    ->searchable()
                    ->limit(20),
                Tables\Columns\TextColumn::make('project_title')
                    ->limit(30),

                // Tables\Columns\TextColumn::make('organization_type')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('organization_id')
                //     ->numeric()
                //     ->sortable(),
                CountryColumn::make('country')
                    ->searchable(),
                Tables\Columns\TextColumn::make('internship_type'),
                Tables\Columns\TextColumn::make('responsible_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('responsible_occupation')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('responsible_phone')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('responsible_email')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('internship_location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('keywords')
                    ->searchable(),
                Tables\Columns\TextColumn::make('attached_file')
                    ->searchable()
                    ->url(fn (InternshipOffer $record) => Storage::url($record->attached_file), shouldOpenInNewTab: true),                Tables\Columns\TextColumn::make('internship_duration')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('remuneration')
                    ->searchable(),
                Tables\Columns\TextColumn::make('currency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('workload')
                    ->numeric()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('recruting_type'),
                Tables\Columns\TextColumn::make('application_email')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('status'),
                // Tables\Columns\TextColumn::make('applyable'),
                Tables\Columns\TextColumn::make('expire_at')
                    ->date()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('deleted_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
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

    public static function infolist(Infolist $infolist): Infolist
    {
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
                        Infolists\Components\TextEntry::make('project_title')
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('project_details')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make(__('Internship Details'))
                    ->columnSpan(1)
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('internship_type'),
                        Infolists\Components\TextEntry::make('internship_location'),
                        // Infolists\Components\TextEntry::make('keywords')
                        //     ->badge(),
                        Infolists\Components\TextEntry::make('attached_file'),
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
                            ->placeholder('No workload specified'),
                        Infolists\Components\TextEntry::make('recruting_type')
                            ->placeholder('No recruiting type specified'),
                        Infolists\Components\TextEntry::make('application_email')
                            ->placeholder('No application email specified'),
                        // Infolists\Components\TextEntry::make('status'),
                        // Infolists\Components\TextEntry::make('applyable'),
                        Infolists\Components\TextEntry::make('expire_at')
                            ->date()
                            ->placeholder('No expiration date specified'),
                    ]),
            ]);
    }
}
