<?php

namespace App\Filament\Administration\Resources;

use Croustibat\FilamentJobsMonitor\FilamentJobsMonitorPlugin;
use Croustibat\FilamentJobsMonitor\Models\QueueMonitor;
use Croustibat\FilamentJobsMonitor\Resources\QueueMonitorResource as QueueMonitorResourceParent;
use Croustibat\FilamentJobsMonitor\Resources\QueueMonitorResource\Pages;
use Croustibat\FilamentJobsMonitor\Resources\QueueMonitorResource\Widgets\QueueStatsOverview;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class QueueMonitorResource extends QueueMonitorResourceParent
{
    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Database Jobs';

    public static function getNavigationLabel(): string
    {
        return __(self::$navigationLabel);
    }

    public static function getnavigationGroup(): string
    {
        return __(self::$navigationGroup);
    }
    // protected static ?string $model = QueueMonitor::class;

    // public static function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             TextInput::make('job_id')
    //                 ->required()
    //                 ->maxLength(255),
    //             TextInput::make('name')
    //                 ->maxLength(255),
    //             TextInput::make('queue')
    //                 ->maxLength(255),
    //             DateTimePicker::make('started_at'),
    //             DateTimePicker::make('finished_at'),
    //             Toggle::make('failed')
    //                 ->required(),
    //             TextInput::make('attempt')
    //                 ->required(),
    //             Textarea::make('exception_message')
    //                 ->maxLength(65535),
    //         ]);
    // }

    // public static function table(Table $table): Table
    // {
    //     return $table
    //         ->columns([
    //             TextColumn::make('status')
    //                 ->badge()
    //                 ->label(__('filament-jobs-monitor::translations.status'))
    //                 ->formatStateUsing(fn (string $state): string => __("filament-jobs-monitor::translations.{$state}"))
    //                 ->color(fn (string $state): string => match ($state) {
    //                     'running' => 'primary',
    //                     'succeeded' => 'success',
    //                     'failed' => 'danger',
    //                 }),
    //             TextColumn::make('name')
    //                 ->label(__('filament-jobs-monitor::translations.name'))
    //                 ->sortable(),
    //             TextColumn::make('queue')
    //                 ->label(__('filament-jobs-monitor::translations.queue'))
    //                 ->sortable(),
    //             TextColumn::make('progress')
    //                 ->label(__('filament-jobs-monitor::translations.progress'))
    //                 ->formatStateUsing(fn (string $state) => "{$state}%")
    //                 ->sortable(),
    //             TextColumn::make('started_at')
    //                 ->label(__('filament-jobs-monitor::translations.started_at'))
    //                 ->since()
    //                 ->sortable(),
    //         ])
    //         ->defaultSort('started_at', 'desc')
    //         ->bulkActions([
    //             DeleteBulkAction::make(),
    //         ])
    //         ->filters([
    //             SelectFilter::make('status')
    //                 ->options([
    //                     'running' => 'Running',
    //                     'succeeded' => 'Succeeded',
    //                     'failed' => 'Failed',
    //                 ])
    //                 ->query(function (Builder $query, array $data) {
    //                     if ($data['value'] === 'succeeded') {
    //                         return $query
    //                             ->whereNotNull('finished_at')
    //                             ->where('failed', 0);
    //                     } elseif ($data['value'] === 'failed') {
    //                         return $query
    //                             ->whereNotNull('finished_at')
    //                             ->where('failed', 1);
    //                     } elseif ($data['value'] === 'running') {
    //                         return $query
    //                             ->whereNull('finished_at');
    //                     }
    //                 }),
    //         ]);
    // }

    // public static function getNavigationBadge(): ?string
    // {
    //     return FilamentJobsMonitorPlugin::get()->getNavigationCountBadge() ? number_format(static::getModel()::count()) : null;
    // }

    // public static function getModelLabel(): string
    // {
    //     return FilamentJobsMonitorPlugin::get()->getLabel();
    // }

    // public static function getPluralModelLabel(): string
    // {
    //     return FilamentJobsMonitorPlugin::get()->getPluralLabel();
    // }

    // public static function getNavigationLabel(): string
    // {
    //     return Str::title(static::getPluralModelLabel()) ?? Str::title(static::getModelLabel());
    // }

    // public static function getNavigationGroup(): ?string
    // {
    //     return FilamentJobsMonitorPlugin::get()->getNavigationGroup();
    // }

    // public static function getNavigationSort(): ?int
    // {
    //     return FilamentJobsMonitorPlugin::get()->getNavigationSort();
    // }

    // public static function getBreadcrumb(): string
    // {
    //     return FilamentJobsMonitorPlugin::get()->getBreadcrumb();
    // }

    // public static function shouldRegisterNavigation(): bool
    // {
    //     return FilamentJobsMonitorPlugin::get()->shouldRegisterNavigation();
    // }

    // public static function getNavigationIcon(): string
    // {
    //     return FilamentJobsMonitorPlugin::get()->getNavigationIcon();
    // }

    // public static function getPages(): array
    // {
    //     return [
    //         'index' => Pages\ListQueueMonitors::route('/'),
    //     ];
    // }

    // public static function getWidgets(): array
    // {
    //     return [
    //         QueueStatsOverview::class,
    //     ];
    // }
}
