<?php

namespace App\Services\Filament\Tables\Professors;

use App\Enums\Role;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Summarizers;

class ProfessorsGrid
{
    public static function get()
    {
        return [
            // Tables\Columns\TextColumn::make('title')
            //     ->searchable(),

            Split::make([
                // Grid::make([
                //     'lg' => 2,
                //     '2xl' => 4,
                // ])
                // ->schema([
                Stack::make([
                    Split::make([
                        Tables\Columns\TextColumn::make('name')
                            ->searchable(
                                ['first_name', 'last_name']
                            )
                            ->formatStateUsing(function ($record) {
                                return "$record->long_full_name ({$record->department->getLabel()})";
                            }),
                        // Tables\Columns\TextColumn::make('first_name')
                        //     ->searchable(),
                        // Tables\Columns\TextColumn::make('last_name')
                        //     ->searchable(),
                        // Tables\Columns\TextColumn::make('department')
                        //     ->verticallyAlignCenter()
                        //     ->description(__('Dpeartment'), position: 'above')
                        //     ->alignCenter()
                        //     ->searchable(),
                        Tables\Columns\TextColumn::make('projects_count')
                            ->alignment(Alignment::Center)
                            ->searchable(false)
                            ->summarize([
                                Summarizers\Average::make()
                                    ->numeric(
                                        decimalPlaces: 1,
                                    ),
                                Summarizers\Range::make()
                                    ->label(__('Range')),
                                Summarizers\Sum::make(),
                            ])
                            // ->formatStateUsing(fn ($record) => $record->projects_count . ' ' . __('projects pariticpations'))
                            ->description(__('Number of Projects Participations'), position: 'above')
                            ->label(__('Number of Projects Participations'))
                        // ->label(new HtmlString(__('Number of <br /> Projects Participations')))
                        // ->label(new HtmlString(nl2br("Home \n number")))
                        // ->translateLabel(false)
                            ->alignEnd()
                            ->verticallyAlignCenter()
                            ->sortable()
                            ->counts('projects'),
                    ]),

                    // Tables\Columns\ToggleColumn::make('is_enabled')
                    //     ->toggleable(isToggledHiddenByDefault: true)
                    //     ->sortable(),
                ]),

                // ]),
            ]),
            Panel::make([
                Stack::make([
                    Split::make([
                        Tables\Columns\TextColumn::make('projects.id_pfe')
                            ->badge()
                            ->searchable(false)
                            ->inline()
                            ->columnSpan(3)
                            ->searchable(false)
                            ->sortable(false)
                         // ->formatStateUsing(function ($record) {
                         //     return $record->projects->pluck('internship_agreements')->flatten()->pluck('id_pfe')->implode(', ');
                         // })
                         // ->listWithLineBreaks()
                         // ->bulleted()
                         // ->action(fn ($record) => Pages\EditProfessor::route('/{record}/edit'))
                            ->label(__('Projects IDs'))
                            ->description(__('Projects IDs'), position: 'above'),
                    ]),
                    Split::make([

                        Tables\Columns\TextColumn::make('role')
                            ->searchable()
                            ->formatStateUsing(function ($record) {

                                return ($record->assigned_program) ? $record->role->getLabel() . " ({$record?->assigned_program?->getLabel()})" : (($record->role === Role::Professor) ? null : $record->role->getLabel());
                                // return $record->role->getLabel() . ' (' . $record?->assigned_program?->getLabel() . ')';
                            }),
                    ]),
                    Tables\Columns\TextColumn::make('email')
                        // ->description(__('email'), position: 'above')
                        ->icon('heroicon-m-envelope')
                        ->searchable(),
                ]),
            ])->collapsible(),
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
