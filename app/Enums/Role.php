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

    public static function getAll(): array
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
            Role::System,
        ];
    }

    public static function toArray(): array
    {
        return [
            Role::ProgramCoordinator->value,
            Role::Professor->value,
            Role::InternshipSupervisor->value,
            Role::DepartmentHead->value,
            Role::Administrator->value,
            Role::SuperAdministrator->value,
            Role::AdministrativeSupervisor->value,
            Role::Direction->value,
            Role::System->value,

        ];
    }

    public static function getArray(): array
    {
        return [
            Role::ProgramCoordinator->value,
            Role::Professor->value,
            Role::InternshipSupervisor->value,
            Role::DepartmentHead->value,
            Role::Administrator->value,
            Role::SuperAdministrator->value,
            Role::AdministrativeSupervisor->value,
            Role::Direction->value,
            Role::System->value,

        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            Role::ProgramCoordinator => __('Program Coordinator'),
            Role::Professor => __('Professor'),
            Role::InternshipSupervisor => __('Internship Supervisor'),
            Role::DepartmentHead => __('Head of Department'),
            Role::Administrator => __('Administrator'),
            Role::SuperAdministrator => __('Super Administrator'),
            Role::AdministrativeSupervisor => __('Administrative Supervisor'),
            Role::Direction => __('Direction'),
            Role::System => __('System'),
        };
    }
}
