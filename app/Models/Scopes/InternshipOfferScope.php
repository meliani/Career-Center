<?php

namespace App\Models\Scopes;

use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class InternshipOfferScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->check()) {
            if (Auth::user() instanceof Student) {
                // check student level with internship_level, FirstYear,2,3 and  FinalYearInternship TechnicalInternship' 'IntroductoryInternship';

                if (Auth::user()->level->value == 'FirstYear' || Auth::user()->level->value == 'SecondYear') {
                    $builder->where('internship_level', '=', 'IntroductoryInternship')
                        ->orWhere('internship_level', '=', 'TechnicalInternship');
                } elseif (Auth::user()->level->value == 'ThirdYear') {
                    $builder->where('internship_level', '=', 'FinalYearInternship');
                }

                $builder->where('status', '=', 'Published');

                return;

            } elseif (auth()->user()->isProfessor() || auth()->user()->isDepartmentHead() || auth()->user()->isProgramCoordinator() || auth()->user()->isSuperAdministrator() || auth()->user()->isAdministrator() || auth()->user()->isDirection()) {
                return;
            } else {

                return;
            }
        }
    }
}
