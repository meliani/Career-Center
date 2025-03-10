<?php

namespace App\Models\Scopes;

use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class ProfessorScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->check()) {
            if (Auth::user() instanceof Student) {
                // $builder->select('first_name', 'last_name', 'department');

                return;
            } elseif (auth()->user()->isSuperAdministrator() || auth()->user()->isAdministrator() || auth()->user()->isDirection() || auth()->user()->isAdministrativeSupervisor()) {
                return;
            } elseif (auth()->user()->isProgramCoordinator()) {
                $builder
                    ->whereHas('projects', function ($q) {
                        $q->whereHas('final_year_internship_agreements', function ($q) {
                            $q->whereHas('student', function ($q) {
                                $q->where('program', '=', auth()->user()->assigned_program);
                            });
                        });
                    });

                // return;
            } elseif (auth()->user()->isDepartmentHead()) {
                $builder
                    ->where('department', '=', auth()->user()->department);

                // return;
            } elseif (auth()->user()->isProfessor()) {
                $builder->where('department', '=', auth()->user()->department);
                // ->whereHas('projects', function ($query) {
                //     // Assuming there's a 'professors' relationship within 'projects'
                //     $query->whereHas('professors', function ($query) {
                //         // Filter projects to those the current professor is involved in
                //         $query->where('id', '=', auth()->user()->id);
                //     });
                // });

                // return;
                // $builder
                //     ->where('id', '=', auth()->user()->id);
            } elseif (auth()->user()->isAdministrativeSupervisor()) {
                return;
            } else {
                abort(403, 'You are not authorized to view this page');
            }

        }
    }
}
