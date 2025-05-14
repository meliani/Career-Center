<?php

namespace App\Filament\Administration\Resources\FinalYearInternshipAgreementResource\Pages;

use App\Enums;
use App\Filament\Actions\Page\GenerateFinalYearInternshipAgreementPageAction;
use App\Filament\Administration\Resources\FinalYearInternshipAgreementResource;
use App\Models\FinalYearInternshipAgreement;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\ActionSize;

class ViewFinalYearInternshipAgreement extends ViewRecord
{
    protected static string $resource = FinalYearInternshipAgreementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->size(ActionSize::Small),
                
            GenerateFinalYearInternshipAgreementPageAction::make('generate_agreement_pdf')
                ->label(__('Generate Agreement PDF'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('primary')
                ->size(ActionSize::Small)
                ->visible(fn (FinalYearInternshipAgreement $record) => $record->status == Enums\Status::Announced || $record->status == Enums\Status::Validated)
                ->record(fn () => $this->getRecord()),
                
            GenerateFinalYearInternshipAgreementPageAction::make('generate_draft_agreement_pdf')
                ->label(__('Generate Draft PDF'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('warning')
                ->size(ActionSize::Small)
                ->visible(fn (FinalYearInternshipAgreement $record) => $record->status == Enums\Status::Draft)
                ->record(fn () => $this->getRecord()),
            
            Actions\Action::make('download_agreement')
                ->label(__('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->size(ActionSize::Small)
                ->url(fn ($record) => $record->pdf_path ? asset($record->pdf_path . '/' . $record->pdf_file_name) : null, true)
                ->visible(fn ($record) => !empty($record->pdf_path) && !empty($record->pdf_file_name)),
        ];
    }
}
