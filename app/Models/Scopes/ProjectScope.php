<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ProjectScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->user()->isSuperAdministrator() || auth()->user()->isAdministrator()) {
            return;
        }
        elseif (auth()->user()->isProgramCoordinator()) {
            $builder
                ->whereHas('students', function ($q) {
                    $q->where('program', '=', auth()->user()->program_coordinator);
                });
            return;
        }
        elseif (auth()->user()->isDepartmentHead()) {
            $builder
                ->whereHas('InternshipAgreements', function ($q) {
                    $q->where('assigned_department', '=', auth()->user()->department);
                });
            return;
        }
        elseif (auth()->user()->isProfessor()) {
            $builder
                ->whereHas('professors', function ($q) {
                    $q->where('professor_id', '=', auth()->user()->id);
                });
            }
        else {
            $builder->where('student_id', '=', auth()->user()->id);
        }

    }
}