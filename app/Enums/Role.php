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
    case Direction = 'Direction';

    public static function getProfessorRoles(): array
    {
        return [
            Role::Professor,
            Role::DepartmentHead,
            Role::ProgramCoordinator,
        ];
    }
    public static function getProgramCoordinatorRoles(): array
    {
        return [
            Role::ProgramCoordinator,
        ];
    }
    public static function getDepartmentHeadRoles(): array
    {
        return [
            Role::DepartmentHead,
        ];
    }
    public static function getAdministrativeSupervisorRoles(): array
    {
        return [
            Role::AdministrativeSupervisor,
        ];
    }
    public static function getDirectionRoles(): array
    {
        return [
            Role::Direction,
        ];
    }
    public static function getAdministratorRoles(): array
    {
        return [
            Role::Administrator,
            Role::SuperAdministrator,
        ];
    }

    public static function toArray(): array
    {
        return [
            Role::ProgramCoordinator,
            Role::Professor,
            Role::InternshipSupervisor,
            Role::DepartmentHead,
            Role::Administrator,
            Role::SuperAdministrator,
            Role::AdministrativeSupervisor,
            Role::Direction,
        ];
    }
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
            Role::Direction => 'Direction',
        };
    }
}
