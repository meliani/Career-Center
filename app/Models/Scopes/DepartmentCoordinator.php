<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class DepartmentCoordinator implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder
        // ->where('assigned_department', '=', auth()->user()->department)
        ->whereHas('student', function ($q) {
            $q->where('program', '=',auth()->user()->program_coordinator);
        });

    }
}
