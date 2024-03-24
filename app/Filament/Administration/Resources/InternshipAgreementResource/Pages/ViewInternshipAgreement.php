<?php

namespace App\Filament\Administration\Resources\InternshipAgreementResource\Pages;

use App\Filament\Administration\Resources\InternshipAgreementResource;
use Filament\Resources\Pages\ViewRecord;
use Pboivin\FilamentPeek\Pages\Concerns\HasBuilderPreview;
use Pboivin\FilamentPeek\Pages\Concerns\HasPreviewModal;

class ViewInternshipAgreement extends ViewRecord
{
    use HasBuilderPreview;
    use HasPreviewModal;

    protected static string $resource = InternshipAgreementResource::class;

    // art make:filament-page ViewInternshipAgreement --resource=InternshipAgreementResource --type=ViewRecord
}
