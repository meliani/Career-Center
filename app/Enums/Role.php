<?php
namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Role: string implements HasLabel
{
    case ProgramCoordinator = 'Program Coordinator';
    case Professor = 'Professor';
    case InternshipSupervisor = 'Internship Supervisor';
    case HeadOfDepartment = 'Head of Department';
    case Administrator = 'Administrator';
    case SuperAdministrator = 'Super Administrator';
    case AdministrativeSupervisor = 'Administrative Supervisor';

    public function getLabel(): ?string
    {
        return __($this->name);

        /*  return match ($this) {
            self::Draft => 'Draft',
            self::Validating => 'Validating',
            self::Published => 'Published',
            self::Rejected => 'Rejected',
        }; */
    }
}
