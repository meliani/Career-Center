<?php

namespace App\Filament\Administration\Resources\UserResource\Pages;

use App\Enums;
use App\Filament\Administration\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('all')
                ->label(__('All')),
            'administrators' => Tab::make(__('Administrators'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('role', Enums\Role::getAdministratorRoles())),
            'professors' => Tab::make(__('Professors'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('role', Enums\Role::getProfessorRoles())),
            'assigned_programs' => Tab::make(__('Program Coordinators'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('role', Enums\Role::getProgramCoordinatorRoles())),
            'department_heads' => Tab::make(__('Department Heads'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('role', Enums\Role::getDepartmentHeadRoles())),
            'administrative_supervisors' => Tab::make(__('Administrative Supervisors'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('role', Enums\Role::getAdministrativeSupervisorRoles())),
            'direction' => Tab::make(__('Direction'))
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('role', Enums\Role::getDirectionRoles())),
        ];

    }
}
