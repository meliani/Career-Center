<?php

namespace App\Filament\Administration\Resources\InternshipAgreementResource\Pages;

use App\Filament\Administration\Resources\InternshipAgreementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Hydrat\TableLayoutToggle\Concerns\HasToggleableTable;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Enums;
use Illuminate\Support\Collection;
use App\Models\InternshipAgreement;

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
            'all' => Tab::make(),
            'draft' => Tab::make('In progress')
                // ->badge(fn (Builder $query) => $query->where('status', Enums\Status::Draft)->count(), 'bg-blue-500 text-white')
                ->badge($this->InternshipAgreementByStatus[Enums\Status::Draft->value] ?? 0, 'bg-blue-500 text-white')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Draft)),
            'announced' => Tab::make()
                ->badge($this->InternshipAgreementByStatus[Enums\Status::Announced->value] ?? 0)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Announced)),
            'validated' => Tab::make()
                ->badge($this->InternshipAgreementByStatus[Enums\Status::Validated->value] ?? 0)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Validated)),
            'signed' => Tab::make()
                ->badge($this->InternshipAgreementByStatus[Enums\Status::Signed->value] ?? 0)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Enums\Status::Signed)),

        ];
    }
}
