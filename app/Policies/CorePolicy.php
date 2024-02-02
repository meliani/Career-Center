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

    // protected $administrators = [ 'SuperAdministrator' , 'Administrator'];
    // protected $professors = [ 'SuperAdministrator', 'Administrator','Professor', 'DepartmentHead', 'ProgramCoordinator'];
    // protected $powerProfessors = ['SuperAdministrator', 'Administrator', 'ProgramCoordinator'];

    
    protected $administrators = [ Role::SuperAdministrator , Role::Administrator];
    protected $professors = [ Role::SuperAdministrator, Role::Administrator, Role::Professor, Role::DepartmentHead, Role::ProgramCoordinator];
    protected $powerProfessors = [Role::SuperAdministrator, Role::Administrator, Role::ProgramCoordinator];

}