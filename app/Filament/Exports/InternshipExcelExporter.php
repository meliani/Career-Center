<?php

namespace App\Filament\Exports;

use App\Models\FinalYearInternshipAgreement;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class InternshipExcelExporter extends ExcelExport
{
    public function __construct()
    {
        $this
            ->askForFilename()
            ->askForWriterType()
            ->withFilename(function ($filename) {
                $date = Carbon::now()->format('Y-m-d');
                return "internships-{$date}-{$filename}";
            })
            ->withSheets([
                'Active Internships' => fn ($export) => $export->withColumns($this->getColumns())
                    ->modifyQueryUsing(fn (Builder $query) => $query->whereNotIn('status', ['Cancelled', 'Expired'])),
                
                'All Internships' => fn ($export) => $export->withColumns($this->getColumns()),
            ])
            ->queue()
            ->notifyWhenReady()
            ->fromModel(fn () => FinalYearInternshipAgreement::class);
    }

    public function getColumns(): array
    {
        return [
            Column::make('student.id_pfe')
                ->heading(__('Student ID'))
                ->width(12),
                
            Column::make('student.full_name')
                ->heading(__('Student'))
                ->width(20),
                
            Column::make('title')
                ->heading(__('Internship Title'))
                ->width(30),
                
            Column::make('organization.name')
                ->heading(__('Organization'))
                ->width(25),
                
            Column::make('externalSupervisor.full_name')
                ->heading(__('External Supervisor'))
                ->width(20),

            Column::make('starting_at')
                ->heading(__('Start Date'))
                ->formatStateUsing(fn (string $state) => Carbon::parse($state)->format('d/m/Y'))
                ->width(12),
                
            Column::make('ending_at')
                ->heading(__('End Date'))
                ->formatStateUsing(fn (string $state) => Carbon::parse($state)->format('d/m/Y'))
                ->width(12),
                
            Column::make('tags')
                ->heading(__('Tags'))
                ->formatStateUsing(fn ($state) => collect($state)->pluck('name')->join(', '))
                ->width(20),
                
            Column::make('academic_supervisor_name')
                ->heading(__('Academic Supervisor'))
                ->width(20),
                
            Column::make('status')
                ->heading(__('Status'))
                ->width(12),
                
            Column::make('assigned_department')
                ->heading(__('Department'))
                ->formatStateUsing(function ($state) {
                    return $state ? $state->getDescription() : '';
                })
                ->width(15),
        ];
    }
}