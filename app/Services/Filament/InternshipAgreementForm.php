<?php

namespace App\Services\Filament;

use App\Enums;
use App\Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;

class InternshipAgreementForm
{
    public static function get()
    {
        return [
            //     \Filament\Forms\Components\Actions::make([
            //         // Actions\CreateProjectFromInternshipAgreement::make('Create Project From Internship Agreement'),
            //         //     ->disabled(fn (RelationManager $livewire): bool =>
            //         //     $livewire->getOwnerRecord()?->student->projects->count() >= 1
            //         // ),
            //         // ->disabled(fn ($record): bool => !isset($record->student->projects)),
            //     ]),
            // ->disabled(! auth()->user()->can('delete', $this->post)),
            Section::make(__('Student informations'))
                ->collapsible()
                ->schema([
                    Fieldset::make(__('Student'))
                        ->relationship('student')
                        ->schema([
                            Forms\Components\TextInput::make('first_name')
                                ->disabled(),
                            Forms\Components\TextInput::make('last_name')
                                ->disabled(),
                            Forms\Components\Select::make('level')
                                ->options(Enums\StudentLevel::class)
                                ->disabled(),
                            Forms\Components\Select::make('program')
                                ->options(Enums\Program::class)
                                ->disabled()
                                ->columnSpan(2),
                        ])->columns(3),
                ]),
            Section::make(__('Reserved for administration'))
                ->description(__('Validation and signature informations.'))
                ->schema([
                    Forms\Components\DateTimePicker::make('validated_at'),
                    Forms\Components\DateTimePicker::make('received_at'),
                    Forms\Components\DateTimePicker::make('signed_at'),
                    Forms\Components\Textarea::make('observations')
                        ->maxLength(65535),
                    Forms\Components\Select::make('assigned_department')
                        ->options(Enums\Department::class),
                    Forms\Components\ToggleButtons::make('status')
                        ->label(__('Status'))
                        ->options(Enums\Status::class)
                        ->inline()
                        ->required()
                        ->columnSpanFull(),
                ])
                ->collapsible()
                ->columns(2),
            Section::make(__('Internship informations'))
                ->description(__('Project title, organization details and internship dates.'))
                // ->compact()
                ->schema([
                    Forms\Components\DateTimePicker::make('announced_at'),
                    Forms\Components\TextInput::make('organization_name')
                        ->required()
                        ->maxLength(191),
                    Forms\Components\TextInput::make('central_organization')
                        ->maxLength(191),

                    Forms\Components\Textarea::make('title')
                        ->required()
                        ->maxLength(65535)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('description')
                        ->required()
                        ->maxLength(65535)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('keywords')
                        ->required()
                        ->maxLength(65535),
                    Forms\Components\DatePicker::make('starting_at')
                        ->required(),
                    Forms\Components\DatePicker::make('ending_at')
                        ->required(),
                    Forms\Components\TextInput::make('id_pfe'),
                ])
                ->columns(2)
                ->collapsed(),
            Section::make(__('Other Internship details'))
                ->description(__('Organization contacts details and external supervisor.'))
                ->schema([
                    Forms\Components\TextInput::make('adresse')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(2),
                    Forms\Components\TextInput::make('city')
                        ->required()
                        ->maxLength(191),
                    Forms\Components\TextInput::make('country')
                        ->required()
                        ->maxLength(191),
                    Forms\Components\TextInput::make('office_location')
                        ->maxLength(255)
                        ->columnSpan(2),
                    Fieldset::make(__('Parrain'))
                        ->schema([
                            Forms\Components\Select::make('parrain_titre')
                                ->options(Enums\Title::class)
                                ->required(),
                            Forms\Components\TextInput::make('parrain_nom')
                                ->required()
                                ->maxLength(191),
                            Forms\Components\TextInput::make('parrain_prenom')
                                ->required()
                                ->maxLength(191),
                            Forms\Components\TextInput::make('parrain_fonction')
                                ->required()
                                ->maxLength(191),
                            Forms\Components\TextInput::make('parrain_tel')
                                ->tel()
                                ->required()
                                ->maxLength(191),
                            Forms\Components\TextInput::make('parrain_mail')
                                ->required()
                                ->maxLength(191),
                        ]),
                    Fieldset::make(__('External supervisor'))
                        ->schema([
                            Forms\Components\Select::make('encadrant_ext_titre')
                                ->options(Enums\Title::class)
                                ->required(),
                            Forms\Components\TextInput::make('encadrant_ext_nom')
                                ->required()
                                ->maxLength(191),
                            Forms\Components\TextInput::make('encadrant_ext_prenom')
                                ->required()
                                ->maxLength(191),
                            Forms\Components\TextInput::make('encadrant_ext_fonction')
                                ->required()
                                ->maxLength(191),
                            Forms\Components\TextInput::make('encadrant_ext_tel')
                                ->tel()
                                ->required()
                                ->maxLength(191),
                            Forms\Components\TextInput::make('encadrant_ext_mail')
                                ->required()
                                ->maxLength(191),
                        ]),
                    Fieldset::make(__('Remuneration'))
                        ->schema([
                            Forms\Components\TextInput::make('remuneration')
                                ->maxLength(191),
                            Forms\Components\TextInput::make('currency')
                                ->maxLength(10),
                            Forms\Components\TextInput::make('load')
                                ->maxLength(191),
                            Forms\Components\TextInput::make('int_adviser_name')
                                ->maxLength(191),
                            // Forms\Components\TextInput::make('year_id')
                            //     ->numeric(),
                        ]),
                ])->collapsed()
                ->columns(3),
        ];
    }
}
