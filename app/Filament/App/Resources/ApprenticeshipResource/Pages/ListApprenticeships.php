<?php

namespace App\Filament\App\Resources\ApprenticeshipResource\Pages;

use App\Filament\App\Resources\ApprenticeshipResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\Apprenticeship;
use App\Models\Year;
use Illuminate\Support\Facades\Auth;

class ListApprenticeships extends ListRecords
{
    protected static string $resource = ApprenticeshipResource::class;

    protected function getHeaderActions(): array
    {
        $currentYearId = Year::current()->id;

        // Check if student already has an agreement for the current year
        $existingAgreement = Apprenticeship::where('student_id', Auth::id())
            ->where('year_id', $currentYearId)
            ->first();

        if ($existingAgreement) {
            return [];
        }

        return [
            Actions\CreateAction::make(),
        ];
    }
}
