<?php

namespace App\Services;

use Illuminate\Support\Collection;

class EmailCampaignService
{
    public function organizeEmailsByDomain(Collection $records): Collection
    {
        // Group records by email domain
        $groupedRecords = $records->groupBy(function ($record) {
            return substr(strrchr($record->email, '@'), 1);
        });

        // Create interleaved collection
        $interleavedRecords = collect();
        $previousDomain = null;

        while (! $groupedRecords->isEmpty()) {
            foreach ($groupedRecords->keys() as $domain) {
                if ($groupedRecords->get($domain)->isNotEmpty()) {
                    $record = $groupedRecords->get($domain)->shift();
                    $delaySeconds = $previousDomain === $domain ? rand(5, 30) : 0;

                    $interleavedRecords->push([
                        'record' => $record,
                        'delay' => $delaySeconds,
                    ]);

                    $previousDomain = $domain;
                }
            }

            $groupedRecords = $groupedRecords->filter(function ($emails) {
                return $emails->isNotEmpty();
            });
        }

        return $interleavedRecords;
    }
}
