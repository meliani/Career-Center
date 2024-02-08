<?php

namespace App\Filament\Administration\Resources;

use App\Enums\Status;
use App\Filament\Administration\Resources\InternshipAgreementResource\Pages;
use App\Filament\Imports\InternshipAgreementImporter;
use App\Models\InternshipAgreement;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class InternshipAgreementResource extends Resource
{
    protected static ?string $modelLabel = 'internship agreement';

    // protected static ?string $pluralModelLabel = 'internship agreements';
    // protected static ?string $navigationParentItem = '';

    protected static ?string $navigationGroup = 'Internships';

    protected static ?string $model = InternshipAgreement::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'title';

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'organization_name', 'student.full_name'];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
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

    public static function viewAny(): bool
    {
        return false;
    }
}
