<?php

namespace App\Filament\Imports;

use App\Models\OrganizationAccount;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class OrganizationAccountImporter extends Importer
{
    protected static ?string $model = OrganizationAccount::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name'),
            ImportColumn::make('description'),
            ImportColumn::make('contact_name'),
            ImportColumn::make('contact_email'),
            ImportColumn::make('contact_office_phone'),
            ImportColumn::make('contact_mobile'),
            ImportColumn::make('notes'),
        ];
    }

    public function resolveRecord(): ?OrganizationAccount
    {
        // return OrganizationAccount::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new OrganizationAccount;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your organization account import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
