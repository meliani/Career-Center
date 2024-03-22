<?php

namespace App\Filament\Administration\Resources;

use App\Enums;
use App\Filament\Actions\BulkAction;
use App\Filament\Administration\Resources\InternshipAgreementResource\Pages;
use App\Filament\Core;
use App\Models\InternshipAgreement;
use Filament;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelPdf\Facades\Pdf;

use function Spatie\LaravelPdf\Support\pdf;

class InternshipAgreementResource extends Core\BaseResource
{
    protected static ?string $model = InternshipAgreement::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?int $sort = 1;

    protected static ?string $navigationBadgeTooltip = 'Announced internships';

    protected static ?string $navigationGroup = 'Students and projects';

    public static function canView(Model $record): bool
    {
        return true;
    }

    public static function getModelLabel(): string
    {
        return __('Internship Agreement');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Internship Agreements');
    }

    public static function getnavigationGroup(): string
    {
        return __(self::$navigationGroup);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'title',
            'organization_name',
            'student.first_name',
            'student.last_name',
            'id_pfe',
        ];
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
                    ->label(__('Internship Agreement Status'))
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
                    ->requiresConfirmation()
                    ->outlined(),

                // Tables\Actions\ActionGroup::make([
                //     ImportAction::make()
                //         ->importer(App\Filament\Imports\InternshipAgreementImporter::class)
                //         ->hidden(fn () => auth()->user()->isAdministrator() === false),
                // ]),
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
                // Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('status')
                    ->multiple()
                    ->options(Enums\Status::class),
                Tables\Filters\SelectFilter::make('assigned_department')
                    ->multiple()
                    ->options(Enums\Department::class),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('Pdf from view')
                        ->link()
                        ->action(
                            fn ($record) => pdf()
                                ->view('pdf.base', ['internship' => $record])
                                ->name('Internship Agreement.pdf')
                                ->save('pdf from view.pdf')
                        ),
                    Tables\Actions\Action::make('Pdf Html')
                        // ->color('danger')
                        ->label('')
                        ->iconButton()
                        ->action(
                            fn () => Pdf::html('<h1>Hello world!!</h1>')->save('pdf from html.pdf')
                        )
                        ->icon('heroicon-o-document-text')
                        ->tooltip(__('Download internship agreement as a PDF')),
                    Tables\Actions\Action::make('Pdf from agreement')
                        ->link()
                        ->action(
                            fn ($record) => pdf()
                                ->view('pdf.templates.table-grid', ['internship' => $record])
                                ->name('Internship Agreement.pdf')
                                ->save('pdf from agreement.pdf')
                        ),
                ])->hidden(fn () => env('APP_ENV') === 'production')
                    ->icon('heroicon-o-printer'),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make()->hidden(fn () => auth()->user()->isAdministrator() === false),
                    // ->disabled(! auth()->user()->can('delete', $this->post)),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),

                ])
                    ->hidden((auth()->user()->isAdministrator() || auth()->user()->isPowerProfessor()) === false)
                    ->label('')
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->size(Filament\Support\Enums\ActionSize::ExtraLarge)
                    ->tooltip(__('View, edit, or delete this internship agreement')),
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
                    ])
                        ->dropdown(false),
                ])
                    ->dropdownWidth(Filament\Support\Enums\MaxWidth::ExtraSmall)
                    ->label('')
                    ->icon('heroicon-o-squares-plus')
                    ->size(Filament\Support\Enums\ActionSize::ExtraLarge)
                    ->tooltip(__('Validate, sign, or assign department'))
                    ->hidden(fn () => (auth()->user()->isAdministrator() || auth()->user()->isPowerProfessor()) === false),

            ], position: Tables\Enums\ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ])->hidden(fn () => auth()->user()->isAdministrator() === false),
                BulkAction\Email\SendGenericEmail::make('Send Email'),
            ]);
        // ->dropdownWidth(Filament\Support\Enums\MaxWidth::ExtraSmall);

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
            'view' => Pages\ViewInternshipAgreement::route('/{record}/view'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('Internship agreement and validation process'))
                    ->headerActions([
                        Infolists\Components\Actions\Action::make('edit page', 'edit')
                            ->label('Edit')
                            ->icon('heroicon-o-pencil')
                            ->size(Filament\Support\Enums\ActionSize::ExtraLarge)
                            ->tooltip('Edit this internship agreement')
                            ->url(fn ($record) => \App\Filament\Administration\Resources\InternshipAgreementResource::getUrl('edit', [$record->id])),

                    ])
                    ->schema([

                        Fieldset::make('Internship agreement')
                            ->schema([
                                Infolists\Components\TextEntry::make('student.long_full_name')
                                    ->label('Student'),
                                Infolists\Components\TextEntry::make('title')
                                    ->label('Title'),
                                Infolists\Components\TextEntry::make('description')
                                    ->columnSpanFull(),
                                Infolists\Components\TextEntry::make('organization_name')
                                    ->label('Organization name'),
                                Infolists\Components\TextEntry::make('id_pfe')
                                    ->label('ID PFE'),
                                Infolists\Components\TextEntry::make('status')
                                    ->label('Status'),
                                Infolists\Components\TextEntry::make('assigned_department')
                                    ->label('Assigned department'),
                            ]),
                        Fieldset::make('Administative dates')
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
            ]);
    }
}
