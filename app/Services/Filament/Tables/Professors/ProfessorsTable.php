<?php

namespace App\Services\Filament\Tables\Professors;

use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers;

class ProfessorsTable
{
    public static function get()
    {
        return [
            // Tables\Columns\TextColumn::make('title')
            //     ->searchable(),
            Tables\Columns\TextColumn::make('name')
                ->searchable(
                    ['first_name', 'last_name']
                )
                ->formatStateUsing(function ($record) {
                    return $record->long_full_name;
                }),
            // Tables\Columns\TextColumn::make('first_name')
            //     ->searchable(),
            // Tables\Columns\TextColumn::make('last_name')
            //     ->searchable(),
            Tables\Columns\TextColumn::make('department')
                ->searchable(),
            Tables\Columns\TextColumn::make('projects_count')
                ->alignment(Alignment::Center)
                ->searchable(false)

                ->summarize([
                    Summarizers\Average::make()->numeric(
                        decimalPlaces: 1,
                    ),
                    Summarizers\Range::make()
                        ->label(__('Range')),
                    Summarizers\Sum::make(),
                ])
                ->label(__('Number of Projects Participations'))
                // ->label(new HtmlString(__('Number of <br /> Projects Participations')))
                // ->label(new HtmlString(nl2br("Home \n number")))
                // ->translateLabel(false)
                ->alignCenter()
                ->sortable()
                ->counts('projects'),
            Tables\Columns\TextColumn::make('projects.id_pfe')
                ->badge()
                ->searchable(false)
                ->inline()
                ->columnSpan(3)
                // ->formatStateUsing(function ($record) {
                //     return $record->projects->pluck('internship_agreements')->flatten()->pluck('id_pfe')->implode(', ');
                // })
                // ->listWithLineBreaks()
                // ->bulleted()
                // ->action(fn ($record) => Pages\EditProfessor::route('/{record}/edit'))
                ->label(__('Projects')),
            Tables\Columns\TextColumn::make('role')
                ->searchable(),
            Tables\Columns\TextColumn::make('email')
                ->searchable(),
            Tables\Columns\TextColumn::make('assigned_program')
                ->label(__('Program Coordinator Program'))
                ->searchable(),
            Tables\Columns\ToggleColumn::make('is_enabled')
                ->toggleable(isToggledHiddenByDefault: true)
                ->sortable(),
            // Tables\Columns\TextColumn::make('email_verified_at')
            //     ->dateTime()
            //     ->sortable(),
            // Tables\Columns\TextColumn::make('created_at')
            //     ->dateTime()
            //     ->sortable()
            //     ->toggleable(isToggledHiddenByDefault: true),
            // Tables\Columns\TextColumn::make('updated_at')
            //     ->dateTime()
            //     ->sortable()
            //     ->toggleable(isToggledHiddenByDefault: true),
            // Tables\Columns\IconColumn::make('active_status')
            //     ->boolean(),
            // Tables\Columns\TextColumn::make('avatar')
            //     ->toggleable(isToggledHiddenByDefault: true)
            //     ->searchable(),
            // Tables\Columns\IconColumn::make('dark_mode')
            //     ->boolean(),
            // Tables\Columns\TextColumn::make('messenger_color')
            //     ->searchable(),
        ];
    }
}
