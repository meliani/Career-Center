<?php

namespace App\Filament\Administration\Resources\InternshipOfferResource\Pages;

use App\Enums\InternshipLevel;
use App\Filament\Administration\Resources\InternshipOfferResource;
use App\Models\InternshipOffer;
use App\Models\Year;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListInternshipOffers extends ListRecords
{
    protected static string $resource = InternshipOfferResource::class;

    public array $InternshipOffersByInternshipLevel = [];

    public function mount(): void
    {
        // Populate the property with the count of internship offers for each level
        $this->InternshipOffersByInternshipLevel = InternshipOffer::query()
            ->selectRaw('internship_level, COUNT(*) as count')
            ->where('year_id', Year::current()->id)
            ->groupBy('internship_level')
            ->pluck('count', 'internship_level')
            ->toArray();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make(__('Create Internship Offer'))
                ->icon('heroicon-o-plus')
                ->url('/publish-internship', shouldOpenInNewTab: true),
        ];
    }

    public function getTabs(): array
    {
        // tabs by internship level
        $tabs = [];

        $tabs['all'] = Tab::make('All');

        foreach (InternshipLevel::getArray() as $level) {
            $levelLabel = $level->getLabel();
            $tabs[$levelLabel] = Tab::make(__($levelLabel))
                ->badge($this->InternshipOffersByInternshipLevel[$level->value] ?? 0)
                ->modifyQueryUsing(fn ($query) => $query->where('internship_level', $level));
        }

        return $tabs;
    }
}
