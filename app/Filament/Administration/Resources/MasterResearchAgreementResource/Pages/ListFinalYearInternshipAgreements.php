<?php

namespace App\Filament\Administration\Resources\MasterResearchInternshipAgreementResource\Pages;

use App\Enums;
use App\Filament\Administration\Resources\MasterResearchInternshipAgreementResource;
use App\Models\MasterResearchInternshipAgreement;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListMasterResearchInternshipAgreements extends ListRecords
{
    protected static string $resource = MasterResearchInternshipAgreementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [];
        if (auth()->user()->isAdministrator()) {

            $tabs = [
                __('All') => Tab::make(),
                __('Draft') => Tab::make()
                    ->badge(MasterResearchInternshipAgreement::where('status', Enums\Status::Draft->value)->count())
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Draft->value)),
                __('Announced') => Tab::make()
                    ->badge(MasterResearchInternshipAgreement::where('status', Enums\Status::Announced->value)->count())
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Announced->value)),
                __('Validated') => Tab::make()
                    ->badge(MasterResearchInternshipAgreement::where('status', Enums\Status::Validated->value)->count())
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Validated->value)),
                __('Completed') => Tab::make()
                    ->badge(MasterResearchInternshipAgreement::where('status', Enums\Status::Completed->value)->count())
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Completed->value)),
                __('Signed') => Tab::make()
                    ->badge(MasterResearchInternshipAgreement::where('status', Enums\Status::Signed->value)->count())
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Signed->value)),
                __('Pending Cancellation') => Tab::make()
                    ->badge(MasterResearchInternshipAgreement::where('status', Enums\Status::PendingCancellation->value)->count())
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::PendingCancellation->value)),
            ];
        }

        return $tabs;

    }
}
