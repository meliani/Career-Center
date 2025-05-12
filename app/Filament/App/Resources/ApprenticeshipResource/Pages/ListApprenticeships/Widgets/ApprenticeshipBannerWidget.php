<?php

namespace App\Filament\App\Resources\ApprenticeshipResource\Pages\ListApprenticeships\Widgets;

use App\Models\Apprenticeship;
use App\Models\Year;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class ApprenticeshipBannerWidget extends Widget
{
    protected static string $view = 'filament.app.resources.apprenticeship-resource.pages.list-apprenticeships.widgets.apprenticeship-banner-widget';

    protected int | string | array $columnSpan = 'full';
    
    public function getAcademicYear(): string
    {
        return Year::current()->title;
    }
    
    public function hasExistingAgreement(): bool
    {
        $currentYearId = Year::current()->id;
        return Apprenticeship::where('student_id', Auth::id())
            ->where('year_id', $currentYearId)
            ->exists();
    }
}
