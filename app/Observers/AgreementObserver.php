<?php

namespace App\Observers;

use App\Enums;
use App\Enums\Role;
use App\Models\FinalYearInternshipAgreement;
use App\Models\User;
use App\Notifications\AgreementAssignedNotification;
use App\Notifications\AgreementCancellationNotification;
use App\Notifications\AgreementCreatedNotification;

class AgreementObserver
{
    /**
     * Handle the Agreement "created" event.
     */
    public function created(FinalYearInternshipAgreement $agreement): void {}

    /**
     * Handle the Agreement "updated" event.
     */
    public function updated(FinalYearInternshipAgreement $agreement): void
    {
        // For status change to Signed, notify program coordinators
        if ($agreement->wasChanged('status') && $agreement->status === Enums\Status::Signed) {
            $programCoordinators = User::query()
                ->where('role', Role::ProgramCoordinator)
                ->where('assigned_program', $agreement->student->program)
                ->get();

            foreach ($programCoordinators as $coordinator) {
                $coordinator->notify(new AgreementCreatedNotification($agreement));
            }
        }

        // For department assignment on Signed agreements, notify department heads and admins
        if (
            $agreement->wasChanged('assigned_department')
            && $agreement->assigned_department
            && $agreement->status === Enums\Status::Signed
        ) {
            $recipients = User::query()
                ->where(function ($query) use ($agreement) {
                    $query
                    // ->whereIn('role', [Role::Administrator, Role::SuperAdministrator])
                        ->where(function ($query) use ($agreement) {
                            $query->where('role', Role::DepartmentHead)
                                ->where('department', $agreement->assigned_department);
                        });
                })
                ->get();

            foreach ($recipients as $recipient) {
                $recipient->notify(new AgreementAssignedNotification($agreement, auth()->user()));
            }
        }
    }

    /**
     * Handle the Agreement "deleted" event.
     */
    public function deleted(FinalYearInternshipAgreement $agreement): void
    {

        // detach agreement from project polymorphically
        $agreement->project()->detach();
        $agreement->save();

        // check if project exists
        if ($agreement->project && $agreement->project->exists && ! $agreement->project->final_internship_agreements()->exists()) {
            $agreement->project->delete();
        }

        $agreement->student->notify(new AgreementCancellationNotification($agreement));

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
