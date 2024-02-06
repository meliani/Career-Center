<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Role: string implements HasLabel
{
    case ProgramCoordinator = 'ProgramCoordinator';
    case Professor = 'Professor';
    case InternshipSupervisor = 'InternshipSupervisor';
    case DepartmentHead = 'DepartmentHead';
    case Administrator = 'Administrator';
    case SuperAdministrator = 'SuperAdministrator';
    case AdministrativeSupervisor = 'AdministrativeSupervisor';

    public function getLabel(): ?string
    {
        return match ($this) {
            Role::ProgramCoordinator => 'Program Coordinator',
            Role::Professor => 'Professor',
            Role::InternshipSupervisor => 'Internship Supervisor',
            Role::DepartmentHead => 'Head of Department',
            Role::Administrator => 'Administrator',
            Role::SuperAdministrator => 'Super Administrator',
            Role::AdministrativeSupervisor => 'Administrative Supervisor',
        };
    }
}
