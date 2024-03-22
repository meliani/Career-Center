<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ProjectByProgram implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->check()) {
            if (auth()->user()->isSuperAdministrator() || auth()->user()->isAdministrator()) {
                return;
            } elseif (auth()->user()->isProgramCoordinator()) {
                $builder
                    ->whereHas('students', function ($q) {
                        $q->where('program', '=', auth()->user()->assigned_program);
                    });

                return;
            } elseif (auth()->user()->isDepartmentHead()) {
                $builder
                    ->whereHas('students', function ($q) {
                        $q->where('department', '=', auth()->user()->department);
                    });

                return;
            } else {
                $builder->where('student_id', '=', auth()->user()->id);
            }
        }
    }
}
