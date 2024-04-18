<?php

namespace App\Filament\Widgets;

use App\Filament\Core\Widgets\ApexChartsParentWidget;
use App\Models\InternshipAgreement;

class InternshipsDistributionByDepartmentChart extends ApexChartsParentWidget
{
    protected static ?int $sort = 3;

    // protected int|string|array $columnSpan = 'full';

    /**
     * Chart Id
     */
    protected static ?string $chartId = 'InternshipsDistributionByDepartmentChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Internships distribution by assigned department';

    public static function canView(): bool
    {
        return true;
    }

    public function getDescription(): ?string
    {
        return __('An overview of the internships distribution by assigned department.');
    }

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    protected function getOptions(): array
    {
        $internships = InternshipAgreement::withoutGlobalScopes()
            ->where('status', '=', 'Signed')
            // whereRaw('status = "Draft" ')
            ->selectRaw('count(*) as count, assigned_department')
            ->groupBy('assigned_department')
            ->get()
            ->toArray();
        foreach ($internships as $key => $value) {
            if (empty($value['assigned_department'])) {
                $internships[$key]['assigned_department'] = __('Not assigned');
            }
        }

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'with: 100%',
            ],
            'series' => [
                [
                    'name' => __('Total assigned internships'),
                    'data' => array_column($internships, 'count'),
                ],
            ],
            'xaxis' => [
                'categories' => array_column($internships, 'assigned_department'),
            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => false,
                    'endingShape' => 'rounded',
                    'borderRadius' => 3,
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
            ],
            // 'fill' => [
            //     'type' => 'gradient',
            //     'gradient' => [
            //         // 'shadeIntensity' => 1,
            //         'opacityFrom' => 0.7,
            //         'opacityTo' => 0.9,
            //         'stops' => [0, 40],
            //     ],
            // ],
        ];
    }
}
