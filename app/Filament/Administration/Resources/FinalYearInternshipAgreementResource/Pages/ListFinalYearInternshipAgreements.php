<?php

namespace App\Filament\Administration\Resources\FinalYearInternshipAgreementResource\Pages;

use App\Enums;
use App\Filament\Administration\Resources\FinalYearInternshipAgreementResource;
use App\Models\FinalYearInternshipAgreement;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListFinalYearInternshipAgreements extends ListRecords
{
    protected static string $resource = FinalYearInternshipAgreementResource::class;

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
            __('Draft') => Tab::make()
                ->badge(FinalYearInternshipAgreement::where('status', Enums\Status::Draft->value)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Draft->value)),
            __('Announced') => Tab::make()
                ->badge(FinalYearInternshipAgreement::where('status', Enums\Status::Announced->value)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Announced->value)),
            __('Validated') => Tab::make()
                ->badge(FinalYearInternshipAgreement::where('status', Enums\Status::Validated->value)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Validated->value)),
            __('Completed') => Tab::make()
                ->badge(FinalYearInternshipAgreement::where('status', Enums\Status::Completed->value)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Completed->value)),
            __('Signed') => Tab::make()
                ->badge(FinalYearInternshipAgreement::where('status', Enums\Status::Signed->value)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Signed->value)),
        ];
    }
}
