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
                ->label(__('ID'))
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('student.first_name')
                ->label(__('Student name'))
                ->searchable()
                ->sortable()
                ->formatStateUsing(function ($state, InternshipAgreement $internship) {
                    return $internship->student->title->getLabel().' '.$internship->student->first_name.' '.$internship->student->last_name;
                }),
            Tables\Columns\TextColumn::make('organization_name')
                ->label(__('Organization'))
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('parrain_nom')
                ->label(__('Parrain'))
                ->formatStateUsing(function ($state, InternshipAgreement $internship) {
                    return $internship->parrain_titre->getLabel().' '.$internship->parrain_nom.' '.$internship->parrain_prenom;
                })
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('encadrant_ext_nom')
                ->label(__('Encadrant'))
                ->formatStateUsing(function ($state, InternshipAgreement $internship) {
                    return $internship->encadrant_ext_titre->getLabel().' '.$internship->encadrant_ext_nom.' '.$internship->encadrant_ext_prenom;
                })
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('status')
                ->label(__('Status'))
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('announced_at')
                ->label(__('Announced'))
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('validated_at')
                ->label(__('Validated'))
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('signed_at')
                ->label(__('Signed'))
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('received_at')
                ->label(__('Received'))
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('starting_at')
                ->label(__('Start'))
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('ending_at')
                ->label(__('End'))
                ->searchable()
                ->sortable(),
        ];
    }
}
