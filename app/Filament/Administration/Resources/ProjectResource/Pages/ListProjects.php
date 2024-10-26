<?php

namespace App\Filament\Administration\Resources\ProjectResource\Pages;

use App\Enums;
use App\Filament\Administration\Resources\ProjectResource;
use App\Models\Student;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Hydrat\TableLayoutToggle\Concerns\HasToggleableTable;
use Illuminate\Support\Collection;

class ListProjects extends ListRecords
{
    use HasToggleableTable;

    protected static string $resource = ProjectResource::class;

    public Collection $ProjectsByProgram;

    public function __construct()
    {
        $this->ProjectsByProgram = Student::whereHas('projects')->select('program', \DB::raw('count(*) as total'))
            ->groupBy('program')
            ->pluck('total', 'program');
    }

    public function getDefaultLayoutView(): string
    {
        return 'list';
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [];
        foreach (Enums\Program::getArray() as $program) {
            // $program = $program_enum->getLabel();
            $tabs[$program] = Tab::make($program)
                ->badge($this->ProjectsByProgram[$program] ?? 0)
                ->modifyQueryUsing(fn ($query) => $query->whereHas('students', fn ($q) => $q->where('program', $program)));
        }

        // merge tabs with all
        $tabs = array_merge(['all' => Tab::make()], $tabs);

        return $tabs;
    }
}
