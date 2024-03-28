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
        $this->InternshipAgreementByStatus = InternshipAgreement::select('status', \DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            __('All') => Tab::make(),
            // __('Draft') => Tab::make()
            //     // ->badge(fn (Builder $query) => $query->where('status', Enums\Status::Draft)->count(), 'bg-blue-500 text-white')
            //     ->badge($this->InternshipAgreementByStatus[Enums\Status::Draft->value] ?? 0, 'bg-blue-500 text-white')
            //     ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Draft)),
            //     __('Announced') => Tab::make()
            //     ->badge($this->InternshipAgreementByStatus[Enums\Status::Announced->value] ?? 0)
            //     ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Announced)),
            // __('Validated') => Tab::make()
            //     ->badge($this->InternshipAgreementByStatus[Enums\Status::Validated->value] ?? 0)
            //     ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Validated)),
            // __('Completed') => Tab::make()
            //     ->badge($this->InternshipAgreementByStatus[Enums\Status::Completed->value] ?? 0)
            //     ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Completed)),
            // __('Signed') => Tab::make()
            //     ->badge($this->InternshipAgreementByStatus[Enums\Status::Signed->value] ?? 0)
            //     ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Signed)),
            __('Draft') => Tab::make()
                ->badge(InternshipAgreement::where('status', Enums\Status::Draft->value)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Draft->value)),
            __('Announced') => Tab::make()
                ->badge(InternshipAgreement::where('status', Enums\Status::Announced->value)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Announced->value)),
            __('Validated') => Tab::make()
                ->badge(InternshipAgreement::where('status', Enums\Status::Validated->value)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Validated->value)),
            __('Completed') => Tab::make()
                ->badge(InternshipAgreement::where('status', Enums\Status::Completed->value)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Completed->value)),
            __('Signed') => Tab::make()
                ->badge(InternshipAgreement::where('status', Enums\Status::Signed->value)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Signed->value)),

        ];
    }
}
