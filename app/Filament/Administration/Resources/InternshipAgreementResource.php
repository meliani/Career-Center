<?php

namespace App\Filament\Administration\Resources;

use App\Enums;
use App\Filament\Administration\Resources\InternshipAgreementResource\Pages;
use App\Filament\Core\BaseResource;
use App\Filament\Imports\InternshipAgreementImporter;
use App\Mail\GenericEmail;
use App\Models\InternshipAgreement;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Support\Enums\ActionSize;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class InternshipAgreementResource extends BaseResource
{
    protected static ?string $model = InternshipAgreement::class;

    protected static ?string $recordTitleAttribute = 'organization_name';

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?int $sort = 1;

    protected static ?string $navigationBadgeTooltip = 'Announced internships';

    public static function getnavigationGroup(): string
    {
        return __('Students and projects');
    }

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
        return ['title', 'organization_name', 'student.full_name', 'id_pfe'];
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
            );
    }

    public static function table(Table $table): Table
    {
        $livewire = $table->getLivewire();

        return $table
            ->defaultSort('announced_at', 'asc')
            ->groups([
                Tables\Grouping\Group::make('assigned_department')
                    ->label(__('Assigned department'))
                    ->collapsible(),
                Tables\Grouping\Group::make('status')
                    ->label(__('Status'))
                    ->collapsible()
                    ->titlePrefixedWithLabel(false),
                Tables\Grouping\Group::make('student.program')
                    ->label(__('Program'))
                    ->collapsible(),
            ])
            ->emptyStateDescription(__('Once students starts announcing internships, it will appear here.'))
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
                    ->hidden(fn () => (auth()->user()->isAdministrator() || auth()->user()->isDepartmentHead() || auth()->user()->isProgramCoordinator()) === false)
                    ->outlined(),
                \App\Filament\Actions\Action\AssignInternshipsToProjects::make('Assign Internships To Projects')
                    ->label(__('Assign Internships To Projects'))
                    ->hidden(fn () => auth()->user()->isAdministrator() === false)
                    ->outlined(),

                Tables\Actions\ActionGroup::make([
                    ImportAction::make()
                        ->importer(InternshipAgreementImporter::class)
                        ->hidden(fn () => auth()->user()->isAdministrator() === false)
                        ->hidden(fn () => app()->environment('production')),
                ]),
            ])

            ->columns(
                $livewire->isGridLayout()
                    ? \App\Services\Filament\InternshipAgreementGrid::get()
                    : \App\Services\Filament\InternshipAgreementTable::get(),
            )
            ->contentGrid(
                fn () => $livewire->isGridLayout()
                    ? [
                        'md' => 2,
                        'lg' => 2,
                        'xl' => 2,
                    ] : null
            )
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('status')
                    ->multiple()
                    ->options(Enums\Status::class),
                Tables\Filters\SelectFilter::make('assigned_department')
                    ->multiple()
                    ->options(Enums\Department::class),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    \App\Filament\Actions\Action\SignAction::make()
                        ->disabled(fn ($record): bool => $record['signed_at'] !== null),
                    \App\Filament\Actions\Action\ReceiveAction::make()
                        ->disabled(fn ($record): bool => $record['received_at'] !== null),
                    Tables\Actions\ActionGroup::make([
                        \App\Filament\Actions\Action\ValidateAction::make()
                            ->disabled(fn ($record): bool => $record['validated_at'] !== null),
                        \App\Filament\Actions\Action\AssignDepartmentAction::make()
                            ->disabled(fn ($record): bool => $record['assigned_department'] !== null),
                    ])->dropdown(false),
                ])
                    ->label('')
                    ->icon('heroicon-o-arrow-up-on-square')
                    ->size(ActionSize::ExtraLarge)
                    ->tooltip(__('Validate, sign, or assign department'))
                    ->color('warning')
                    ->hidden(fn () => (auth()->user()->isAdministrator() || auth()->user()->isPowerProfessor()) === false),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    // ->disabled(! auth()->user()->can('delete', $this->post)),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ])
                    ->hidden((auth()->user()->isSuperAdministrator() || auth()->user()->isPowerProfessor()) === false)
                    ->label('')
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->size(ActionSize::ExtraLarge)
                    ->tooltip(__('View, edit, or delete this internship agreement')),
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
                    )
                    ->label('')
                    ->icon('heroicon-o-envelope')
                    ->size(ActionSize::ExtraLarge)
                    ->tooltip(__('Send an email to the student')),
            ], position: Tables\Enums\ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInternshipAgreements::route('/'),
            // 'create' => Pages\CreateInternshipAgreement::route('/create'),
            'edit' => Pages\EditInternshipAgreement::route('/{record}/edit'),
        ];
    }
}
