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
                    ->url(route('filament.research.resources.master-research-internship-agreements.create'))
                    ->icon('heroicon-o-plus-circle')
                    ->button(),
            ])
            ->columns([
                Tables\Columns\Layout\Panel::make([
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('organization.name')
                            ->weight(FontWeight::Bold)
                            ->size(Tables\Columns\TextColumn\TextColumnSize::Large)
                            ->label('Organization')
                            ->tooltip('The organization where you will do your internship')
                            ->sortable(false)
                            ->toggleable(false)
                            ->extraAttributes([
                                'class' => 'pb-2',
                            ]),

                        Tables\Columns\TextColumn::make('title')
                            ->weight(FontWeight::Medium)
                            ->label('Subject')
                            ->tooltip('The subject or topic of your internship')
                            ->sortable(false)
                            ->toggleable(false)
                            ->extraAttributes([
                                'class' => 'pb-4',
                            ]),
                    ])
                        ->space(3),

                    Tables\Columns\Layout\Grid::make([
                        'md' => 3,
                        'lg' => 3,
                    ])
                        ->extraAttributes([
                            'class' => 'border rounded-xl p-4 bg-gray-50',
                        ])
                        ->schema([
                            Tables\Columns\TextColumn::make('status')
                                ->badge()
                                ->alignment(\Filament\Support\Enums\Alignment::Center)
                                ->tooltip('Current status of your internship agreement')
                                ->sortable(false)
                                ->toggleable(false)
                                ->grow(false),

                            Tables\Columns\TextColumn::make('starting_at')
                                ->date()
                                ->label('Starting')
                                ->tooltip('When your internship begins')
                                ->alignment(\Filament\Support\Enums\Alignment::Center)
                                ->sortable(false)
                                ->toggleable(false)
                                ->formatStateUsing(fn ($state) => $state->format('M d, Y')),

                            Tables\Columns\TextColumn::make('ending_at')
                                ->date()
                                ->label('Ending')
                                ->tooltip('When your internship ends')
                                ->alignment(\Filament\Support\Enums\Alignment::Center)
                                ->sortable(false)
                                ->toggleable(false)
                                ->formatStateUsing(fn ($state) => $state->format('M d, Y')),
                        ]),

                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('agreement_pdf_url')
                            ->label('Agreement PDF')
                            ->tooltip('Download or generate your internship agreement document')
                            ->placeholder(__('No PDF generated yet'))
                            ->formatStateUsing(fn ($record) => $record->agreement_pdf_url ? __('Download agreement PDF') : __('No PDF generated yet'))
                            ->url(fn ($record) => $record->agreement_pdf_url, shouldOpenInNewTab: true)
                            ->badge()
                            ->sortable(false)
                            ->toggleable(false)
                            ->color(fn ($record) => $record->agreement_pdf_url ? 'success' : 'gray')
                            ->extraAttributes([
                                'class' => 'mt-4',
                            ]),
                    ]),
                ])
                    ->collapsible(false),
            ])
            ->contentGrid([
                'md' => 1,
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('success')
                        ->label('View details'),
                    Tables\Actions\EditAction::make()
                        ->color('warning')
                        ->label('Edit details')
                        ->disabled(fn ($record) => $record->status !== Enums\Status::Draft),
                    ApplyForCancelInternshipAction::make('Apply for internship cancellation')
                        ->color('danger')
                        ->icon('heroicon-o-bolt-slash')
                        ->disabled(fn ($record) => $record->status === Enums\Status::Draft)
                        ->hidden(fn ($record) => $record->status === Enums\Status::PendingCancellation),
                ])
                    ->label(__('Manage'))
                    ->icon('heroicon-m-cog-6-tooth')
                    ->color('gray')
                    ->button(),

                GenerateInternshipAgreementAction::make('Generate Agreement PDF')
                    ->label(__('Generate Agreement PDF'))
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->button()
                    ->visible(fn ($record) => $record->status == Enums\Status::Announced),
                GenerateInternshipAgreementAction::make('Generate Draft Agreement PDF')
                    ->label(__('Generate Draft Agreement PDF'))
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->button()
                    ->visible(fn ($record) => $record->status == Enums\Status::Draft),
            ], position: Tables\Enums\ActionsPosition::AfterColumns);
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
            'edit' => Pages\EditMasterResearchInternshipAgreement::route('/{record}/edit'),
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
