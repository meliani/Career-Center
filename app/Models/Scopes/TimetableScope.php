<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TimetableScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->check()) {

            if (auth()->user()->isSuperAdministrator() || auth()->user()->isAdministrator() || auth()->user()->isDirection()) {
                return;
            } elseif (auth()->user()->isProgramCoordinator()) {
                $builder
                    ->whereHas('students', function ($q) {
                        $q->where('program', '=', auth()->user()->assigned_program);
                    });

            } elseif (auth()->user()->isDepartmentHead()) {
                $builder
                    ->whereHas('projects', function ($q) {
                        $q->whereHas('internship_agreements', function ($q) {
                            $q->where('assigned_department', '=', auth()->user()->department);
                        });
                    });

            } elseif (auth()->user()->isProfessor()) {
                $builder->where('department', '=', auth()->user()->department);

            } elseif (auth()->user()->isAdministrativeSupervisor()) {
                $builder->whereHas('project', function ($q) {
                    $q->whereHas('internship_agreements', function ($q) {
                        $q->whereHas('student', function ($q) {
                            $q->where('program', '=', auth()->user()->assigned_program);
                        });
                    });
                })
                    ->orWhere('project_id', null);
            } else {
                abort(403, 'You are not authorized to view this page');
            }

        }

    }
}
