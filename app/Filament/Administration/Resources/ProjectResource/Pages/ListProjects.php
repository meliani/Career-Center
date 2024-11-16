<?php

namespace App\Filament\Administration\Resources\ProjectResource\Pages;

use App\Enums\Program;
use App\Filament\Administration\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Hydrat\TableLayoutToggle\Concerns\HasToggleableTable;

class ListProjects extends ListRecords
{
    use HasToggleableTable;

    protected static string $resource = ProjectResource::class;

    public function getDefaultLayoutView(): string
    {
        return 'list';
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $baseQuery = static::getResource()::getEloquentQuery();
        $tabs = [];

        foreach (Program::cases() as $program) {
            $label = $program->getLabel();
            $value = $program->value;

            $tabs[$value] = Tab::make($label)
                ->badge(
                    $baseQuery->clone()
                        ->whereHas('students', fn ($q) => $q->where('program', $value))
                        ->count()
                )
                ->modifyQueryUsing(
                    fn ($query) => $query->whereHas(
                        'students',
                        fn ($q) => $q->where('program', $value)
                    )
                );
        }

        // Add 'all' tab with total count from base query
        $tabs = array_merge([
            'all' => Tab::make('All')->badge($baseQuery->count()),
        ], $tabs);

        return $tabs;
    }
}
