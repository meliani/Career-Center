<?php

namespace App\Filament\Administration\Resources\TimetableResource\Pages;

use App\Filament\Administration\Resources\TimetableResource;
use App\Models\Timetable;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListTimetables extends ListRecords
{
    protected static string $resource = TimetableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'planned' => Tab::make('Planned')
                ->label(__('Planned'))
                ->badge(Timetable::planned()->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->planned()),
            'unplanned' => Tab::make('Unplanned')
                ->label(__('Unplanned'))
                ->badge(Timetable::unplanned()->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->unplanned()),

        ];
    }
}
