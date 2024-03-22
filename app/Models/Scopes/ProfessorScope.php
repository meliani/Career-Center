<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ProfessorScope implements Scope
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
                    ->whereHas('projects', function ($q) {
                        $q->whereHas('internship_agreements', function ($q) {
                            $q->whereHas('student', function ($q) {
                                $q->where('program', '=', auth()->user()->assigned_program);
                            });
                        });
                    });

                return;
            } elseif (auth()->user()->isDepartmentHead()) {
                $builder
                    ->where('department', '=', auth()->user()->department);

                return;
            } elseif (auth()->user()->isProfessor()) {
                $builder
                    ->where('id', '=', auth()->user()->id);
            } else {
                abort(403, 'You are not authorized to view this page');
            }

        }
    }
}
