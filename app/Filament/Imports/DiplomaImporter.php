<?php

namespace App\Filament\Imports;

use App\Models\Diploma;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class DiplomaImporter extends Importer
{
    protected static ?string $model = Diploma::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('registration_number')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('cne')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('cin')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('first_name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('last_name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('full_name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('last_name_ar')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('first_name_ar')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('birth_place_ar')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('birth_place_fr')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('birth_date')
                ->requiredMapping()
                ->rules(['required', 'date']),
            ImportColumn::make('nationality')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('council')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('program_code')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('assigned_program')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('program_tifinagh')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('program_english')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('program_arabic')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('qr_code')
                ->rules(['max:255']),
        ];
    }

    public function resolveRecord(): ?Diploma
    {
        // return Diploma::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Diploma();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your diploma import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
