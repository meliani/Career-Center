<?php

namespace App\Policies;

use App\Models\Alumni;
use App\Models\JobOffer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JobOfferPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User | Alumni $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User | Alumni $user, JobOffer $jobOffer): Response
    {
        // return $user->id === $jobOffer->user_id
        //     ? Response::allow()
        //     : Response::deny('You do not own this job offer.');
        return Response::allow();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User | Alumni $user): bool
    {
        return $user->isVerified();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User | Alumni $user, JobOffer $jobOffer): bool
    {
        return $user->id === $jobOffer->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User | Alumni $user, JobOffer $jobOffer): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User | Alumni $user, JobOffer $jobOffer): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User | Alumni $user, JobOffer $jobOffer): bool
    {
        //
    }
}
