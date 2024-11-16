<?php

namespace App\Filament\Administration\Resources\InternshipAgreementResource\Pages;

use App\Enums;
use App\Filament\Administration\Resources\InternshipAgreementResource;
use App\Models\InternshipAgreement;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Hydrat\TableLayoutToggle\Concerns\HasToggleableTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ListInternshipAgreements extends ListRecords
{
    use HasToggleableTable;

    public Collection $InternshipAgreementByStatus;

    protected static string $resource = InternshipAgreementResource::class;

    public function __construct()
    {
        // $this->InternshipAgreementByStatus = InternshipAgreement::select('status', \DB::raw('count(*) as total'))
        //     ->groupBy('status')
        //     ->pluck('total', 'status');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $baseQuery = static::getResource()::getEloquentQuery();

        return [
            __('All') => Tab::make(),
            __('Draft') => Tab::make()
                ->badge(
                    $baseQuery->clone()
                        ->where('status', Enums\Status::Draft->value)
                        ->count()
                )
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Draft->value)),
            __('Announced') => Tab::make()
                ->badge(
                    $baseQuery->clone()
                        ->where('status', Enums\Status::Announced->value)
                        ->count()
                )
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Announced->value)),
            __('Validated') => Tab::make()
                ->badge(
                    $baseQuery->clone()
                        ->where('status', Enums\Status::Validated->value)
                        ->count()
                )
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Validated->value)),
            __('Completed') => Tab::make()
                ->badge(
                    $baseQuery->clone()
                        ->where('status', Enums\Status::Completed->value)
                        ->count()
                )
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Completed->value)),
            __('Signed') => Tab::make()
                ->badge(
                    $baseQuery->clone()
                        ->where('status', Enums\Status::Signed->value)
                        ->count()
                )
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Signed->value)),
        ];
    }
}
