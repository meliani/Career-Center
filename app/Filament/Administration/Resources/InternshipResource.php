<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Administration\Resources\InternshipResource\Pages;
use App\Models\Internship;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Hydrat\TableLayoutToggle\Concerns\HasToggleableTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Mail;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Models\Student;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Actions\ActionGroup;


class InternshipResource extends Resource
{
    // use HasToggleableTable;

    protected static ?string $model = Internship::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('student_id')
                    ->numeric(),
                    Forms\Components\TextInput::make('id_pfe')
                    ->numeric(),
                Forms\Components\TextInput::make('organization_name')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('adresse')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('city')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('country')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('office_location')
                    ->maxLength(255),
                Forms\Components\TextInput::make('parrain_titre')
                    ->required()
                    ->maxLength(191),
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
                Forms\Components\TextInput::make('encadrant_ext_titre')
                    ->required()
                    ->maxLength(191),
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
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('starting_at')
                    ->required(),
                Forms\Components\DatePicker::make('ending_at')
                    ->required(),
                Forms\Components\TextInput::make('remuneration')
                    ->maxLength(191),
                Forms\Components\TextInput::make('currency')
                    ->maxLength(10),
                Forms\Components\TextInput::make('load')
                    ->maxLength(191),
                Forms\Components\TextInput::make('int_adviser_name')
                    ->maxLength(191),
                Forms\Components\TextInput::make('year_id')
                    ->numeric(),
                Forms\Components\Toggle::make('is_valid')
                ->label('Student validation'),
                Forms\Components\ToggleButtons::make('status')
                ->options([
                    'Draft' => __('Draft'),
                    'Announced' => __('Announced'),
                    'Validated' => __('Validated'),
                    'Approved' => __('Approved'),
                    'Signed' => __('Signed'),
                ]),
                Forms\Components\Select::make('assigned_department')
                    ->options([
                        'SC' => 'SC',
                        'MIR' => 'MIR',
                        'EMO' => 'EMO',
                        'GLC' => 'GLC',
                    ]),                    
                Forms\Components\DateTimePicker::make('announced_at'),
                Forms\Components\DateTimePicker::make('validated_at'),
                Forms\Components\DateTimePicker::make('received_at'),
                Forms\Components\DateTimePicker::make('signed_at'),
                Forms\Components\TextInput::make('project_id')
                    ->numeric(),
                // Forms\Components\TextInput::make('binome_user_id')
                //     ->numeric(),
                Forms\Components\Select::make('binome_user_id')
                    ->options(function () {
                        return Student::all()->pluck('full_name', 'id');
                    }),
                Forms\Components\TextInput::make('partner_internship_id')
                    ->numeric(),
                Forms\Components\TextInput::make('partnership_status')
                    ->maxLength(50),
            ]);
    }

    public static function table(Table $table): Table
    {
        $livewire = $table->getLivewire();

        return $table
            ->groups([
                Group::make(__('status'))
                    ->collapsible()
                    ->titlePrefixedWithLabel(false),
                Group::make('student.program')
                    ->label(__('Program'))
                    ->collapsible(),
                // ->titlePrefixedWithLabel(false)
                // ->getTitleFromRecordUsing(fn (Internship $record): string => ucfirst($record->program)),
            ])
            ->defaultGroup('status') //->groupingSettingsHidden()
            ->emptyStateDescription('Once students starts announcing internships, it will appear here.')
            ->columns(
                $livewire->isGridLayout()
                    ? static::getGridTableColumns()
                    : static::getTableColumns(),
            )
            ->contentGrid(
                fn () => $livewire->isGridLayout()
                    ? [
                        'md' => 2,
                        'lg' => 3,
                        'xl' => 4,
                    ] : null
            )
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                SelectFilter::make('status')
                    ->multiple()
                    ->options([
                        'Draft' => __('Draft'),
                        'Announced' => __('Announced'),
                        'Validated' => __('Validated'),
                        'Approved' => __('Approved'),
                        'Signed' => __('Signed'),
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    \App\Filament\Actions\SignAction::make()
                    ->disabled(fn ($record): bool => $record['signed_at'] !== null),
                    \App\Filament\Actions\ReceiveAction::make()
                    ->disabled(fn ($record): bool => $record['received_at'] !== null),
                    ActionGroup::make([
                        \App\Filament\Actions\ValidateAction::make()
                        ->disabled(fn ($record): bool => $record['validated_at'] !== null),
                        \App\Filament\Actions\AssignDepartmentAction::make()
                        ->disabled(fn ($record): bool => $record['assigned_department'] !== null),
                        ])->dropdown(false)
                    ])
                    ->label(__('DASRE'))
                    ->icon('')
                    // ->size(ActionSize::ExtraSmall)
                    ->color('warning')
                    ->outlined()
                    ->button(),
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    // ->disabled(! auth()->user()->can('delete', $this->post)),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                    ])
                    ->label(__('Manage'))
                    ->icon('')
                    // ->size(ActionSize::ExtraSmall)
                    ->outlined()
                    ->color('warning')
                    ->button(),
                // ActionGroup::make([
                //     \App\Filament\Actions\ValidateAction::make(),
                //     \App\Filament\Actions\AssignDepartmentAction::make(),
                //     ])
                //     ->label(__())
                //     ->icon('heroicon-m-ellipsis-vertical')
                //     ->size(ActionSize::Medium)
                //     ->color('danger')
                    // ->button()
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                ExportBulkAction::make(),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageInternships::route('/'),
            // 'card-view' => Pages\ManageInternships::route('/card-view'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('Internship');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Internships');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGridTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('student.full_name')
                ->weight(FontWeight::Bold)
                ->searchable(),
            Tables\Columns\TextColumn::make('title')
                ->weight(FontWeight::Bold)
                ->searchable(),
            // Split::make([
            Stack::make([
                // Tables\Columns\TextColumn::make('student_id')
                //     ->numeric()
                //     ->sortable(),
                // add validated_at as date column with d/m/y format
                Tables\Columns\TextColumn::make('validated_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Validated at'),
                Tables\Columns\TextColumn::make('assigned_department')
                    ->dateTime()
                    ->sortable()
                    ->label('Assigned department'),
                Tables\Columns\TextColumn::make('announced_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('approved_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('signed_at')
                    ->dateTime()
                    ->sortable(),
            ])
                ->alignment(Alignment::Start),

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

    public static function getTableColumns(): array
    {
        return [

            Tables\Columns\TextColumn::make('student_id')
                ->numeric()
                ->sortable(),
                Tables\Columns\TextColumn::make('id_pfe')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('student.full_name')
                ->label(__('Full Name'))
                ->weight(FontWeight::Bold)
                ->searchable(),
            Tables\Columns\TextColumn::make('student.program')
                ->label(__('Program'))
                ->weight(FontWeight::Bold)
                ->searchable(),
            Tables\Columns\TextColumn::make('organization_name')
                ->searchable(),
            Tables\Columns\TextColumn::make('adresse')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('city')
                ->searchable(),
            Tables\Columns\TextColumn::make('country')
                ->searchable(),
            Tables\Columns\TextColumn::make('office_location')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('parrain_titre')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('parrain_nom')
                ->searchable(),
            Tables\Columns\TextColumn::make('parrain_prenom')
                ->searchable(),
            Tables\Columns\TextColumn::make('parrain_fonction')
                ->searchable(),
            Tables\Columns\TextColumn::make('parrain_tel')
                ->searchable(),
            Tables\Columns\TextColumn::make('parrain_mail')
                ->searchable(),
            Tables\Columns\TextColumn::make('encadrant_ext_titre')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('encadrant_ext_nom')
                ->searchable(),
            Tables\Columns\TextColumn::make('encadrant_ext_prenom')
                ->searchable(),
            Tables\Columns\TextColumn::make('encadrant_ext_fonction')
                ->searchable(),
            Tables\Columns\TextColumn::make('encadrant_ext_tel')
                ->searchable(),
            Tables\Columns\TextColumn::make('encadrant_ext_mail')
                ->searchable(),
            Tables\Columns\TextColumn::make('starting_at')
                ->date()
                ->sortable(),
            Tables\Columns\TextColumn::make('ending_at')
                ->date()
                ->sortable(),
            Tables\Columns\TextColumn::make('remuneration')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('currency')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('load')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('int_adviser_name')
                ->searchable(),
            Tables\Columns\TextColumn::make('year_id')
                ->toggleable(isToggledHiddenByDefault: true)
                ->numeric()
                ->sortable(),
            Tables\Columns\IconColumn::make('is_valid')
                ->toggleable(isToggledHiddenByDefault: true)
                ->boolean()
                ->label(__('Validated by student')),
                Tables\Columns\TextColumn::make('status')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('announced_at')
                ->dateTime()
                ->sortable()
                ->label(__('Announced at'))->since()
                ->description(__('Announced'), 'above')
                ->placeholder('Not Announced yet')
                ->badge(function (Internship $internship) {
                    return $internship->announced_at ? __('Announced') : __('Not announced yet');
                }),
            Tables\Columns\TextColumn::make('validated_at')
                ->dateTime()
                ->sortable(),
                Tables\Columns\TextColumn::make('assigned_department')
                ->sortable()
                ->label(__('Assigned department')),
            Tables\Columns\TextColumn::make('approved_at')
                ->dateTime()
                ->sortable(),
            Tables\Columns\TextColumn::make('signed_at')
                ->dateTime()
                ->sortable(),
            Tables\Columns\TextColumn::make('project_id')
                ->toggleable(isToggledHiddenByDefault: true)
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('binome_user_id')
                ->toggleable(isToggledHiddenByDefault: true)
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('partner_internship_id')
                ->toggleable(isToggledHiddenByDefault: true)
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('partnership_status')
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('deleted_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }
}
