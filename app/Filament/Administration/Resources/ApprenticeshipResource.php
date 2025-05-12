<?php

namespace App\Filament\Administration\Resources;

use App\Enums;
use App\Filament\Administration\Resources\ApprenticeshipResource\Pages;
use App\Filament\Core\BaseResource;
use App\Models\Apprenticeship;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class ApprenticeshipResource extends BaseResource
{
    protected static ?string $model = Apprenticeship::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationBadgeTooltip = 'Announced apprenticeships';

    protected static ?string $navigationGroup = 'Internships and Projects';

    protected static ?string $modelLabel = 'Apprenticeship';

    protected static ?string $pluralModelLabel = 'Apprenticeships';

    protected static ?string $title = 'Announced apprenticeships';

    public static function canAccess(): bool
    {
        return auth()->user()->isAdministrator();
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count('id');
    }

    public static function canViewAny(): bool
    {
        if (auth()->check()) {
            return auth()->user()->isAdministrator();
        }

        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Section::make(__('Important Dates'))
                            ->description(__('Key dates for the apprenticeship process'))
                            ->icon('heroicon-o-calendar')
                            ->columnSpan(1)
                            ->schema([
                                Forms\Components\DateTimePicker::make('validated_at')
                                    ->seconds(false)
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->timezone('Africa/Casablanca'),
                                Forms\Components\DateTimePicker::make('received_at')
                                    ->seconds(false)
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->timezone('Africa/Casablanca'),
                                Forms\Components\DateTimePicker::make('signed_at')
                                    ->seconds(false)
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->timezone('Africa/Casablanca'),
                            ]),

                        Forms\Components\Tabs::make('Apprenticeship')
                            ->columnSpan(2)
                            ->tabs([
                                Forms\Components\Tabs\Tab::make(__('Schedule & Status'))
                                    ->icon('heroicon-o-calendar')
                                    ->schema([
                                        Forms\Components\Section::make(__('Timeline'))
                                            ->schema([
                                                Forms\Components\DateTimePicker::make('starting_at')
                                                    ->native(false)
                                                    ->displayFormat('d/m/Y'),
                                                Forms\Components\DateTimePicker::make('ending_at')
                                                    ->native(false)
                                                    ->displayFormat('d/m/Y'),
                                            ])->columns(2),
                                        Forms\Components\Section::make(__('Status'))
                                            ->schema([
                                                Forms\Components\ToggleButtons::make('status')
                                                    ->options(Enums\Status::class)
                                                    ->required()
                                                    ->inline()
                                                    ->columnSpanFull(),
                                            ]),
                                    ]),

                                Forms\Components\Tabs\Tab::make(__('Basic Information'))
                                    ->icon('heroicon-o-information-circle')
                                    ->schema([
                                        Forms\Components\Section::make(__('Organization Details'))
                                            ->schema([
                                                Forms\Components\Select::make('organization_id')
                                                    ->relationship('organization', 'name')
                                                    ->required(),
                                                Forms\Components\TextInput::make('office_location')
                                                    ->maxLength(255),
                                                Forms\Components\Select::make('internship_type')
                                                    ->options(Enums\InternshipType::class)
                                                    ->required(),
                                            ])->columns(3),

                                        Forms\Components\Section::make(__('Apprenticeship Details'))
                                            ->schema([
                                                Forms\Components\TextInput::make('title')
                                                    ->maxLength(255)
                                                    ->columnSpanFull(),
                                                Forms\Components\TagsInput::make('keywords')
                                                    ->columnSpanFull(),
                                                Forms\Components\RichEditor::make('description')
                                                    ->columnSpanFull(),
                                            ]),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.full_name')
                    ->searchable(['first_name', 'last_name'])
                    ->description(fn ($record) => $record->student?->id_pfe),

                Tables\Columns\TextColumn::make('student.program')
                    ->label('Program')
                    ->tooltip(fn ($record) => $record->student?->program->getDescription()),

                Tables\Columns\TextColumn::make('organization.name')
                    ->description(fn ($record) => $record->organization->city . ', ' . $record->organization->country)
                    ->searchable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(50)
                    ->description(fn ($record) => __('Start date') . ': ' . $record->starting_at->format('d/m/Y') . ' - ' . __('End date') . ': ' . $record->ending_at->format('d/m/Y')),

                Tables\Columns\TextColumn::make('parrain.full_name')
                    ->searchable(false)
                    ->sortable(),

                Tables\Columns\TextColumn::make('supervisor.full_name')
                    ->searchable(false)
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('internship_type')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge(),
            ])
            ->groups([
                Tables\Grouping\Group::make('student.level')
                    ->label('Level')
                    ->collapsible()
                    ->titlePrefixedWithLabel(false),
            ])
            ->defaultGroup('student.level')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('internship_type')
                    ->options(Enums\InternshipType::class)
                    ->label('Work Modality'),
                // Tables\Filters\SelectFilter::make('student_level')
                //     ->relationship('student', 'level')
                //     // ->options(Enums\StudentLevel::class)
                //     ->label('Level'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
                    ->hidden(fn () => ! auth()->user()->isAdministrator())
                    ->outlined(),
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
            'index' => Pages\ListApprenticeships::route('/'),
            'create' => Pages\CreateApprenticeship::route('/create'),
            'view' => Pages\ViewApprenticeship::route('/{record}'),
            'edit' => Pages\EditApprenticeship::route('/{record}/edit'),
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
        $verification_document_url = Storage::disk('cancellation_verification')->url($infolist->getRecord()->verification_document_url);

        return $infolist
            ->columns(12)
            ->schema([
                Infolists\Components\Tabs::make('Relations')
                    ->columns(4)
                    ->columnSpan(8)
                    ->tabs([
                        Infolists\Components\Tabs\Tab::make(__('Agreement Details'))
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Infolists\Components\Section::make(__('Basic Information'))
                                    ->icon('heroicon-o-information-circle')
                                    ->columns(3)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('student.long_full_name')
                                            ->label(__('Student'))
                                            ->icon('heroicon-o-user')
                                            ->badge(),

                                        Infolists\Components\TextEntry::make('student.program')
                                            ->label(__('Program'))
                                            ->icon('heroicon-o-academic-cap')
                                            ->badge(),

                                        Infolists\Components\TextEntry::make('organization.name')
                                            ->label(__('Organization'))
                                            ->icon('heroicon-o-building-office')
                                            ->badge()
                                            ->color('success'),
                                    ]),

                                Infolists\Components\Section::make(__('Apprenticeship Details'))
                                    ->icon('heroicon-o-briefcase')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('title')
                                            ->markdown(),
                                        Infolists\Components\TextEntry::make('description')
                                            ->markdown(),
                                        Infolists\Components\SpatieTagsEntry::make('keywords'),
                                    ]),
                            ]),

                        Infolists\Components\Tabs\Tab::make(__('Location & Schedule'))
                            ->icon('heroicon-o-map-pin')
                            ->schema([

                                Infolists\Components\Section::make(__('Location & Work Modality'))
                                    ->icon('heroicon-o-map-pin')
                                    ->columns(2)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('office_location')
                                            ->icon('heroicon-o-map-pin')
                                            ->badge(),
                                        Infolists\Components\TextEntry::make('internship_type')
                                            ->icon('heroicon-o-building-office-2')
                                            ->badge(),
                                    ]),

                                Infolists\Components\Section::make(__('Schedule'))
                                    ->icon('heroicon-o-calendar')
                                    ->schema([
                                        Infolists\Components\TextEntry::make('starting_at')
                                            ->icon('heroicon-o-calendar')
                                            ->badge(),
                                        Infolists\Components\TextEntry::make('ending_at')
                                            ->icon('heroicon-o-calendar')
                                            ->badge(),
                                    ]),
                            ]),
                    ]),

                Infolists\Components\Grid::make(4)
                    ->columnSpan(4)
                    ->schema([
                        Infolists\Components\Section::make(__('Status & Documents'))
                            ->icon('heroicon-o-document-check')
                            ->collapsible()
                            ->schema([
                                Infolists\Components\TextEntry::make('status')
                                    ->badge(),
                                // ... other status entries
                            ]),

                        Infolists\Components\Section::make(__('Important Dates'))
                            ->icon('heroicon-o-calendar')
                            ->collapsible()
                            ->schema([
                                Infolists\Components\TextEntry::make('validated_at')
                                    ->icon('heroicon-o-calendar')
                                    ->badge(),
                                Infolists\Components\TextEntry::make('received_at')
                                    ->icon('heroicon-o-calendar')
                                    ->badge(),
                                Infolists\Components\TextEntry::make('signed_at')
                                    ->icon('heroicon-o-calendar')
                                    ->badge(),
                            ]),
                    ]),
            ]);
    }
}
