<?php

namespace App\Policies;

use App\Models\User;
use App\Models\InternshipAgreement;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Enums\Role;

class CorePolicy
{
    use HandlesAuthorization;
    
    protected $administrators = [ Role::SuperAdministrator , Role::Administrator];
    protected $professors = [ Role::SuperAdministrator, Role::Administrator, Role::Professor, Role::DepartmentHead, Role::ProgramCoordinator];
    protected $powerProfessors = [Role::SuperAdministrator, Role::Administrator, Role::ProgramCoordinator];

}