<?php

namespace App\Filament\App\Resources\FinalYearInternshipAgreementResource\Pages;

use App\Filament\App\Resources\FinalYearInternshipAgreementResource;
use App\Models\FinalYearInternshipAgreement;
use App\Models\Year;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListFinalYearInternshipAgreements extends ListRecords
{
    protected static string $resource = FinalYearInternshipAgreementResource::class;

    protected function getHeaderActions(): array
    {
        $currentYearId = Year::current()->id;

        // Check if student already has an agreement for the current year
        $existingAgreement = FinalYearInternshipAgreement::where('student_id', Auth::id())
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
