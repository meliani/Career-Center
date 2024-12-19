<?php

namespace App\Filament\Administration\Resources\ProjectResource\Pages;

use App\Enums\Program;
use App\Filament\Administration\Resources\ProjectResource;
use App\Models\FinalYearInternshipAgreement;
use App\Models\InternshipAgreement;
use App\Models\Year;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Hydrat\TableLayoutToggle\Concerns\HasToggleableTable;

class ListProjects extends ListRecords
{
    use HasToggleableTable;

    protected static string $resource = ProjectResource::class;

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
        $baseQuery = static::getResource()::getEloquentQuery()->active();
        $tabs = [];
        if (auth()->user()->isAdministrator()) {

            // All Projects tab
            $tabs['all'] = Tab::make('All Projects')
                ->badge(
                    $baseQuery->clone()
                        ->whereHas('agreements', function ($query) {
                            $query->whereMorphRelation(
                                'agreeable',
                                [InternshipAgreement::class, FinalYearInternshipAgreement::class],
                                'year_id',
                                Year::current()->id
                            );
                        })->count()
                );

            // Program specific tabs
            foreach (Program::cases() as $program) {
                $label = $program->getLabel();
                $value = $program->value;

                $tabs[$value] = Tab::make($label)
                    ->badge(
                        $baseQuery->clone()
                            ->whereHas('agreements', function ($query) use ($value) {
                                $query->whereHas('agreeable', function ($q) use ($value) {
                                    $q->whereHas('student', fn ($q) => $q->where('program', $value));
                                });
                            })->count()
                    )
                    ->modifyQueryUsing(
                        fn ($query) => $query->whereHas('agreements', function ($q) use ($value) {
                            $q->whereHas('agreeable', function ($q) use ($value) {
                                $q->whereHas('student', fn ($q) => $q->where('program', $value));
                            });
                        })
                    );
            }
        }

        return $tabs;
    }
}
