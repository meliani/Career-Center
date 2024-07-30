<?php

namespace App\Filament\Imports;

use App\Models\AlumniReference;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\Checkbox;

class AlumniReferenceImporter extends Importer
{
    protected static ?string $model = AlumniReference::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('title')
                ->ignoreBlankState(),
            ImportColumn::make('name')
                ->rules(['max:255'])
                ->ignoreBlankState(),
            ImportColumn::make('first_name')
                ->rules(['max:255'])
                ->ignoreBlankState(),
            ImportColumn::make('last_name')
                ->rules(['max:255'])
                ->ignoreBlankState(),
            ImportColumn::make('email')
                ->requiredMapping()
                ->rules(['required', 'email', 'max:255',
                    // , 'unique:alumni_references'
                ]),
            ImportColumn::make('phone_number')
                ->rules(['max:255', 'phone:INTERNATIONAL,MA'])
                ->ignoreBlankState(),
            ImportColumn::make('graduation_year_id')
                ->requiredMapping()
                // ->numeric()
                // ->rules(['integer'])
                ->ignoreBlankState(),
            ImportColumn::make('degree')
                ->ignoreBlankState(),
            ImportColumn::make('assigned_program')
                ->ignoreBlankState(),
            ImportColumn::make('is_enabled')
                // ->numeric()
                // ->rules(['integer'])
                ->ignoreBlankState(),
            ImportColumn::make('is_mobility')
                // ->numeric()
                // ->rules(['integer'])
                ->ignoreBlankState(),
            ImportColumn::make('abroad_school')
                ->rules(['max:191'])
                ->ignoreBlankState(),
            ImportColumn::make('work_status'),
            ImportColumn::make('resume_url')
                ->rules(['max:255'])
                ->ignoreBlankState(),
            ImportColumn::make('avatar_url')
                ->rules(['max:255'])
                ->ignoreBlankState(),
            ImportColumn::make('number_of_bounces')
                // ->numeric()
                // ->rules(['integer'])
                ->ignoreBlankState(),
            ImportColumn::make('bounce_reason')
                ->rules(['max:255'])
                ->ignoreBlankState(),
        ];
    }

    public function resolveRecord(): ?AlumniReference
    {
        // return AlumniReference::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);
        if ($this->options['updateExisting'] ?? false) {
            return AlumniReference::firstOrNew([
                'email' => $this->data['email'],
            ]);
        }

        return new AlumniReference;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your alumni reference import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }

    public static function getOptionsFormComponents(): array
    {
        return [
            Checkbox::make('updateExisting')
                ->label('Update existing records'),
        ];
    }
}
