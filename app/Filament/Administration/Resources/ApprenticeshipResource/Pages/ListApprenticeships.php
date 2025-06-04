<?php

namespace App\Filament\Administration\Resources\ApprenticeshipResource\Pages;

use App\Enums;
use App\Filament\Administration\Resources\ApprenticeshipResource;
use App\Models\Apprenticeship;
use App\Models\Year;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListApprenticeships extends ListRecords
{
    protected static string $resource = ApprenticeshipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $currentYear = Year::current();
        
        return [
            __('All') => Tab::make()
                ->badge(Apprenticeship::where('year_id', $currentYear->id)->count()),

            __('Draft') => Tab::make()
                ->badge(Apprenticeship::where('year_id', $currentYear->id)->where('status', Enums\Status::Draft->value)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Draft->value)),
            __('Announced') => Tab::make()
                ->badge(Apprenticeship::where('year_id', $currentYear->id)->where('status', Enums\Status::Announced->value)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Announced->value)),
            __('Validated') => Tab::make()
                ->badge(Apprenticeship::where('year_id', $currentYear->id)->where('status', Enums\Status::Validated->value)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Validated->value)),
            __('Completed') => Tab::make()
                ->badge(Apprenticeship::where('year_id', $currentYear->id)->where('status', Enums\Status::Completed->value)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Completed->value)),
            __('Signed') => Tab::make()
                ->badge(Apprenticeship::where('year_id', $currentYear->id)->where('status', Enums\Status::Signed->value)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Signed->value)),
            __('Pending cancellation') => Tab::make()
                ->badge(Apprenticeship::where('year_id', $currentYear->id)->where('status', Enums\Status::PendingCancellation->value)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::PendingCancellation->value)),

        ];
    }
}
