<?php

namespace App\Services\Filament;

use App\Models\InternshipAgreement;
use Filament\Tables;

class InternshipAgreementTable
{
    public static function get()
    {
        return [
            Tables\Columns\TextColumn::make('id')
                ->toggleable(isToggledHiddenByDefault: true)
                ->label(__('ID'))
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('id_pfe')
                ->label(__('PFE ID'))
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('student.full_name')
                ->label(__('Student name'))
                ->searchable()
                ->sortable(),
            // ->formatStateUsing(function ($state, InternshipAgreement $internship) {
            //     return $internship->student->title->getLabel() . ' ' . $internship->student->first_name . ' ' . $internship->student->last_name;
            // }),

            Tables\Columns\TextColumn::make('organization_name')
                ->label(__('Organization'))
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('city')
                ->label(__('City'))
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('country')
                ->label(__('Country'))
                ->searchable()
                ->sortable(),

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
                ->sortable()
                ->wrap()
                ->lineClamp(2)
                ->tooltip(
                    fn (InternshipAgreement $internship) => $internship->description,
                ),
            Tables\Columns\TextColumn::make('parrain_nom')
                ->toggleable(isToggledHiddenByDefault: true)
                ->label(__('Parrain'))
                ->formatStateUsing(function ($state, InternshipAgreement $internship) {
                    return $internship->parrain_titre->getLabel() . ' ' . $internship->parrain_nom . ' ' . $internship->parrain_prenom;
                })
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('encadrant_ext_nom')
                ->toggleable(isToggledHiddenByDefault: true)
                ->label(__('Encadrant'))
                ->formatStateUsing(function ($state, InternshipAgreement $internship) {
                    return $internship->encadrant_ext_titre->getLabel() . ' ' . $internship->encadrant_ext_nom . ' ' . $internship->encadrant_ext_prenom;
                })
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('status')
                ->label(__('Status'))
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('project.professors.name')
                ->label(__('Supervisor - Reviewer'))
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('assigned_department')
                ->label(__('Assigned department'))
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('announced_at')
                ->toggleable(isToggledHiddenByDefault: true)
                ->label(__('Announced'))
                ->searchable()
                ->sortable()
                ->date(),
            Tables\Columns\TextColumn::make('validated_at')
                ->toggleable(isToggledHiddenByDefault: true)
                ->label(__('Validated'))
                ->searchable()
                ->sortable()
                ->date(),
            Tables\Columns\TextColumn::make('signed_at')
                ->toggleable(isToggledHiddenByDefault: true)
                ->label(__('Signed'))
                ->searchable()
                ->sortable()
                ->date(),
            Tables\Columns\TextColumn::make('received_at')
                ->toggleable(isToggledHiddenByDefault: true)
                ->label(__('Received'))
                ->searchable()
                ->sortable()
                ->date(),
            Tables\Columns\TextColumn::make('starting_at')
                ->label(__('Start'))
                ->searchable()
                ->sortable()
                ->date(),
            Tables\Columns\TextColumn::make('ending_at')
                ->date()
                ->label(__('End'))
                ->searchable()
                ->sortable(),
        ];
    }
}
