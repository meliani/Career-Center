<?php

namespace App\Filament\Research\Resources;

use App\Enums;
use App\Filament\Actions\Action\ApplyForCancelInternshipAction;
use App\Filament\Actions\Action\Processing\GenerateInternshipAgreementAction;
use App\Filament\Core\StudentBaseResource;
use App\Filament\Research\Resources\MasterResearchInternshipAgreementResource\Pages;
use App\Models\MasterResearchInternshipAgreement;
use App\Models\Student;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class MasterResearchInternshipAgreementResource extends StudentBaseResource
{
    protected static ?string $model = MasterResearchInternshipAgreement::class;

    protected static ?string $modelLabel = 'Research Internship Agreement';

    protected static ?string $pluralModelLabel = 'Research Internship Agreements';

    protected static ?string $navigationGroup = null;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function canAccess(): bool
    {
        if (Auth::user() instanceof Student) {
            if (Auth::user()->level === Enums\StudentLevel::MasterIoTBigData) {
                return true;
            }
        }

        return false;
    }

    public static function canViewAny(): bool
    {
        return false;
    }

    // eloquent query

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->where('student_id', Auth::id());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading(__(''))
            ->recordTitleAttribute('title')
            ->paginated(false)
            ->searchable(false)
            ->emptyStateHeading('')
            ->emptyStateDescription('')
            ->emptyStateIcon('heroicon-o-document-text')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('Create your first internship agreement')
                    ->url(route('filament.app.resources.final-year-internship-agreements.create'))
                    ->icon('heroicon-o-plus-circle')
                    ->button(),
            ])
            ->contentGrid(
                [
                    'md' => 1,
                    'lg' => 1,
                    'xl' => 1,
                    '2xl' => 1,
                ]
            )
            ->columns([
                Tables\Columns\Layout\Split::make([

                    Tables\Columns\Layout\Split::make([
                        Tables\Columns\TextColumn::make('organization.name')
                            ->weight(FontWeight::Bold)
                            ->description(__('Organization'), position: 'above')
                            ->toggleable(false)
                            ->label('Organization')
                            ->numeric()
                            ->sortable(false),
                        Tables\Columns\TextColumn::make('title')
                            ->toggleable(false)
                            ->sortable(false)
                            ->description(__('Subject'), position: 'above')
                            ->weight(FontWeight::Bold),
                    ]),

                    Tables\Columns\Layout\Split::make([
                        Tables\Columns\TextColumn::make('status')
                            // ->description(__('Status'), position: 'above')
                            ->toggleable(false)
                            ->sortable(false)
                            ->grow(true)
                            ->badge()
                            ->columnSpan(1),
                        Tables\Columns\Layout\Stack::make([
                            Tables\Columns\TextColumn::make('starting_at')
                                ->toggleable(false)
                                ->description(__('Starting from'), position: 'above')
                                ->date()
                                ->sortable(false),
                            Tables\Columns\TextColumn::make('ending_at')
                                ->toggleable(false)
                                ->description(__('to'), position: 'above')
                                ->date()
                                ->sortable(false),
                        ]),
                    ]),

                ]),
                Tables\Columns\Layout\Panel::make([

                    Tables\Columns\TextColumn::make('agreement_pdf_url')
                        ->toggleable(false)
                        // ->description(__('Agreement PDF'), position: 'before')
                        ->label('Agreement PDF')
                        ->placeholder(__('No PDF generated yet'))
                        // ->limit(20)
                        ->formatStateUsing(fn ($record) => $record->agreement_pdf_url ? _('Download agreement PDF') : _('No PDF generated yet'))
                        ->columnSpan(3)
                        ->sortable(false)
                        ->url(fn ($record) => $record->agreement_pdf_url, shouldOpenInNewTab: true),
                ]),
            ])
            ->filters([
                //
            ])
            ->actions([

                Tables\Actions\ActionGroup::make([

                    ApplyForCancelInternshipAction::make('Apply for internship cancellation')
                        ->color('danger')
                        ->icon('heroicon-o-bolt-slash'),
                    Tables\Actions\ViewAction::make()
                        ->color('success')
                        ->label('View details'),
                    Tables\Actions\EditAction::make()
                        ->color('warning')
                        ->label('Edit possible fields'),
                ])
                    ->dropdownPlacement('top-start')
                    ->dropdownWidth(\Filament\Support\Enums\MaxWidth::Small)
                    // ->outlined()
                    ->label(__('Manage my internship agreement'))
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->color('primary'),
                GenerateInternshipAgreementAction::make('Generate Agreement PDF')
                    ->label(__('Generate Agreement PDF'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->outlined()
                    ->size(\Filament\Support\Enums\ActionSize::ExtraSmall)
                    ->button(),

            ], position: Tables\Enums\ActionsPosition::AfterColumns)
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
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
            'index' => Pages\ListMasterResearchInternshipAgreements::route('/'),
            'create' => Pages\CreateMasterResearchInternshipAgreement::route('/create'),
            'view' => Pages\ViewMasterResearchInternshipAgreement::route('/{record}'),
            // 'edit' => Pages\EditMasterResearchInternshipAgreement::route('/{record}/edit'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('Internship agreement and validation process'))
                    ->schema([

                        Infolists\Components\Fieldset::make('Internship agreement')
                            ->schema([
                                Infolists\Components\TextEntry::make('student.long_full_name')
                                    ->label('Student'),
                                Infolists\Components\TextEntry::make('title')
                                    ->label('Title'),
                                Infolists\Components\TextEntry::make('organization.name')

                                    ->label('Organization'),
                                Infolists\Components\TextEntry::make('internship_period')
                                    ->label('Internship Period'),
                                Infolists\Components\TextEntry::make('status')
                                    ->label('Status'),
                                Infolists\Components\TextEntry::make('created_at')

                                    ->label('Created At'),
                            ]),
                        Infolists\Components\Fieldset::make('Validation process')
                            ->schema([
                                Infolists\Components\TextEntry::make('announced_at')
                                    ->date()
                                    ->label('Announced at'),
                                Infolists\Components\TextEntry::make('validated_at')
                                    ->date()
                                    ->label('Validated at'),
                                Infolists\Components\TextEntry::make('received_at')
                                    ->date()
                                    ->label('Received at'),
                                Infolists\Components\TextEntry::make('signed_at')
                                    ->date()
                                    ->label('Signed at'),
                            ])
                            ->columns(4),
                    ]),
                Infolists\Components\Section::make(__('Internship agreement paperwork'))
                    ->schema([
                        Infolists\Components\TextEntry::make('agreement_pdf_url')
                            ->label('Agreement PDF')
                            ->placeholder(__('No PDF generated yet'))
                            ->formatStateUsing(fn ($record) => $record->agreement_pdf_url ? _('Download agreement PDF') : _('No PDF generated yet'))
                            ->columnSpan(3)
                            ->badge()
                            ->url(fn ($record) => $record->agreement_pdf_url, shouldOpenInNewTab: true)
                            ->hintActions(
                                [Infolists\Components\Actions\Action::make('generate')
                                    ->label('Generate PDF')
                                    ->icon('heroicon-o-document-plus')
                                    ->button(),

                                ]
                            ),
                        // ->suffixAction(
                        //     Infolists\Components\Actions\Action::make('delete')
                        //         ->label('Delete PDF')
                        //         ->icon('heroicon-o-trash'),
                        // ),

                    ]),
            ]);

    }
}
