<?php
namespace App\Filament\Resources\InternshipResource\Actions;

use Filament\Tables\Actions\Action;
use Illuminate\Support\Carbon;

class ReviewAction extends Action
{
    public function run($record, $request)
    {
        // Update the pedagogic_validation_date to the current date
        $record->reviewed_at = Carbon::now();
        $record->save();
    }
}