<?php

namespace App\Filament\Administration\Resources\RescheduleRequestResource\Widgets;

use App\Models\RescheduleRequest;
use App\Enums\RescheduleRequestStatus;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class RescheduleRequestStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Get basic statistics
        $totalRequests = RescheduleRequest::count();
        $pendingRequests = RescheduleRequest::where('status', RescheduleRequestStatus::Pending)->count();
        $approvedRequests = RescheduleRequest::where('status', RescheduleRequestStatus::Approved)->count();
        $rejectedRequests = RescheduleRequest::where('status', RescheduleRequestStatus::Rejected)->count();
        
        // Get urgent requests (defense within 3 days)
        $urgentRequests = RescheduleRequest::where('status', RescheduleRequestStatus::Pending)
            ->whereHas('timetable.timeslot', function ($q) {
                $q->where('start_time', '<=', now()->addDays(3));
            })->count();
            
        // Get high priority requests (defense within 7 days)
        $highPriorityRequests = RescheduleRequest::where('status', RescheduleRequestStatus::Pending)
            ->whereHas('timetable.timeslot', function ($q) {
                $q->where('start_time', '<=', now()->addDays(7));
            })->count();
            
        // Calculate average processing time
        $avgProcessingTime = RescheduleRequest::whereNotNull('processed_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, processed_at)) as avg_hours'))
            ->value('avg_hours');
        
        // Calculate approval rate
        $processedRequests = $approvedRequests + $rejectedRequests;
        $approvalRate = $processedRequests > 0 ? round(($approvedRequests / $processedRequests) * 100, 1) : 0;
        
        // Get recent activity (last 7 days)
        $recentRequests = RescheduleRequest::where('created_at', '>=', now()->subDays(7))->count();
        
        return [
            Stat::make('Total Requests', $totalRequests)
                ->description('All time requests')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('gray'),
                
            Stat::make('Pending Requests', $pendingRequests)
                ->description('Awaiting processing')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingRequests > 10 ? 'warning' : 'primary'),
                
            Stat::make('Urgent Requests', $urgentRequests)
                ->description('Defense ≤ 3 days')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($urgentRequests > 0 ? 'danger' : 'success'),
                
            Stat::make('High Priority', $highPriorityRequests)
                ->description('Defense ≤ 7 days')
                ->descriptionIcon('heroicon-m-flag')
                ->color($highPriorityRequests > 5 ? 'warning' : 'info'),
                
            Stat::make('Approval Rate', $approvalRate . '%')
                ->description('Of processed requests')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($approvalRate >= 80 ? 'success' : ($approvalRate >= 60 ? 'warning' : 'danger')),
                
            Stat::make('Avg. Processing Time', $avgProcessingTime ? round($avgProcessingTime, 1) . 'h' : 'N/A')
                ->description('Hours to process')
                ->descriptionIcon('heroicon-m-clock')
                ->color($avgProcessingTime && $avgProcessingTime <= 24 ? 'success' : 'warning'),
                
            Stat::make('Recent Activity', $recentRequests)
                ->description('Last 7 days')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
                
            Stat::make('Today\'s Tasks', function () {
                return RescheduleRequest::where('status', RescheduleRequestStatus::Pending)
                    ->whereHas('timetable.timeslot', function ($q) {
                        $q->where('start_time', '<=', now()->addDay());
                    })->count();
            })
                ->description('Need immediate attention')
                ->descriptionIcon('heroicon-m-bell-alert')
                ->color('danger'),
        ];
    }
    
    protected function getColumns(): int
    {
        return 4;
    }
}
