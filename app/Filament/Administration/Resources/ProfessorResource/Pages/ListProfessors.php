<?php

namespace App\Filament\Administration\Resources\ProfessorResource\Pages;

use App\Enums;
use App\Filament\Administration\Resources\ProfessorResource;
use App\Models\Professor;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Hydrat\TableLayoutToggle\Concerns\HasToggleableTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ListProfessors extends ListRecords
{
    use HasToggleableTable;

    protected static string $resource = ProfessorResource::class;

    public Collection $ProfessorsByDepartment;

    public function getDefaultLayoutView(): string
    {
        return 'grid';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function __construct()
    {
        $this->ProfessorsByDepartment = Professor::select('department', \DB::raw('count(*) as total'))
            ->groupBy('department')
            ->pluck('total', 'department');
    }

    public function getTabs(): array
    {
        /*     case EMO = 'EMO';
    case MIR = 'MIR';
    case GLC = 'GLC';
    case SC = 'SC';
    case NULL = ''; */
        return [
            'all' => Tab::make()
                ->badge($this->ProfessorsByDepartment->sum()),
            'EMO' => Tab::make(Enums\Department::EMO->value)
                ->badge($this->ProfessorsByDepartment[Enums\Department::EMO->value] ?? 0)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('department', Enums\Department::EMO)),
            'MIR' => Tab::make(Enums\Department::MIR->value)
                ->badge($this->ProfessorsByDepartment[Enums\Department::MIR->value] ?? 0)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('department', Enums\Department::MIR)),
            'GLC' => Tab::make(Enums\Department::GLC->value)
                ->badge($this->ProfessorsByDepartment[Enums\Department::GLC->value] ?? 0)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('department', Enums\Department::GLC)),
            'SC' => Tab::make(Enums\Department::SC->value)
                ->badge($this->ProfessorsByDepartment[Enums\Department::SC->value] ?? 0)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('department', Enums\Department::SC)),

        ];
    }
}
