<?php

namespace App\Filament\Widgets;

use App\Filament\Core\Widgets\ApexChartsParentWidget;
use App\Models\EntrepriseContacts;

class EntrepriseContactsChart extends ApexChartsParentWidget
{
    protected static ?int $sort = 9;

    protected static ?int $contentHeight = 300; //px

    /**
     * Chart Id
     */
    protected static ?string $chartId = 'entrepriseContactsChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Entreprise Contacts per provenance';

    public static function canView(): bool
    {
        return auth()->user()->isSuperAdministrator() || auth()->user()->isAdministrator() || auth()->user()->isDirection() || auth()->user()->isProgramCoordinator() || auth()->user()->isDepartmentHead();
    }

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    protected function getOptions(): array
    {
        $contacts = EntrepriseContacts::query()
            ->select('entreprise_contacts.category', \DB::raw('count(*) as count'))
            ->groupBy('category')
            ->get()
            ->toArray();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'width' => '100%',
            ],
            'series' => [
                [
                    'name' => __('Entreprises Contacts'),
                    'data' => array_column($contacts, 'count'),
                ],
            ],
            'xaxis' => [
                'categories' => array_column($contacts, 'category'),
            ],
            'legend' => [
                'labels' => [
                    'fontFamily' => 'inherit',
                    'color' => '#ffffff',
                    'fontColor' => '#ffffff',
                ],
            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => true,
                    'endingShape' => 'rounded',
                    'borderRadius' => 3,
                ],
            ],
        ];
    }
}
