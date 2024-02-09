<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Imports\InternshipAgreementImporter;
use App\Models\InternshipAgreement;
use Filament\Forms\Form;
use App\Filament\Core\BaseResource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

use App\Enums;
use App\Filament\Administration\Resources\InternshipAgreementResource\Pages;
use App\Mail\GenericEmail;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\ActionsPosition;
use Illuminate\Support\Facades\Mail;
use App\Filament\Administration\Resources\InternshipAgreementResource\RelationManagers;

class InternshipAgreementResource extends BaseResource
{
    protected static ?string $modelLabel = 'internship agreement';

    // protected static ?string $pluralModelLabel = 'internship agreements';
    // protected static ?string $navigationParentItem = '';

    protected static ?string $navigationGroup = 'Internships';

    protected static ?string $model = InternshipAgreement::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationBadgeTooltip = 'Announced internships';

    public static function getModelLabel(): string
    {
        return __('Internship Agreement');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Internship Agreements');
    }
    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'organization_name', 'student.full_name'];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count('id');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema(
                \App\Services\Filament\InternshipAgreementForm::get(),
                \App\Filament\Actions\CreateProjectFromInternshipAgreement::make('Create Project From Internship Agreement'),
            );
    
    }

    public static function table(Table $table): Table
    {
        $livewire = $table->getLivewire();

        return $table
            ->headerActions([
                ImportAction::make()
                    ->importer(InternshipAgreementImporter::class),
            ])
            ->defaultSort('announced_at', 'asc')
            ->groups([
                Group::make(__('status'))
                    ->collapsible()
                    ->titlePrefixedWithLabel(false),
                Group::make('student.program')
                    ->label(__('Program'))
                    ->collapsible(),
            ])
            ->emptyStateDescription('Once students starts announcing internships, it will appear here.')
            ->columns(
                $livewire->isGridLayout()
                    ? \App\Services\Filament\InternshipAgreementGrid::get()
                    : \App\Services\Filament\InternshipAgreementTable::get(),
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
                    ->options(Enums\Status::class),
            ])
            ->filters([
                //
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
                    ])->dropdown(false),
                ])
                    ->label(__('Validation'))
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
                Tables\Actions\Action::make('sendEmail')
                    ->form([
                        TextInput::make('subject')->required(),
                        RichEditor::make('body')->required(),
                    ])
                    ->action(
                        fn (array $data, InternshipAgreement $internship) => Mail::to($internship->student->email_perso)
                            ->send(new GenericEmail(
                                $internship->student,
                                $data['subject'],
                                $data['body'],
                            ))
                    )->label(__('Send email')),
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make(),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\ProjectRelationManager::class,
            // RelationManagers\StudentRelationManager::class,
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
