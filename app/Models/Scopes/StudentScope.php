<?php

namespace App\Models\Scopes;

use App\Models\Student;
use App\Models\Year;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class StudentScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model, ?Student $student = null): void
    {
        // if ($model instanceof \App\Models\Project) {
        //     $builder->with('students');

        //     return;
        // }
        if (auth()->check()) {
            if (Auth::guard('students')->check()) {
                $builder->where('id', '=', auth()->user()->id);

                return;
            } elseif (Auth::guard('web')->check()) {
                if (auth()->user()->isSuperAdministrator() || auth()->user()->isAdministrator() || auth()->user()->isDirection()) {
                    $builder->where('year_id', '=', Year::current()->id);

                    return;
                } elseif (auth()->user()->isProgramCoordinator()) {
                    $builder->where('program', '=', auth()->user()->assigned_program);

                    return;
                } elseif (auth()->user()->isDepartmentHead()) {
                    $builder
                        ->whereHas('active_internship_agreement', function ($q) {
                            $q->where('assigned_department', '=', auth()->user()->department)
                                ->where('year_id', '=', Year::current()->id);
                        });

                    return;
                } elseif (auth()->user()->isProfessor()) {
                    $builder
                        ->whereHas('projects', function ($q) {
                            $q->whereHas('professors', function ($q) {
                                $q->where('professor_id', '=', auth()->user()->id)
                                    ->where('year_id', '=', Year::current()->id);
                            });
                        });

                    return;
                }
            } else {
                abort(403, 'You are not authorized to view this page');
            }
        }

    }
}
