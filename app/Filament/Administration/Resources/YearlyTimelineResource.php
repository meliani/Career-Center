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
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
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
                Forms\Components\Select::make('year_id')
                    ->relationship('year', 'title')
                    ->required(),

                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull(),

                Forms\Components\DateTimePicker::make('start_date')
                    ->required(),

                Forms\Components\DateTimePicker::make('end_date'),

                Forms\Components\ColorPicker::make('color'),

                Forms\Components\TextInput::make('icon')
                    ->placeholder('heroicon-o-academic-cap'),

                Forms\Components\Select::make('category')
                    ->options(
                        collect(TimelineCategory::cases())->pluck('value', 'value')
                    )
                    ->label('Category'),

                Forms\Components\Select::make('priority')
                    ->options(
                        collect(TimelinePriority::cases())->pluck('value', 'value')
                    )
                    ->label('Priority'),

                Forms\Components\Select::make('status')
                    ->options(
                        collect(TimelineStatus::cases())->pluck('value', 'value')
                    )
                    ->label('Status'),

                Checkbox::make('is_highlight')
                    ->label('Highlight Event')
                    ->default(false),

                MultiSelect::make('assigned_users')
                    ->relationship('assignedUsers', 'name')
                    ->searchable()
                    ->label('Assigned Users'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('year.title')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    // ->formatStateUsing(fn ($state) => TimelineCategory::from($state)->getLabel())
                    ->badge(),
                Tables\Columns\TextColumn::make('priority')
                    // ->formatStateUsing(fn ($state) => TimelinePriority::from($state)->getLabel())
                    ->badge()
                    ->colors([
                        'danger' => TimelinePriority::Critical->value,
                        'warning' => TimelinePriority::High->value,
                        'success' => TimelinePriority::Medium->value,
                        'gray' => TimelinePriority::Low->value,
                    ]),
                Tables\Columns\TextColumn::make('status')
                    // ->formatStateUsing(fn ($state) => TimelineStatus::from($state)->getLabel())
                    ->badge(),
                BooleanColumn::make('is_highlight')
                    ->label('Highlight')
                    ->sortable(),
                TextColumn::make('assignedUsers.name')
                    ->label('Assigned Users')
                    ->limit(2)
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year_id')
                    ->relationship('year', 'title'),
                Tables\Filters\SelectFilter::make('category'),
                Tables\Filters\SelectFilter::make('status'),
                Tables\Filters\SelectFilter::make('priority'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListYearlyTimelines::route('/'),
            'create' => Pages\CreateYearlyTimeline::route('/create'),
            'edit' => Pages\EditYearlyTimeline::route('/{record}/edit'),
        ];
    }
}
