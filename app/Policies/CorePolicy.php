<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Internship;
use Illuminate\Auth\Access\HandlesAuthorization;

class CorePolicy
{
    use HandlesAuthorization;
    protected $administrators = ['SuperAdministrator', 'Administrator'];
    protected $professors = ['Professor', 'HeadOfDepartment', 'ProgramCoordinator'];
    protected $powerProfessors = ['SuperAdministrator', 'Administrator', 'ProgramCoordinator'];

}