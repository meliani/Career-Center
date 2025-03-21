<?php

namespace App\Services\Filament;

use App\Models\InternshipAgreement;
use Filament\Tables;

class InternshipAgreementTable
{
    public static function get()
    {
        return [
            // Tables\Columns\TextColumn::make('id')
            //     ->toggleable(isToggledHiddenByDefault: true)
            //     ->label(__('ID'))
            //     ->searchable()
            //     ->sortable(),
            // Tables\Columns\TextColumn::make('id_pfe')
            //     ->label(__('PFE ID'))
            //     ->searchable()
            //     ->sortable(),

            Tables\Columns\TextColumn::make('student.long_full_name')
                ->label(__('Student name'))
                ->searchable(
                    ['first_name', 'last_name']
                )->sortable()
                ->description(fn (InternshipAgreement $internship) => $internship->student->id_pfe),
            // ->formatStateUsing(function ($state, InternshipAgreement $internship) {
            //     return $internship->student->title->getLabel() . ' ' . $internship->student->first_name . ' ' . $internship->student->last_name;
            // }),
            Tables\Columns\TextColumn::make('student.email_perso')
                ->label(__('Student personal email'))
                ->searchable(false)
                ->sortable(false)
                ->visibleFrom('md'),
            Tables\Columns\TextColumn::make('student.program')
                ->label(__('Student program'))
                ->searchable(false)
                ->sortable(false)
                ->visibleFrom('md'),
            Tables\Columns\TextColumn::make('starting_at')
                ->label(__('Start'))
                ->searchable()
                ->sortable()
                ->date()
                ->visibleFrom('md'),
            Tables\Columns\TextColumn::make('ending_at')
                ->label(__('End'))
                ->searchable()
                ->sortable()
                ->date(),
            Tables\Columns\TextColumn::make('duration_in_months')
                ->searchable(false)
                ->label(__('Duration')),
            // Tables\Columns\TextColumn::make('central_organization')
            //     ->toggleable(isToggledHiddenByDefault: true)
            //     ->label('Central organization')
            //     ->searchable()
            //     ->sortable(),

            Tables\Columns\TextColumn::make('organization.name')
                ->label('Organization')
                ->searchable()
                ->sortable()
                ->description(fn (InternshipAgreement $internship) => $internship->country . ' - ' . $internship->city),
            // Tables\Columns\TextColumn::make('city')
            //     ->label('City')
            //     ->searchable()
            //     ->sortable(),
            // Tables\Columns\TextColumn::make('country')
            //     ->label('Country')
            //     ->searchable()
            //     ->sortable()
            //     ->description(fn (InternshipAgreement $internship) => $internship->city),

            Tables\Columns\TextColumn::make('title')
                ->label(__('Title'))
                ->searchable()
                ->sortable()
                ->wrap()
                ->lineClamp(2)
                ->tooltip(
                    fn (InternshipAgreement $internship) => $internship->title,
                ),
            Tables\Columns\TextColumn::make('description')
                ->label(__('Description'))
                ->searchable()
                ->width('xl')
                ->sortable()
                ->wrap()
                ->lineClamp(2)
                ->tooltip(
                    fn (InternshipAgreement $internship) => $internship->description,
                ),
            // Tables\Columns\TextColumn::make('parrain_nom')
            //     ->toggleable(isToggledHiddenByDefault: true)
            //     ->label(__('Parrain'))
            //     ->formatStateUsing(function ($state, InternshipAgreement $internship) {
            //         return $internship->parrain_titre?->getLabel() . ' ' . $internship->parrain_nom . ' ' . $internship->parrain_prenom;
            //     })
            //     ->searchable()
            //     ->sortable(),
            // Tables\Columns\TextColumn::make('encadrant_ext_nom')
            //     ->toggleable(isToggledHiddenByDefault: true)
            //     ->label('Encadrant')
            //     ->formatStateUsing(function ($state, InternshipAgreement $internship) {
            //         return $internship->encadrant_ext_titre?->getLabel() . ' ' . $internship->encadrant_ext_nom . ' ' . $internship->encadrant_ext_prenom;
            //     })
            //     ->searchable()
            //     ->sortable(),
            Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('project.defense_status')
                ->label('Defense status')
                ->searchable(false)
                ->sortable(false),
            Tables\Columns\TextColumn::make('project.professors.name')
                ->label('Supervisor - Reviewer')
                ->searchable()
                ->sortable(false),
            Tables\Columns\TextColumn::make('assigned_department')
                ->label('Assigned department')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('remuneration')
                ->label('Remuneration')
                ->searchable(false)
                ->sortable(false),
            Tables\Columns\TextColumn::make('currency')
                ->label('Currency')
                ->searchable(false)
                ->sortable(false),
            Tables\Columns\TextColumn::make('load')
                ->label('Hour per week')
                ->searchable(false)
                ->sortable(false),

            Tables\Columns\TextColumn::make('announced_at')
                ->toggleable(isToggledHiddenByDefault: true)
                ->label('Announced')
                ->searchable()
                ->sortable()
                ->date(),
            Tables\Columns\TextColumn::make('validated_at')
                ->toggleable(isToggledHiddenByDefault: true)
                ->label('Validated')
                ->searchable()
                ->sortable()
                ->date(),
            Tables\Columns\TextColumn::make('signed_at')
                ->toggleable(isToggledHiddenByDefault: true)
                ->label('Signed')
                ->searchable()
                ->sortable()
                ->date(),
            Tables\Columns\TextColumn::make('received_at')
                ->toggleable(isToggledHiddenByDefault: true)
                ->label('Achieved')
                ->searchable()
                ->sortable()
                ->date(),

        ];
    }
}
