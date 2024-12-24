<?php

namespace App\Filament\Administration\Resources;

use App\Enums\TimelineCategory;
use App\Enums\TimelinePriority;
use App\Enums\TimelineStatus;
use App\Filament\Administration\Resources\YearlyTimelineResource\Pages;
use App\Filament\Core\BaseResource;
use App\Models\YearlyTimeline;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Table;

class YearlyTimelineResource extends BaseResource
{
    protected static ?string $model = YearlyTimeline::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $modelLabel = 'Timeline Event';

    protected static ?string $pluralModelLabel = 'Timeline Events';

    protected static ?string $navigationGroup = 'Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    Section::make('Basic Information')
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),

                            Forms\Components\Select::make('year_id')
                                ->relationship('year', 'title')
                                ->required(),

                            Forms\Components\Select::make('status')
                                ->options(
                                    collect(TimelineStatus::cases())->pluck('value', 'value')
                                )
                                ->label('Status')
                                ->default(TimelineStatus::Pending->value),
                        ])->columnSpan(1),

                    Section::make('Timeline Details')
                        ->schema([
                            Forms\Components\DateTimePicker::make('start_date')
                                ->required()
                                ->timezone('Europe/Paris'),

                            Forms\Components\DateTimePicker::make('end_date')
                                ->timezone('Europe/Paris')
                                ->after('start_date'),

                            Forms\Components\Select::make('priority')
                                ->options(
                                    collect(TimelinePriority::cases())->pluck('value', 'value')
                                )
                                ->label('Priority')
                                ->default(TimelinePriority::Medium->value),
                        ])->columnSpan(1),
                ]),

                Section::make('Event Configuration')
                    ->schema([
                        Forms\Components\Select::make('category')
                            ->options(
                                collect(TimelineCategory::cases())->pluck('value', 'value')
                            )
                            ->label('Category'),

                        Forms\Components\ColorPicker::make('color')
                            ->hex(),

                        Forms\Components\TextInput::make('icon')
                            ->placeholder('heroicon-o-academic-cap'),

                        Checkbox::make('is_highlight')
                            ->label('Highlight Event')
                            ->helperText('Highlighted events will be more prominent in the timeline view')
                            ->default(false),
                    ])->columns(4),

                Section::make('Description & Assignment')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),

                        MultiSelect::make('assigned_users')
                            ->relationship('assignedUsers', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Assigned Users'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->wrap()
                    ->limit(50),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->tooltip(function ($record): string {
                        if (! $record->end_date) {
                            return __('No end date set');
                        }

                        return __('Ends: :date', [
                            'date' => $record->end_date->translatedFormat('d M Y'),
                        ]);
                    })
                    ->description(
                        fn ($record) => $record->end_date ?
                        __('Until :date', ['date' => $record->end_date->translatedFormat('d M Y')]) :
                        null
                    ),
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color(fn (TimelineCategory $state): string => $state->getColor()),
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn (TimelinePriority $state): string => match ($state) {
                        TimelinePriority::Critical => 'danger',
                        TimelinePriority::High => 'warning',
                        TimelinePriority::Medium => 'success',
                        TimelinePriority::Low => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (TimelineStatus $state): string => $state->getColor()),
                BooleanColumn::make('is_highlight')
                    ->label('★')
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\ViewColumn::make('assignedUsers')
                    ->label('Assigned Users')
                    ->view('filament.tables.columns.avatar-group')
                    ->searchable(false)
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('assignedUsers_count')
                    ->counts('assignedUsers')
                    ->label('Total Assigned')
                    ->searchable(false)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('start_date', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('year_id')
                    ->relationship('year', 'title'),
                Tables\Filters\SelectFilter::make('category'),
                Tables\Filters\SelectFilter::make('status'),
                Tables\Filters\SelectFilter::make('priority'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Event Details')
                    ->schema([
                        Components\TextEntry::make('title')
                            ->size(Components\TextEntry\TextEntrySize::Large),
                        Components\TextEntry::make('description')
                            ->markdown()
                            ->columnSpanFull(),
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('start_date')
                                    ->dateTime('d M Y')
                                    ->timezone('Europe/Paris')
                                    ->label(__('Starts at'))
                                    ->icon('heroicon-o-calendar')
                                    ->color('primary'),
                                Components\TextEntry::make('end_date')
                                    ->dateTime('d M Y')
                                    ->timezone('Europe/Paris')
                                    ->label(__('Ends at'))
                                    ->icon('heroicon-o-arrow-right')
                                    ->color(fn ($state) => $state ? 'gray' : 'danger'),
                                Components\TextEntry::make('year.title')
                                    ->label(__('Academic Year'))
                                    ->icon('heroicon-o-academic-cap'),
                            ]),
                    ]),

                Components\Section::make('Event Configuration')
                    ->schema([
                        Components\Grid::make(4)
                            ->schema([
                                Components\TextEntry::make('category')
                                    ->badge(),
                                Components\TextEntry::make('priority')
                                    ->badge()
                                    ->color(fn (TimelinePriority $state): string => match ($state) {
                                        TimelinePriority::Critical => 'danger',
                                        TimelinePriority::High => 'warning',
                                        TimelinePriority::Medium => 'success',
                                        TimelinePriority::Low => 'gray',
                                    }),
                                Components\TextEntry::make('status')
                                    ->badge(),
                                Components\IconEntry::make('is_highlight')
                                    ->icon(fn (bool $state): string => $state ? 'heroicon-o-star' : 'heroicon-o-no-symbol')
                                    ->color(fn (bool $state): string => $state ? 'warning' : 'gray'),
                            ]),
                    ]),

                Components\ViewEntry::make('assignedUsers')
                    ->view('filament.infolists.components.avatar-group'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListYearlyTimelines::route('/'),
            'create' => Pages\CreateYearlyTimeline::route('/create'),
            'edit' => Pages\EditYearlyTimeline::route('/{record}/edit'),
            'view' => Pages\ViewYearlyTimeline::route('/{record}'),
        ];
    }
}
