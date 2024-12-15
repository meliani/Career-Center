<?php

namespace App\Observers;

use App\Models\FinalYearInternshipAgreement;

class AgreementObserver
{
    /**
     * Handle the Agreement "created" event.
     */
    public function created(FinalYearInternshipAgreement $agreement): void
    {
        //
    }

    /**
     * Handle the Agreement "updated" event.
     */
    public function updated(FinalYearInternshipAgreement $agreement): void
    {
        // dd($agreement);
    }

    /**
     * Handle the Agreement "deleted" event.
     */
    public function deleted(FinalYearInternshipAgreement $agreement): void
    {
        // check if project esists
        if (! $agreement->project) {
            return;
        }
        $agreement->project->delete();
    }

    /**
     * Handle the Agreement "restored" event.
     */
    public function restored(FinalYearInternshipAgreement $agreement): void
    {
        //
    }

    /**
     * Handle the Agreement "force deleted" event.
     */
    public function forceDeleted(FinalYearInternshipAgreement $agreement): void
    {
        //
    }
}
