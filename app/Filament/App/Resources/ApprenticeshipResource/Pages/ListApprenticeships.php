<?php

namespace App\Filament\App\Resources\ApprenticeshipResource\Pages;

use App\Enums\Status;
use App\Filament\App\Resources\ApprenticeshipResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\Apprenticeship;
use App\Models\Year;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\View\View;
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
            Actions\CreateAction::make()
                ->label(__('Create apprenticeship agreement'))
                ->icon('heroicon-o-plus-circle')
                ->extraAttributes(['class' => 'filament-button-create']),
        ];
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            ListApprenticeships\Widgets\ApprenticeshipBannerWidget::class,
        ];
    }
    
    protected function getHeaderActionsMaxWidth(): MaxWidth
    {
        return MaxWidth::ExtraLarge;
    }
    
    protected function getInfoAlert(): ?View
    {
        $currentYearId = Year::current()->id;
        $existingAgreement = Apprenticeship::where('student_id', Auth::id())
            ->where('year_id', $currentYearId)
            ->where('status', Status::Draft)
            ->first();
            
        if ($existingAgreement) {
            return view('filament.app.resources.apprenticeship-resource.pages.list-apprenticeships.info-alert', [
                'message' => __('You have a draft apprenticeship agreement. Complete and announce it to start the validation process.'),
                'type' => 'info',
                'icon' => 'heroicon-o-information-circle',
            ]);
        }
        
        return null;
    }
    
    protected function getWarningAlert(): ?View
    {
        $deadlineExceeded = false; // You can implement logic to check if submission deadline is approaching
        
        if ($deadlineExceeded) {
            return view('filament.app.resources.apprenticeship-resource.pages.list-apprenticeships.info-alert', [
                'message' => __('The deadline for submitting your apprenticeship agreement is approaching. Please submit it as soon as possible.'),
                'type' => 'warning',
                'icon' => 'heroicon-o-exclamation-triangle',
            ]);
        }
        
        return null;
    }
}
