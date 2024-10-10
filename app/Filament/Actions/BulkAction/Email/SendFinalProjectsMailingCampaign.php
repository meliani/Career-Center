<?php

namespace App\Filament\Actions\BulkAction\Email;

use App\Jobs\ProcessFinalProjectsCompaign;
use Filament\Tables\Actions\BulkAction;

class SendFinalProjectsMailingCampaign extends BulkAction
{
    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);

        // $static->configure()->action(function ($records): void {
        //     // $seconds = 0;

        //     foreach ($records as $record) {

        //         SendMassMail::dispatch($record->email, $record->long_full_name, $record->category->value);
        //         // ->onQueue('emails');
        //         // ->delay(now()->addSeconds($seconds));
        //         // $seconds += 0;
        //     }
        // });

        $static->configure()->action(function ($records): void {
            // Group records by email domain
            $groupedRecords = collect($records)->groupBy(function ($record) {
                return substr(strrchr($record->email, '@'), 1);
            });

            // Create a new collection where each domain is followed by an email from a different domain
            $interleavedRecords = collect();
            $previousDomain = null;
            while (! $groupedRecords->isEmpty()) {
                foreach ($groupedRecords->keys() as $domain) {
                    if ($groupedRecords->get($domain)->isNotEmpty()) {
                        $record = $groupedRecords->get($domain)->shift();
                        $record->delay = $previousDomain === $domain ? rand(5, 30) : 0; // Add a random delay if it's the same domain as before
                        $interleavedRecords->push($record);
                        $previousDomain = $domain;
                    }
                }
                $groupedRecords = $groupedRecords->filter(function ($emails) {
                    return $emails->isNotEmpty();
                });
            }

            // Dispatch the jobs
            foreach ($interleavedRecords as $record) {
                ProcessFinalProjectsCompaign::dispatch($record->email, $record->long_full_name, $record->category->value)
                    ->delay(now()->addSeconds($record->delay));
            }
        });

        return $static;
    }
}
