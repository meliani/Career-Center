<?php

namespace App\Filament\Administration\Resources\RescheduleRequestResource\Pages;

use App\Filament\Administration\Resources\RescheduleRequestResource;
use App\Models\RescheduleRequest;
use App\Enums\RescheduleRequestStatus;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Colors\Color;

class ListRescheduleRequests extends ListRecords
{
    protected static string $resource = RescheduleRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('refresh_stats')
                ->label('Refresh Statistics')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(fn () => $this->redirect(request()->url())),
                
            Actions\Action::make('export_all')
                ->label('Export All')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->action(function () {
                    // TODO: Implement export functionality
                    \Filament\Notifications\Notification::make()
                        ->title('Export Started')
                        ->body('Export functionality will be implemented.')
                        ->info()
                        ->send();
                }),
        ];
    }
    
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Requests')
                ->badge(RescheduleRequest::count())
                ->badgeColor('gray'),
                
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', RescheduleRequestStatus::Pending))
                ->badge(RescheduleRequest::where('status', RescheduleRequestStatus::Pending)->count())
                ->badgeColor('warning'),
                
            'urgent' => Tab::make('Urgent')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->where('status', RescheduleRequestStatus::Pending)
                        ->whereHas('timetable.timeslot', function ($q) {
                            $q->where('start_time', '<=', now()->addDays(3));
                        });
                })
                ->badge(function () {
                    return RescheduleRequest::where('status', RescheduleRequestStatus::Pending)
                        ->whereHas('timetable.timeslot', function ($q) {
                            $q->where('start_time', '<=', now()->addDays(3));
                        })->count();
                })
                ->badgeColor('danger'),
                
            'high_priority' => Tab::make('High Priority')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->where('status', RescheduleRequestStatus::Pending)
                        ->whereHas('timetable.timeslot', function ($q) {
                            $q->where('start_time', '<=', now()->addDays(7))
                              ->where('start_time', '>', now()->addDays(3));
                        });
                })
                ->badge(function () {
                    return RescheduleRequest::where('status', RescheduleRequestStatus::Pending)
                        ->whereHas('timetable.timeslot', function ($q) {
                            $q->where('start_time', '<=', now()->addDays(7))
                              ->where('start_time', '>', now()->addDays(3));
                        })->count();
                })
                ->badgeColor('warning'),
                
            'approved' => Tab::make('Approved')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', RescheduleRequestStatus::Approved))
                ->badge(RescheduleRequest::where('status', RescheduleRequestStatus::Approved)->count())
                ->badgeColor('success'),
                
            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', RescheduleRequestStatus::Rejected))
                ->badge(RescheduleRequest::where('status', RescheduleRequestStatus::Rejected)->count())
                ->badgeColor('danger'),
                
            'recent' => Tab::make('Recent (7 days)')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('created_at', '>=', now()->subDays(7)))
                ->badge(RescheduleRequest::where('created_at', '>=', now()->subDays(7))->count())
                ->badgeColor('info'),
        ];
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            RescheduleRequestResource\Widgets\RescheduleRequestStatsWidget::class,
        ];
    }
}
