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
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Final Internship Agreements'))
            ->description(__('Manage department assignments for internship agreements in your program'))
            ->headerActions([
                // Add stats grid in the header
                \Filament\Tables\Actions\Action::make('stats')
                    ->label('')
                    ->tooltip(false)
                    ->button()
                    ->view('filament.widgets.program-coordinator.stats', [
                        'stats' => $this->getStats(),
                    ]),
            ])
            ->query(
                Agreement::query()
                    ->whereHas('student', function (Builder $query) {
                        $query->where('program', auth()->user()->assigned_program->value);
                    })
                    ->where('status', Enums\Status::Signed)
                    ->where('assigned_department', null)
            )
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label(__('Student'))
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->student->student_id)
                    ->extraAttributes(['class' => 'animate-fade-in']),

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
                    ->modalContent(fn (Agreement $record): View => view(
                        'filament.resources.final-year-internship-agreement-resource.modal.view',
                        ['record' => $record]
                    ))
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->extraAttributes(['class' => 'transition-transform duration-300 hover:scale-110']),
            ])
            ->recordUrl(
                fn (Agreement $record): string => route('filament.Administration.resources.final-year-internship-agreements.view', ['record' => $record])
            )
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->poll('30s');
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
            ],
            [
                'label' => __('Pending Department'),
                'value' => $baseQuery
                    ->where('status', Enums\Status::Signed)
                    ->whereNull('assigned_department')
                    ->count(),
                'icon' => 'heroicon-o-clock',
                'color' => 'warning',
            ],
            [
                'label' => __('Department Assigned'),
                'value' => $baseQuery
                    ->whereNotNull('assigned_department')
                    ->orWhere('assigned_department', '!=', '')
                    ->count(),
                'icon' => 'heroicon-o-check-badge',
                'color' => 'success',
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
