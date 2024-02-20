<?php

namespace App\Filament\Exports;

use App\Models\InternshipAgreement;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class InternshipAgreementExporter extends Exporter
{
    protected static ?string $model = InternshipAgreement::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('id_pfe'),
            ExportColumn::make('organization_name'),
            ExportColumn::make('adresse'),
            ExportColumn::make('city'),
            ExportColumn::make('country'),
            ExportColumn::make('office_location'),
            ExportColumn::make('parrain_titre'),
            ExportColumn::make('parrain_nom'),
            ExportColumn::make('parrain_prenom'),
            ExportColumn::make('parrain_fonction'),
            ExportColumn::make('parrain_tel'),
            ExportColumn::make('parrain_mail'),
            ExportColumn::make('encadrant_ext_titre'),
            ExportColumn::make('encadrant_ext_nom'),
            ExportColumn::make('encadrant_ext_prenom'),
            ExportColumn::make('encadrant_ext_fonction'),
            ExportColumn::make('encadrant_ext_tel'),
            ExportColumn::make('encadrant_ext_mail'),
            ExportColumn::make('title'),
            ExportColumn::make('description'),
            ExportColumn::make('keywords'),
            ExportColumn::make('starting_at'),
            ExportColumn::make('ending_at'),
            ExportColumn::make('remuneration'),
            ExportColumn::make('currency'),
            ExportColumn::make('load'),
            ExportColumn::make('int_adviser_name'),
            ExportColumn::make('student_id'),
            ExportColumn::make('year_id'),
            ExportColumn::make('project_id'),
            ExportColumn::make('status'),
            ExportColumn::make('announced_at'),
            ExportColumn::make('validated_at'),
            ExportColumn::make('assigned_department'),
            ExportColumn::make('received_at'),
            ExportColumn::make('signed_at'),
            ExportColumn::make('observations'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your internship agreement export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
