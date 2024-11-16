<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class InternshipAgreementScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (! auth()->check()) {
            return;
        }

        $user = auth()->user();

        if ($user->isSuperAdministrator() || $user->isAdministrator() || $user->isDirection()) {
            return;
        }

        if ($user->isProgramCoordinator()) {
            $builder->whereHas('student', function ($q) use ($user) {
                $q->where('program', $user->assigned_program);
            });

            return;
        }

        if ($user->isDepartmentHead()) {
            $builder->where('assigned_department', $user->department);

            return;
        }

        if ($user->isProfessor()) {
            $builder->whereHas('project', function ($q) use ($user) {
                $q->whereHas('professors', function ($q) use ($user) {
                    $q->where('professor_id', $user->id);
                });
            });

            return;
        }
    }
}
