<?php

namespace App\Filament\Actions\BulkAction\Email;

use App\Jobs\ProcessFinalProjectsCompaign;
use App\Services\EmailCampaignService;
use Filament\Tables\Actions\BulkAction;

class SendFinalProjectsMailingCampaign extends BulkAction
{
    public static function make(?string $name = null): static
    {
        $static = app(static::class, [
            'name' => $name ?? static::getDefaultName(),
        ]);

        $static->configure()->action(function ($records): void {
            $emailCampaignService = new EmailCampaignService;
            $interleavedRecords = $emailCampaignService->organizeEmailsByDomain(collect($records));

            foreach ($interleavedRecords as $item) {
                $item['record']->trackInteraction();

                ProcessFinalProjectsCompaign::dispatch(
                    $item['record']->email,
                    $item['record']->long_full_name,
                    $item['record']->category->value
                )->delay(now()->addSeconds($item['delay']));
            }
        });

        return $static;
    }
}
