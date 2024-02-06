<?php

namespace App\Filament\Administration\Resources;

use App\Enums;
use App\Enums\Status;
use App\Filament\Administration\Resources\InternshipAgreementResource\Pages;
use App\Models\Internship;
use App\Models\InternshipAgreement;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class InternshipAgreementResource extends Resource
{
    protected static ?string $model = InternshipAgreement::class;

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
                Forms\Components\Toggle::make('is_valid'),
                Forms\Components\ToggleButtons::make('status')
                    ->label(__('Status'))
                    ->options(Enums\Status::class)
                    ->inline()
                    ->required(),

                Forms\Components\DateTimePicker::make('announced_at'),
                Forms\Components\DateTimePicker::make('validated_at'),
                Forms\Components\Select::make('assigned_department')
                    ->options(Enums\Department::class),
                Forms\Components\DateTimePicker::make('received_at'),
                Forms\Components\DateTimePicker::make('signed_at'),
                Forms\Components\TextInput::make('project_id')
                    ->numeric(),
                Forms\Components\Select::make('binome_user_id')
                    ->options(function () {
                        return Student::all()->pluck('full_name', 'id');
                    }),
                Forms\Components\TextInput::make('partner_internship_id')
                    ->numeric(),
                Forms\Components\TextInput::make('partnership_status')
                    ->maxLength(50),
                Forms\Components\Textarea::make('observations')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $livewire = $table->getLivewire();

        return $table
            ->defaultSort('announced_at', 'asc')
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
        //->groupingSettingsHidden()
        // ->groupsOnly()
            ->emptyStateDescription('Once students starts announcing internships, it will appear here.')
            ->columns(
                $livewire->isGridLayout()
                ? static::getGridTableColumns()
                : static::getTableColumns(),
                // TextColumn::make('is_valid')
                //     ->summarize(Sum::make()),
            )
        // ->defaultGroup('status')
        // ->groupsOnly()
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
                    ->options(Status::class),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getGridTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('id')
                ->label(__('ID'))
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('student_id')
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

    public static function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('id')
                ->label(__('ID'))
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('student_id')
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInternshipAgreements::route('/'),
            'create' => Pages\CreateInternshipAgreement::route('/create'),
            'edit' => Pages\EditInternshipAgreement::route('/{record}/edit'),
        ];
    }
}
