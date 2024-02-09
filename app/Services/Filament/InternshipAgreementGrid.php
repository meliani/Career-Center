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
            Split::make([
                Stack::make([
                    Tables\Columns\TextColumn::make('organization_name')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('country')
                        ->searchable(),
                ]),
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
            Tables\Columns\IconColumn::make('is_valid')
                ->label(__('Validated by student'))
                ->boolean(),
        ];
    }
}
