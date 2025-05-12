<?php

namespace App\Filament\App\Resources\ApprenticeshipResource\Pages;

use App\Enums\Status;
use App\Filament\Actions\Action\ApplyForCancelInternshipAction;
use App\Filament\Actions\Action\AddApprenticeshipAmendmentAction;
use App\Filament\Actions\Action\Processing\GenerateApprenticeshipAgreementAction;
use App\Filament\App\Resources\ApprenticeshipResource;
use Filament\Actions;
use Filament\Infolists\Components\Actions\Action as InfoAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;

class ViewApprenticeship extends ViewRecord
{
    protected static string $resource = ApprenticeshipResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('Apprenticeship Agreement Details');
    }

    public function getSubheading(): string|Htmlable|null
    {
        $organization = $this->record->organization->name;
        $period = $this->record->starting_at->format('M d, Y') . ' to ' . $this->record->ending_at->format('M d, Y');
        return "{$organization} • {$period}";
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label(__('Edit details'))
                ->icon('heroicon-o-pencil')
                ->color('warning')
                ->visible(fn () => $this->record->status === Status::Draft),
                
            AddApprenticeshipAmendmentAction::make('request_amendment')
                ->label(__('Request Amendment'))
                ->icon('heroicon-o-pencil-square')
                ->color('warning')
                ->visible(fn () => $this->record->status !== Status::Draft && 
                                   $this->record->status !== Status::PendingCancellation && 
                                   !$this->record->hasPendingAmendmentRequests()),
                
            // ApplyForCancelInternshipAction::make('Apply for cancellation')
            //     ->label(__('Apply for cancellation'))
            //     ->icon('heroicon-o-bolt-slash')
            //     ->color('danger')
            //     ->visible(fn () => $this->record->status !== Status::Draft && $this->record->status !== Status::PendingCancellation),
                
            // GenerateApprenticeshipAgreementAction::make('generate_agreement')
            //     ->label(__('Generate Agreement PDF'))
            //     ->icon('heroicon-o-document-arrow-down')
            //     ->color('primary')
            //     ->visible(fn () => $this->record->status == Status::Announced || $this->record->status == Status::Validated),
                
            // GenerateApprenticeshipAgreementAction::make('generate_draft_agreement')
            //     ->label(__('Generate Draft Agreement PDF'))
            //     ->icon('heroicon-o-document-arrow-down')
            //     ->color('primary')
            //     ->visible(fn () => $this->record->status == Status::Draft),
        ];
    }
    
    protected function getHeaderActionsMaxWidth(): MaxWidth
    {
        return MaxWidth::Large;
    }
    
    protected function getFooterWidgets(): array
    {
        return [
            ViewApprenticeship\Widgets\ApprenticeshipStatusWidget::class,
        ];
    }
}
