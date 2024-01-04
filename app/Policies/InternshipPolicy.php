<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Internship;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Policies\CorePolicy;

class InternshipPolicy extends CorePolicy
{
    use HandlesAuthorization;
    protected $administrators = ['SuperAdministrator', 'Administrator'];
    protected $professors = ['Professor', 'HeadOfDepartment', 'ProgramCoordinator'];
    protected $powerProfessors = ['SuperAdministrator', 'Administrator', 'ProgramCoordinator'];

    public function view(User $user, Internship $internship)
    {
        return $user->hasAnyRole($this->administrators);
    }
    /**
     * Review function to be executed only by SuperAdministrator, Administrator, or ProgramCoordinator
     */
    public function review(User $user, Internship $internship)
    {
        return $user->hasAnyRole($this->administrators);
    }

    public function update(User $user, Internship $internship)
    {
        return $user->hasRole($this->administrators);
    }

    public function delete(User $user, Internship $internship)
    {
        return $user->hasRole('SuperAdministrator');
    }

    public function viewSome(User $user, Internship $internship)
    {
        // Professors can view some records and properties from internship model
        // You'll need to define what "some" means in this context
        return $user->hasRole('ProgramCoordinator');
    }

    public function viewRelated(User $user, Internship $internship)
    {
        // Head of department can see related records from same program belonging to the department
        // You'll need to define what "related" means in this context
        return $user->hasRole('ProgramCoordinator') && $user->department_id == $internship->department_id;
    }

    public function updateCertain(User $user, Internship $internship)
    {
        // Policies must check if a given user can update Internship with a certain given program from students table
        // You'll need to define what "certain" means in this context
        return $user->hasRole('ProgramCoordinator') && $user->program_id == $internship->program_id;
    }
}