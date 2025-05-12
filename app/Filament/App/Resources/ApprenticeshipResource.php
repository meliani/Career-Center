<?php

namespace App\Filament\App\Resources;

use App\Enums;
use App\Filament\Actions\Action\AddApprenticeshipAmendmentAction;
use App\Filament\Actions\Action\ApplyForCancelInternshipAction;
use App\Filament\Actions\Action\Processing\GenerateApprenticeshipAgreementAction;
use App\Filament\App\Resources\ApprenticeshipResource\Pages;
use App\Filament\Core\StudentBaseResource;
use App\Models\Apprenticeship;
use App\Models\Student;
use App\Services\Filament\Forms\ApprenticeshipAgreementForm;
use Filament;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class ApprenticeshipResource extends StudentBaseResource
{
    protected static ?string $model = Apprenticeship::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static bool $softDelete = true;

    protected static ?string $modelLabel = 'Apprenticeship agreement';

    protected static ?string $pluralModelLabel = 'Apprenticeship agreements';

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __(static::$modelLabel);
    }

    public static function getPluralModelLabel(): string
    {
        return __(static::$pluralModelLabel);
    }

    public static function canAccess(): bool
    {
        // check if auth is student and check level if its a first or secondYear
        if (Auth::user() instanceof Student) {
            if (Auth::user()->level->value == 'FirstYear' || Auth::user()->level->value == 'SecondYear') {
                return true;
            }
        }

        return false;
    }

    public static function form(Form $form): Form
    {
        // This form definition will be used for editing. 
        // The creation form is handled by the wizard in CreateApprenticeship page
        return $form->schema((new ApprenticeshipAgreementForm)->getSchema());
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('student_id', Auth::id());
    }
            
    public static function table(Table $table): Table
    {
        return $table
            ->heading(__('My Apprenticeship Agreements'))
            ->recordTitleAttribute('title')
            ->paginated(false)
            ->searchable(false)
            ->emptyStateHeading('No Apprenticeship Agreement')
            ->emptyStateDescription('You have not created an apprenticeship agreement yet.')
            ->emptyStateIcon('heroicon-o-document-text')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Create your apprenticeship agreement')
                    ->url(route('filament.app.resources.apprenticeships.create'))
                    ->icon('heroicon-o-plus-circle')
                    ->visible(function () {
                        $currentYearId = \App\Models\Year::current()->id;
                        $existingAgreement = Apprenticeship::where('student_id', Auth::id())
                            ->where('year_id', $currentYearId)
                            ->exists();

                        return ! $existingAgreement;
                    })
                    ->button(),
            ])
            ->contentGrid([
                'md' => 1,
            ])
            ->columns([
                Tables\Columns\Layout\Panel::make([
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('organization.name')
                            ->weight(FontWeight::Bold)
                            ->size(Tables\Columns\TextColumn\TextColumnSize::Large)
                            ->label('Organization')
                            ->tooltip('The organization where you will do your apprenticeship')
                            ->sortable(false)
                            ->toggleable(false)
                            ->extraAttributes([
                                'class' => 'pb-2',
                            ]),

                        Tables\Columns\TextColumn::make('title')
                            ->weight(FontWeight::Medium)
                            ->label('Subject')
                            ->tooltip('The subject or topic of your apprenticeship')
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
                                ->tooltip('Current status of your apprenticeship agreement')
                                ->sortable(false)
                                ->toggleable(false)
                                ->grow(false),

                            Tables\Columns\TextColumn::make('internship_level')
                                ->badge()
                                ->label('Type')
                                ->tooltip('The type of apprenticeship')
                                ->alignment(\Filament\Support\Enums\Alignment::Center)
                                ->sortable(false)
                                ->toggleable(false),
                                
                            Tables\Columns\TextColumn::make('internship_type')
                                ->badge()
                                ->label('Modality')
                                ->tooltip('How you will work during the apprenticeship')
                                ->alignment(\Filament\Support\Enums\Alignment::Center)
                                ->sortable(false)
                                ->toggleable(false),

                            Tables\Columns\Layout\Stack::make([
                                Tables\Columns\TextColumn::make('starting_at')
                                    ->date()
                                    ->label('Duration')
                                    ->tooltip('Apprenticeship period')
                                    ->formatStateUsing(fn ($state) => $state->format('M d, Y')),
                                    
                                Tables\Columns\TextColumn::make('ending_at')
                                    ->date()
                                    ->formatStateUsing(fn ($state) => "to " . $state->format('M d, Y')),
                            ])
                                ->space(1)
                                ->alignment(\Filament\Support\Enums\Alignment::Center),
                        ]),

                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('agreement_pdf_url')
                            ->label('Agreement PDF')
                            ->tooltip('Download or generate your apprenticeship agreement document')
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
            ->filters([
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
                    Action::make('request_amendment')
                        ->label(__('Request Amendment'))
                        ->icon('heroicon-o-pencil-square')
                        ->color('warning')
                        ->action(function (Apprenticeship $record) {
                            return redirect()->to(static::getUrl('view', ['record' => $record]));
                        })
                        ->visible(fn (Apprenticeship $record) => 
                            $record->status !== Enums\Status::Draft && 
                            $record->status !== Enums\Status::PendingCancellation && 
                            !$record->hasPendingAmendmentRequests()),
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
                
                GenerateApprenticeshipAgreementAction::make('Generate Agreement PDF')
                    ->label(__('Generate Agreement PDF'))
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->button()
                    ->visible(fn ($record) => $record->status == Enums\Status::Announced || $record->status == Enums\Status::Validated),
                GenerateApprenticeshipAgreementAction::make('Generate Draft Agreement PDF')
                    ->label(__('Generate Draft Agreement PDF'))
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->button()
                    ->visible(fn ($record) => $record->status == Enums\Status::Draft),
            ], position: Tables\Enums\ActionsPosition::AfterColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                    // Tables\Actions\ForceDeleteBulkAction::make(),
                    // Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);

    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\App\Resources\ApprenticeshipResource\RelationManagers\AmendmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApprenticeships::route('/'),
            'create' => Pages\CreateApprenticeship::route('/create'),
            'view' => Pages\ViewApprenticeship::route('/{record}'),
            'edit' => Pages\EditApprenticeship::route('/{record}/edit'),
        ];
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()
    //         ->withoutGlobalScopes([
    //             SoftDeletingScope::class,
    //         ]);
    // }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('Apprenticeship agreement and validation process'))
                    ->schema([
                        Infolists\Components\Fieldset::make('Apprenticeship agreement')
                            ->schema([
                                Infolists\Components\TextEntry::make('student.long_full_name')
                                    ->label('Student'),
                                Infolists\Components\TextEntry::make('title')
                                    ->label('Title'),
                                Infolists\Components\TextEntry::make('description')
                                    ->columnSpanFull()
                                    ->html(),
                                Infolists\Components\TextEntry::make('organization.name')
                                    ->label('Organization'),
                                Infolists\Components\TextEntry::make('internship_level')
                                    ->label('Apprenticeship Type')
                                    ->badge(),
                                Infolists\Components\TextEntry::make('internship_type')
                                    ->label('Work Modality')
                                    ->badge(),
                                Infolists\Components\TextEntry::make('status')
                                    ->label('Status')
                                    ->badge(),
                                    
                                Infolists\Components\TextEntry::make('amendment_status')
                                    ->label('Amendment Status')
                                    ->state(fn ($record) => $record->hasPendingAmendmentRequests() ? __('Amendment Pending') : null)
                                    ->badge()
                                    ->color('warning')
                                    ->visible(fn ($record) => $record->hasPendingAmendmentRequests()),
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->date(),
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
                Infolists\Components\Section::make(__('Apprenticeship agreement paperwork'))
                    ->schema([
                        Infolists\Components\TextEntry::make('agreement_pdf_url')
                            ->label('Agreement PDF')
                            ->placeholder(__('No PDF generated yet'))
                            ->formatStateUsing(fn ($record) => $record->agreement_pdf_url ? __('Download agreement PDF') : __('No PDF generated yet'))
                            ->columnSpan(3)
                            ->badge()
                            ->color(fn ($record) => $record->agreement_pdf_url ? 'success' : 'gray')
                            ->url(fn ($record) => $record->agreement_pdf_url, shouldOpenInNewTab: true),
                    ]),
            ]);
    }
}
