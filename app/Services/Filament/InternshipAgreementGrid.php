<?php

namespace App\Services\Filament;

use App\Enums\Status;
use App\Models\InternshipAgreement;
use App\Models\Student;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Illuminate\Support\Facades\Mail;

class InternshipAgreementGrid
{
    public static function get()
    {
        return [
            Tables\Columns\TextColumn::make('student.first_name')
                ->label(__('Student first name'))
                ->searchable()
                ->sortable()
                ->formatStateUsing(function ($state, InternshipAgreement $internship) {
                    return $internship->student->title->getLabel().' '.$internship->student->first_name.' '.$internship->student->last_name;
                })
                ->weight(FontWeight::Bold),
            Tables\Columns\TextColumn::make('title')
                ->weight(FontWeight::Bold)
                ->searchable(),
            // Split::make([
            // Stack::make([
            //     // Tables\Columns\TextColumn::make('student_id')
            //     //     ->numeric()
            //     //     ->sortable(),
            //     // add validated_at as date column with d/m/y format
            //     Tables\Columns\TextColumn::make('validated_at')
            //         ->dateTime()
            //         ->sortable()
            //         ->label('Validated at'),
            //     Tables\Columns\TextColumn::make('assigned_department')
            //         ->sortable()
            //         ->label('Assigned department'),
            //     Tables\Columns\TextColumn::make('announced_at')
            //         ->dateTime()
            //         ->sortable(),

            //     Tables\Columns\TextColumn::make('approved_at')
            //         ->dateTime()
            //         ->sortable(),
            //     Tables\Columns\TextColumn::make('signed_at')
            //         ->dateTime()
            //         ->sortable(),
            // ])
            //     ->alignment(Alignment::Start),

            // Stack::make([
            //     Tables\Columns\TextColumn::make('parrain_nom')
            //         ->searchable(),
            //     Tables\Columns\TextColumn::make('parrain_prenom')
            //         ->searchable(),
            //     Tables\Columns\TextColumn::make('parrain_fonction')
            //         ->searchable(),
            //     Tables\Columns\TextColumn::make('parrain_tel')
            //         ->searchable()->icon('heroicon-m-phone'),
            //     Tables\Columns\TextColumn::make('parrain_mail')
            //         ->searchable()->icon('heroicon-m-envelope'),
            // ]),

            Split::make([
                Stack::make([
                    Tables\Columns\TextColumn::make('organization_name')
                        ->searchable(),
                    // Tables\Columns\TextColumn::make('adresse')
                    //     ->searchable(),
                    // Tables\Columns\TextColumn::make('city')
                    //     ->searchable(),
                    Tables\Columns\TextColumn::make('country')
                        ->searchable(),
                ]),
                // Tables\Columns\TextColumn::make('office_location')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('parrain_titre')
                //     ->searchable(),
                Stack::make([
                    Tables\Columns\TextColumn::make('starting_at')
                        ->date()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('ending_at')
                        ->date()
                        ->sortable(),
                ])->alignment(Alignment::End),
            ]),
            Panel::make([

                Stack::make([
                    // Tables\Columns\TextColumn::make('encadrant_ext_titre')
                    //     ->searchable(),
                    Tables\Columns\TextColumn::make('encadrant_ext_nom')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('encadrant_ext_prenom')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('encadrant_ext_fonction')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('encadrant_ext_tel')
                        ->searchable()->icon('heroicon-m-phone'),
                    Tables\Columns\TextColumn::make('encadrant_ext_mail')
                        ->searchable()->icon('heroicon-m-envelope')
                        ->copyable()
                        ->copyMessage('Email address copied'),
                ])->grow(true)->alignment(Alignment::End),
            ])->collapsible(),

            // Tables\Columns\IconColumn::make('abroad')
            //     ->boolean(),
            // Tables\Columns\TextColumn::make('remuneration')
            //     ->searchable(),
            // Tables\Columns\TextColumn::make('currency')
            //     ->searchable(),
            // Tables\Columns\TextColumn::make('load')
            //     ->searchable(),
            // Tables\Columns\TextColumn::make('abroad_school')
            //     ->searchable(),
            // Tables\Columns\TextColumn::make('int_adviser_id')
            //     ->numeric()
            //     ->sortable(),
            // Tables\Columns\TextColumn::make('int_adviser_name')
            //     ->searchable(),
            // Tables\Columns\IconColumn::make('is_signed')
            //     ->boolean(),
            // Tables\Columns\TextColumn::make('year_id')
            //     ->numeric()
            //     ->sortable(),
            // Tables\Columns\TextColumn::make('binome_user_id')
            //     ->numeric()
            //     ->sortable(),
            Tables\Columns\IconColumn::make('is_valid')
                ->label(__('Validated by student'))
                ->boolean(),
            // Tables\Columns\TextColumn::make('status')
            //     ->searchable(),

            // Tables\Columns\TextColumn::make('partner_internship_id')
            //     ->numeric()
            //     ->sortable(),
            // Tables\Columns\TextColumn::make('partnership_status')
            //     ->searchable(),
            // Tables\Columns\TextColumn::make('created_at')
            //     ->dateTime()
            //     ->sortable()
            //     ->toggleable(isToggledHiddenByDefault: true),
            // Tables\Columns\TextColumn::make('updated_at')
            //     ->dateTime()
            //     ->sortable()
            //     ->toggleable(isToggledHiddenByDefault: true),
            // Tables\Columns\TextColumn::make('deleted_at')
            //     ->dateTime()
            //     ->sortable()
            //     ->toggleable(isToggledHiddenByDefault: true),
            // ]),
        ];
    }
}
