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
        if (auth()->user()->isSuperAdministrator() || auth()->user()->isAdministrator() || auth()->user()->isDirection()) {
            return;
        } elseif (auth()->user()->isProgramCoordinator()) {
            $builder
                ->whereHas('student', function ($q) {
                    $q->where('program', '=', auth()->user()->assigned_program);
                });

            return;
        } elseif (auth()->user()->isDepartmentHead()) {
            $builder
                ->whereHas('student', function ($q) {
                    $q->where('assigned_department', '=', auth()->user()->department);
                });

            return;
        } elseif (auth()->user()->isProfessor()) {
            $builder
                ->whereHas('project', function ($q) {
                    $q->whereHas('professors', function ($q) {
                        $q->where('professor_id', '=', auth()->user()->id);
                    });
                });
        } else {
            $builder->where('student_id', '=', auth()->user()->id);
        }

    }
}
