<?php

namespace App\Filament\Administration\Widgets;

use App\Models\RescheduleRequest;
use App\Enums\RescheduleRequestStatus;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RescheduleRequestChart extends ChartWidget
{
    protected static ?string $heading = 'Reschedule Requests Trend';
    
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        // Get data for the last 30 days
        $endDate = now();
        $startDate = $endDate->copy()->subDays(29);
        
        $dates = [];
        $pendingData = [];
        $approvedData = [];
        $rejectedData = [];
        
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $dateStr = $date->format('M j');
            $dates[] = $dateStr;
            
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();
            
            $pendingData[] = RescheduleRequest::where('status', RescheduleRequestStatus::Pending)
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->count();
                
            $approvedData[] = RescheduleRequest::where('status', RescheduleRequestStatus::Approved)
                ->whereBetween('processed_at', [$dayStart, $dayEnd])
                ->count();
                
            $rejectedData[] = RescheduleRequest::where('status', RescheduleRequestStatus::Rejected)
                ->whereBetween('processed_at', [$dayStart, $dayEnd])
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Submitted',
                    'data' => $pendingData,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
                [
                    'label' => 'Approved',
                    'data' => $approvedData,
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                ],
                [
                    'label' => 'Rejected',
                    'data' => $rejectedData,
                    'borderColor' => 'rgb(239, 68, 68)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                ],
            ],
            'labels' => $dates,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
    
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
