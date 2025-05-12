<?php

namespace App\Filament\App\Resources\ApprenticeshipResource\Pages\ViewApprenticeship\Widgets;

use App\Enums\Status;
use App\Models\Apprenticeship;
use Filament\Widgets\Widget;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\IconPosition;

class ApprenticeshipStatusWidget extends Widget
{
    protected static string $view = 'filament.app.resources.apprenticeship-resource.pages.view-apprenticeship.widgets.apprenticeship-status-widget';
    
    public Apprenticeship $record;

    protected int | string | array $columnSpan = 'full';
    
    public function getStatus(): string
    {
        return $this->record->status->getLabel();
    }
    
    public function getStatusColor(): string
    {
        return $this->record->status->getColor();
    }
    
    public function getStatusIcon(): string
    {
        return $this->record->status->getIcon();
    }
    
    public function getSteps(): array
    {
        // Get the current status to determine progress
        $status = $this->record->status;
        
        $steps = [
            'draft' => [
                'label' => __('Draft'),
                'description' => __('Agreement is being prepared'),
                'complete' => true, // Draft is always complete since we have a record
                'active' => $status === Status::Draft,
                'icon' => 'heroicon-o-pencil',
            ],
            'announced' => [
                'label' => __('Announced'),
                'description' => __('Agreement has been submitted for validation'),
                'complete' => in_array($status, [Status::Announced, Status::Validated, Status::Signed, Status::Completed]),
                'active' => $status === Status::Announced,
                'icon' => 'heroicon-o-sparkles',
            ],
            'validated' => [
                'label' => __('Validated'), 
                'description' => __('Agreement has been approved by the institution'),
                'complete' => in_array($status, [Status::Validated, Status::Signed, Status::Completed]),
                'active' => $status === Status::Validated,
                'icon' => 'heroicon-o-check-circle',
            ],
            'signed' => [
                'label' => __('Signed'),
                'description' => __('Agreement has been signed by all parties'),
                'complete' => in_array($status, [Status::Signed, Status::Completed]),
                'active' => $status === Status::Signed,
                'icon' => 'heroicon-o-document-check',
            ],
            'completed' => [
                'label' => __('Completed'),
                'description' => __('Apprenticeship has been completed'),
                'complete' => $status === Status::Completed,
                'active' => $status === Status::Completed,
                'icon' => 'heroicon-o-trophy',
            ],
        ];
        
        // Handle special statuses
        if ($status === Status::PendingCancellation) {
            $steps['pending_cancellation'] = [
                'label' => __('Pending Cancellation'),
                'description' => __('Cancellation request is under review'),
                'complete' => false,
                'active' => true,
                'icon' => 'heroicon-o-exclamation-triangle',
                'color' => 'danger',
            ];
        } elseif ($status === Status::Canceled) {
            $steps['canceled'] = [
                'label' => __('Canceled'),
                'description' => __('Agreement has been canceled'),
                'complete' => true,
                'active' => true,
                'icon' => 'heroicon-o-x-circle',
                'color' => 'danger',
            ];
        }
        
        return $steps;
    }
    
    public function getAdminDates(): array
    {
        return [
            'announced_at' => [
                'label' => __('Announced'),
                'date' => $this->record->announced_at,
                'icon' => 'heroicon-o-sparkles',
            ],
            'validated_at' => [
                'label' => __('Validated'),
                'date' => $this->record->validated_at,
                'icon' => 'heroicon-o-check-circle',
            ],
            'received_at' => [
                'label' => __('Received'),
                'date' => $this->record->received_at,
                'icon' => 'heroicon-o-envelope',
            ],
            'signed_at' => [
                'label' => __('Signed'),
                'date' => $this->record->signed_at,
                'icon' => 'heroicon-o-document-check',
            ],
        ];
    }
}
