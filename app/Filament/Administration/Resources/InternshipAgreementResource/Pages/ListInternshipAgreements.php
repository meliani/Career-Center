<?php

namespace App\Filament\Administration\Resources\InternshipAgreementResource\Pages;

use App\Filament\Administration\Resources\InternshipAgreementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Hydrat\TableLayoutToggle\Concerns\HasToggleableTable;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Enums;


class ListInternshipAgreements extends ListRecords
{
    use HasToggleableTable;

    protected static string $resource = InternshipAgreementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'draft' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Draft)),
            'announced' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Announced)),
                'validated' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Validated)),
                'signed' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Signed)),

        ];
    }
}
