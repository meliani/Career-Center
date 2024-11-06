<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Administration\Resources\InternshipOfferResource\Pages;
use App\Filament\Core\BaseResource;
use App\Models\InternshipOffer;
use App\Models\Year;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Parfaitementweb\FilamentCountryField\Forms\Components\Country;
use Parfaitementweb\FilamentCountryField\Tables\Columns\CountryColumn;
use Storage;

class InternshipOfferResource extends BaseResource
{
    protected static ?string $model = InternshipOffer::class;

    protected static ?string $modelLabel = 'internship offer';

    protected static ?string $pluralModelLabel = 'internship offers';

    protected static ?string $title = 'Manage internship offers';

    protected static ?string $recordTitleAttribute = 'organization_name';

    protected static ?string $navigationGroup = 'Internships and Projects';

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationBadgeTooltip = 'Internship offers';

    public static function canAccess(): bool
    {
        return auth()->user()->isAdministrator() || auth()->user()->isProfessor() || auth()->user()->isDepartmentHead() || auth()->user()->isProgramCoordinator() || auth()->user()->isDirection();
    }

    public static function canViewAny(): bool
    {
        if (auth()->check()) {
            return auth()->user()->isAdministrator() || auth()->user()->isDirection();
        }

        return false;
    }

    public static function canView(Model $record): bool
    {
        return self::canViewAny();
    }

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
                Forms\Components\Textarea::make('application_link')
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
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('internship_level')
                    ->searchable()
                    ->badge()
                    ->description(fn (InternshipOffer $record) => $record->internship_duration ? $record->internship_duration . ' months' : null)
                    ->label('Level and duration'),
                Tables\Columns\TextColumn::make('organization_name')
                    ->searchable()
                    // ->description(fn (InternshipOffer $record) => $record->views_summary)
                    // ->limit(50)
                    ->weight(FontWeight::Bold)
                    ->wrap()
                    // ->width('1%')
                    ->formatStateUsing(fn (InternshipOffer $record) => $record->organization_name . ' - ' . $record->country)
                    ->description(fn (InternshipOffer $record) => $record->responsible_name . ' - ' . $record->responsible_occupation)
                    ->tooltip(fn (InternshipOffer $record) => $record->views_summary)
                    ->wrapHeader(false),
                // ->size(Tables\Columns\TextColumn\TextColumnSize::Large)
                // ->lineClamp(2),
                Tables\Columns\TextColumn::make('project_title')
                    ->lineClamp(2)
                    ->description(fn (InternshipOffer $record) => $record->internship_type?->getLabel() . ' - ' . $record->internship_location)
                    ->limit(60)
                    ->tooltip(fn (InternshipOffer $record) => $record->project_title)
                    ->wrap()
                    ->wrapHeader(false),
                Tables\Columns\TextColumn::make('expertiseField.name')
                    ->searchable(false),
                Tables\Columns\TextColumn::make('views_count')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Views')
                    ->numeric()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('organization_type')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('organization_id')
                //     ->numeric()
                //     ->sortable(),
                // CountryColumn::make('country')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('internship_type'),
                Tables\Columns\TagsColumn::make('tags')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                // Tables\Columns\TextColumn::make('responsible_name')
                //     ->toggleable(isToggledHiddenByDefault: true)
                //     ->searchable()
                //     ->description(fn (InternshipOffer $record) => $record->responsible_occupation . ' - ' . $record->responsible_email),
                // Tables\Columns\TextColumn::make('responsible_occupation')
                //     ->toggleable(isToggledHiddenByDefault: true)
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('responsible_phone')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('responsible_email')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('internship_location')
                //     ->toggleable(isToggledHiddenByDefault: true)
                //     ->searchable(),

                // Tables\Columns\TextColumn::make('attached_file')
                //     ->searchable()
                //     ->url(fn (InternshipOffer $record) => Storage::url($record->attached_file), shouldOpenInNewTab: true),
                // Tables\Columns\TextColumn::make('internship_duration')
                //     ->numeric()
                //     ->sortable()
                //     ->suffix(__(' months')),
                Tables\Columns\TextColumn::make('recruting_type')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('remuneration')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->money(fn (InternshipOffer $record) => $record->currency->getLabel()),
                // Tables\Columns\TextColumn::make('currency')
                //     ->toggleable(isToggledHiddenByDefault: true)
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('workload')
                // ->toggleable(isToggledHiddenByDefault: true)
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('recruting_type'),
                Tables\Columns\TextColumn::make('application_email')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                // Tables\Columns\TextColumn::make('status'),
                // Tables\Columns\TextColumn::make('applyable'),
                Tables\Columns\TextColumn::make('expire_at')
                    ->date()
                    ->sortable()
                    ->since()
                    ->badge(),
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
                ])
                    ->label(__('Delete')),
                Tables\Actions\BulkAction::make('Publish')
                    ->label('Publish selection')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(
                        function ($records) {
                            $records->each->publish();
                            \Filament\Notifications\Notification::make()
                                ->title(__('Internship offers published'))
                                ->success()
                                ->icon('heroicon-s-check-circle')
                                ->send();
                        }
                    ),
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
            // 'create' => Pages\CreateInternshipOffer::route('/create'),
            'view' => Pages\ViewInternshipOffer::route('/{record}'),
            'edit' => Pages\EditInternshipOffer::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->active();
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
                        // ->url(fn (InternshipOffer $record) => Storage::url($record->attached_file), shouldOpenInNewTab: true),
                        Infolists\Components\TextEntry::make('attached_file')
                            ->url(fn ($record) => Storage::url($record->attached_file)),
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
