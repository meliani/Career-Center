<?

use Filament\Support\Contracts\HasLabel;

enum Role: string implements HasLabel
{
    case ProgramCoordinator = 'Program Coordinator';
    case InternshipSupervisor = 'Internship Supervisor';
    case HeadOfDepartment = 'Head of Department';
    case Administrator = 'Administrator';
    case SuperAdministrator = 'Super Administrator';

    public function getLabel(): ?string
    {
        return $this->name;

        /*  return match ($this) {
            self::Draft => 'Draft',
            self::Reviewing => 'Reviewing',
            self::Published => 'Published',
            self::Rejected => 'Rejected',
        }; */
    }
}
