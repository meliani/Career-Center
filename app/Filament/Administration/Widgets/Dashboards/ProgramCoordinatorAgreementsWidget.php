<?php

namespace App\Filament\Administration\Widgets\Dashboards;

use App\Enums;
use App\Models\FinalYearInternshipAgreement as Agreement;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class ProgramCoordinatorAgreementsWidget extends BaseWidget
{
    public ?string $activeFilter = null;

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function filterByStat(?string $filter): void
    {
        $this->activeFilter = $this->activeFilter === $filter ? null : $filter;
        $this->resetTable();
    }

    protected function getTableQuery(): Builder
    {
        $query = Agreement::query()
            ->with(['student', 'organization'])
            ->whereHas('student', function (Builder $query) {
                $query->where('program', auth()->user()->assigned_program->value);
            });

        return match ($this->activeFilter) {
            'pending' => $query->where('status', Enums\Status::Signed)
                ->whereNull('assigned_department'),
            'assigned' => $query->whereNotNull('assigned_department')
                ->where('assigned_department', '!=', ''),
            default => $query,
        };
    }

    public function table(Table $table): Table
    {
        // $filterLabel = match ($this->activeFilter) {
        //     'pending' => __('Pending Assignment'),
        //     'assigned' => __('Department Assigned'),
        //     default => null,
        // };

        return $table
            // ->heading($filterLabel)
            ->heading(false)
            ->headerActions([
                \Filament\Tables\Actions\Action::make('help')
                    ->label('')
                    ->tooltip(false)
                    ->button()
                    ->view('filament.widgets.program-coordinator.help-message'),

                \Filament\Tables\Actions\Action::make('stats')
                    ->label('')
                    ->tooltip(false)
                    ->button()
                    ->view('filament.widgets.program-coordinator.stats', [
                        'stats' => $this->getStats(),
                        'activeFilter' => $this->activeFilter,
                    ]),
            ])
            ->query(fn () => $this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label(__('Student'))
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->student->student_id)
                    ->extraAttributes([
                        'class' => 'animate-fade-in',
                        'wire:loading.class' => 'opacity-50',
                    ]),

                Tables\Columns\TextColumn::make('organization.name')
                    ->label(__('Organization'))
                    ->searchable()
                    ->sortable()
                    ->extraAttributes(['class' => 'animate-slide-in-right']),

                Tables\Columns\SelectColumn::make('assigned_department')
                    ->label(__('Department'))
                    ->options(Enums\Department::class)
                    ->searchable()
                    ->extraAttributes([
                        'class' => 'transition-all duration-300 hover:scale-105',
                    ])
                    ->afterStateUpdated(function ($record) {
                        $record->save();
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Submitted'))
                    ->dateTime()
                    ->sortable()
                    ->extraAttributes(['class' => 'animate-fade-in']),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('assigned_department')
                    ->label(__('Department'))
                    ->options(Enums\Department::class),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-magnifying-glass')
                    ->modalContent(fn (Agreement $record): View => view(
                        'filament.resources.final-year-internship-agreement-resource.modal.view',
                        ['record' => $record]
                    ))
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->extraAttributes(['class' => 'transition-transform duration-300 hover:scale-110']),
            ], position: Tables\Enums\ActionsPosition::BeforeColumns)
            ->recordUrl(
                fn (Agreement $record): string => route('filament.Administration.resources.final-year-internship-agreements.view', ['record' => $record])
            )
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->poll('30s')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', '!=', null));
    }

    protected function getStats(): array
    {
        $baseQuery = Agreement::query()
            ->whereHas('student', function (Builder $query) {
                $query->where('program', auth()->user()->assigned_program->value);
            });

        return [
            [
                'label' => __('Total Agreements'),
                'value' => $baseQuery->count(),
                'icon' => 'heroicon-o-document-text',
                'color' => 'primary',
                'filter' => 'all',
            ],
            [
                'label' => __('Pending Department Assignment'),
                'value' => (clone $baseQuery)
                    ->where('status', Enums\Status::Signed)
                    ->whereNull('assigned_department')
                    ->orWhere('assigned_department', '')
                    ->count(),
                'icon' => 'heroicon-o-clock',
                'color' => 'warning',
                'filter' => 'pending',
            ],
            [
                'label' => __('Department Assigned'),
                'value' => (clone $baseQuery)
                    ->whereNotNull('assigned_department')
                    ->where('assigned_department', '!=', '')
                    ->count(),
                'icon' => 'heroicon-o-check-badge',
                'color' => 'success',
                'filter' => 'assigned',
            ],
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole(Enums\Role::ProgramCoordinator);
    }

    protected function getTableContentFooter(): ?View
    {
        return view('filament.widgets.program-coordinator.footer');
    }
}
