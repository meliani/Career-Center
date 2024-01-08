<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InternshipResource\Pages;
use App\Filament\Resources\InternshipResource\RelationManagers;
use App\Models\Internship;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\Layout\Panel;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Testing\Fakes\MailFake;
use App\Mail\GenericContactEmail;
use App\Mail\DefenseReadyEmail;
use Filament\Support\Enums\FontWeight;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class InternshipTableResource extends Resource
{
    protected static ?string $model = Internship::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('student_id')
                //     ->numeric(),
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
                // Forms\Components\TextInput::make('parrain_titre')
                //     ->required()
                //     ->maxLength(191),
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
                // Forms\Components\TextInput::make('encadrant_ext_titre')
                //     ->required()
                //     ->maxLength(191),
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
                // Forms\Components\Toggle::make('abroad'),
                Forms\Components\TextInput::make('remuneration')
                    ->maxLength(191),
                Forms\Components\TextInput::make('currency')
                    ->maxLength(10),
                Forms\Components\TextInput::make('load')
                    ->maxLength(191),
                // Forms\Components\TextInput::make('abroad_school')
                // ->maxLength(191),
                // Forms\Components\TextInput::make('int_adviser_id')
                // ->numeric(),
                Forms\Components\TextInput::make('int_adviser_name')
                    ->maxLength(191),
                // Forms\Components\Toggle::make('is_signed'),
                // Forms\Components\TextInput::make('year_id')
                //     ->numeric(),
                // Forms\Components\TextInput::make('binome_user_id')
                //     ->numeric(),
                // Forms\Components\Toggle::make('is_valid'),
                Forms\Components\TextInput::make('status')
                    ->maxLength(255),
                // Forms\Components\DateTimePicker::make('anounced_at'),
                // Forms\Components\DateTimePicker::make('reviewed_at'),
                // Forms\Components\DateTimePicker::make('approved_at'),
                // Forms\Components\DateTimePicker::make('signed_at'),
                // Forms\Components\TextInput::make('partner_internship_id')
                //     ->numeric(),
                // Forms\Components\TextInput::make('partnership_status')
                //     ->maxLength(50),
            ]);
    }

    public static function table(Table $table): Table
    {
        $livewire = $table->getLivewire();
        return $table
            // ->modifyQueryUsing(fn (Builder $query) =>
            // $query->whereHas(
            //     'student',
            //     fn (Builder $query) => $query->where('filiere_text', Auth::user()->program_coordinator)
            // ))
            ->groups([
                Group::make('status')
                    ->collapsible(),
                Group::make('student.filiere_text')
                    ->label('Program')
                // ->titlePrefixedWithLabel(false)
                // ->getTitleFromRecordUsing(fn (Internship $record): string => ucfirst($record->filiere_text)),
            ])
            ->defaultGroup('status') //->groupingSettingsHidden()
            ->emptyStateDescription('Once students starts announcing internships, it will appear here.')
            ->columns([
                Tables\Columns\TextColumn::make('student.full_name')
                    ->weight(FontWeight::Bold)
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->weight(FontWeight::Bold)
                    ->searchable(),
                Tables\Columns\TextColumn::make('keywords')
                    ->searchable()
                    ->badge()
                    ->separator(','),
                // Split::make([
                Stack::make([
                    // Tables\Columns\TextColumn::make('student_id')
                    //     ->numeric()
                    //     ->sortable(),
                    // add reviewed_at as date column with d/m/y format
                    Tables\Columns\TextColumn::make('reviewed_at')
                        ->dateTime()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('anounced_at')
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
                // Tables\Columns\IconColumn::make('is_valid')
                //     ->boolean(),
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
            ])
            // ->contentGrid([
            //     'md' => 2,
            //     'xl' => 3,
            // ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\Action::make('review')->action(fn (Internship $internship) => $internship->review())
                    ->requiresConfirmation(fn (Internship $internship) => "Are you sure you want to mark this internship as reviewed?"),
                // Tables\Actions\Action::make('sendEmail')
                // ->form([
                //     TextInput::make('subject')->required(),
                //     RichEditor::make('body')->required(),
                // ])
                // ->action(fn (array $data, Internship $internship) => Mail::to($internship->student->email_perso)
                //     ->send(new DefenseReadyEmail(
                //         $data['subject'],
                //         $data['body'],
                //     ))
                // )
                // \App\Filament\Resources\InternshipResource\Actions\ReviewAction::make()->action(fn (Internship $internship) => $internship->review()),
            ], position: ActionsPosition::BeforeCells)
            ->bulkActions([
                ExportBulkAction::make()
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                //     Tables\Actions\ForceDeleteBulkAction::make(),
                //     Tables\Actions\RestoreBulkAction::make(),
                // ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageInternships::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
