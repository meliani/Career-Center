
namespace App\Policies;

use App\Enums\Role;
use App\Models\InternshipApplication as Application;
use App\Models\User;

class ApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([Role::Administrator, Role::Direction, Role::Professor, Role::ProgramCoordinator, Role::DepartmentHead]);
    }

    // ...existing methods...
}
