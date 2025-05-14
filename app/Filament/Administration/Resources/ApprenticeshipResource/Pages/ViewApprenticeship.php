<?php

namespace App\Filament\Administration\Resources\ApprenticeshipResource\Pages;

use App\Enums;
use App\Filament\Actions\Page\GenerateApprenticeshipAgreementPageAction;
use App\Filament\Administration\Resources\ApprenticeshipResource;
use App\Models\Apprenticeship;
use Filament;
use Filament\Resources\Pages\ViewRecord;

class ViewApprenticeship extends ViewRecord
{
    protected static string $resource = ApprenticeshipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Filament\Actions\Action::make('edit_page')
                ->label(__('Switch to edit mode'))
                ->icon('heroicon-o-pencil')
                ->size(Filament\Support\Enums\ActionSize::Small)
                ->tooltip('Edit apprenticeship details')
                ->url(fn ($record) => \App\Filament\Administration\Resources\ApprenticeshipResource::getUrl('edit', ['record' => $record]))
                ->hidden(fn () => ! auth()->user()->isAdministrator()),
                
            GenerateApprenticeshipAgreementPageAction::make('generate_agreement_pdf')
                ->label(__('Generate Agreement PDF'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('primary')
                ->size(Filament\Support\Enums\ActionSize::Small)
                ->visible(fn (Apprenticeship $record) => $record->status == Enums\Status::Announced || $record->status == Enums\Status::Validated)
                ->record(fn () => $this->getRecord()),
                
            GenerateApprenticeshipAgreementPageAction::make('generate_draft_agreement_pdf')
                ->label(__('Generate Draft PDF'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('warning')
                ->size(Filament\Support\Enums\ActionSize::Small)
                ->visible(fn (Apprenticeship $record) => $record->status == Enums\Status::Draft)
                ->record(fn () => $this->getRecord()),
            
            Filament\Actions\Action::make('download_agreement')
                ->label(__('Download PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->size(Filament\Support\Enums\ActionSize::Small)
                ->url(fn ($record) => $record->pdf_path ? asset($record->pdf_path . '/' . $record->pdf_file_name) : null, true)
                ->visible(fn ($record) => !empty($record->pdf_path) && !empty($record->pdf_file_name)),
        ];
    }
}
