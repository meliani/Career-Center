<?php

namespace App\Filament\App\Resources\InternshipOfferResource\Pages;

use App\Filament\App\Resources\InternshipOfferResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInternshipOffer extends ViewRecord
{
    protected static string $resource = InternshipOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
            Actions\Action::make('rate')
                ->label('Rate')
                ->icon('heroicon-o-star')
                ->form(
                    [
                        \Mokhosh\FilamentRating\Components\Rating::make('rating')
                            ->label('Rating')
                            ->required()
                            ->theme(\Mokhosh\FilamentRating\RatingTheme::HalfStars)
                            ->allowZero(),
                    ]
                )
                ->action(function (array $data) {
                    $this->record->rate($data['rating']);
                })
                ->disabled(fn () => ($this->record->timesRated() > 0)),
        ];
    }
}
