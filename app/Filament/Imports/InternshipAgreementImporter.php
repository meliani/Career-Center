<?php

namespace App\Filament\Imports;

use App\Models\InternshipAgreement;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class InternshipAgreementImporter extends Importer
{
    protected static ?string $model = InternshipAgreement::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('id')
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('id_pfe')
                ->numeric()
                ->rules(['integer'])
                ->ignoreBlankState(),
            ImportColumn::make('organization_name')
                ->rules(['max:191'])
                ->ignoreBlankState(),
            ImportColumn::make('adresse')
                ->rules(['max:255'])
                ->ignoreBlankState(),
            ImportColumn::make('city')
                ->rules(['max:191'])
                ->ignoreBlankState(),
            ImportColumn::make('country')
                ->rules(['max:191'])
                ->ignoreBlankState(),
            ImportColumn::make('office_location')
                ->rules(['max:255'])
                ->ignoreBlankState(),
            ImportColumn::make('parrain_titre')
                ->rules(['max:191'])
                ->ignoreBlankState(),
            ImportColumn::make('parrain_nom')
                ->rules(['max:191'])
                ->ignoreBlankState(),
            ImportColumn::make('parrain_prenom')
                ->rules(['max:191'])
                ->ignoreBlankState(),
            ImportColumn::make('parrain_fonction')
                ->rules(['max:191'])
                ->ignoreBlankState(),
            ImportColumn::make('parrain_tel')
                ->rules(['max:191'])
                ->ignoreBlankState(),
            ImportColumn::make('parrain_mail')
                ->rules(['max:191'])
                ->ignoreBlankState(),
            ImportColumn::make('encadrant_ext_titre')
                ->rules(['max:191'])
                ->ignoreBlankState(),
            ImportColumn::make('encadrant_ext_nom')
                ->rules(['max:191'])
                ->ignoreBlankState(),
            ImportColumn::make('encadrant_ext_prenom')
                ->rules(['max:191'])
                ->ignoreBlankState(),
            ImportColumn::make('encadrant_ext_fonction')
                ->rules(['max:191'])
                ->ignoreBlankState(),
            ImportColumn::make('encadrant_ext_tel')
                ->rules(['max:191'])
                ->ignoreBlankState(),
            ImportColumn::make('encadrant_ext_mail')
                ->rules(['max:191'])
                ->ignoreBlankState(),
            ImportColumn::make('title')
                ->rules(['max:65535'])
                ->ignoreBlankState(),
            ImportColumn::make('description')
                ->rules(['max:65535'])
                ->ignoreBlankState(),
            ImportColumn::make('keywords')
                ->rules(['max:65535'])
                ->ignoreBlankState(),
            ImportColumn::make('starting_at')
                ->rules(['date'])
                ->ignoreBlankState(),
            ImportColumn::make('ending_at')
                ->rules(['date']),
            ImportColumn::make('remuneration')
                ->rules(['max:191'])
                ->ignoreBlankState(),
            ImportColumn::make('currency')
                ->rules(['max:10'])
                ->ignoreBlankState(),
            ImportColumn::make('load')
                ->rules(['max:191'])
                ->ignoreBlankState(),
            ImportColumn::make('int_adviser_name')
                ->rules(['max:191'])
                ->ignoreBlankState(),
            ImportColumn::make('year_id')
                ->numeric()
                ->rules(['integer'])
                ->ignoreBlankState(),
            ImportColumn::make('status')
                ->rules(['max:255'])
                ->ignoreBlankState(),
            ImportColumn::make('announced_at')
                ->rules(['date'])
                ->ignoreBlankState(),
            ImportColumn::make('validated_at')
                ->rules(['date'])
                ->ignoreBlankState(),
            ImportColumn::make('assigned_department')
                ->rules(['max:10'])
                ->ignoreBlankState(),
            ImportColumn::make('received_at')
                ->rules(['date'])
                ->ignoreBlankState(),
            ImportColumn::make('signed_at')
                ->rules(['date'])
                ->ignoreBlankState(),
            ImportColumn::make('observations')
                ->rules(['max:65535'])
                ->ignoreBlankState(),
        ];
    }

    public function resolveRecord(): ?InternshipAgreement
    {
        // return InternshipAgreement::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);
        return InternshipAgreement::query()
            ->where('id', $this->data['id'])
            ->first();
        // return new InternshipAgreement();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your internship agreement import has completed and '.number_format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
