<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Internship;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Enums\Role;

class CorePolicy
{
    use HandlesAuthorization;

    // public $role = Role::Administrator;
    protected $administrators = [ 'SuperAdministrator' , 'Administrator'];
    protected $professors = [ 'SuperAdministrator', 'Administrator','Professor', 'HeadOfDepartment', 'ProgramCoordinator'];
    protected $powerProfessors = ['SuperAdministrator', 'Administrator', 'ProgramCoordinator'];

}