<?php

namespace App\Filament\Imports;

use App\Models\MidweekEventSession;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class MidweekEventSessionImporter extends Importer
{
    protected static ?string $model = MidweekEventSession::class;

    public static function getColumns(): array
    {
        return [
            //
        ];
    }

    public function resolveRecord(): ?MidweekEventSession
    {
        // return MidweekEventSession::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new MidweekEventSession();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your midweek event session import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
