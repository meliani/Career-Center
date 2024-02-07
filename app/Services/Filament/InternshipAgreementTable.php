<?php

namespace App\Services\Filament;

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
            Tables\Columns\TextColumn::make('student.long_full_name')
                ->label(__('Student'))
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('organization_name')
                ->label(__('Organization'))
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('parrain_nom')
                ->label(__('Parrain'))
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('encadrant_ext_nom')
                ->label(__('Encadrant'))
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
