<?php

namespace App\Filament\Administration\Resources\ProjectResource\RelationManagers;

use App\Enums;
use Filament;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ProfessorsRelationManager extends RelationManager
{
    protected static string $relationship = 'professors';

    protected static ?string $title = 'Jury';

    protected static bool $isLazy = false;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __(self::$title);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('jury_role')
                    ->required()
                    ->options(Enums\JuryRole::class)
                    ->default(Enums\JuryRole::Reviewer),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Jury Members'))
            ->recordTitleAttribute('name')
            ->paginated(false)
            ->searchable(false)
            ->columns([
                Tables\Columns\TextColumn::make('long_full_name')
                    // ->size(Tables\Columns\TextColumn\TextColumnSize::ExtraSmall)
                    ->label('Full Name'),
                Tables\Columns\TextColumn::make('jury_role')
                    ->label('Jury role')
                    ->badge(),
                Tables\Columns\CheckboxColumn::make('was_present')
                    ->label('Was present')
                    ->hidden(fn () => (auth()->user()->isAdministrator() || auth()->user()->isAdministrativeSupervisor()) === false),
                Tables\Columns\TextColumn::make('pivot.CreatedBy.full_name')
                    ->badge()
                    ->label('Assigned by'),
                Tables\Columns\TextColumn::make('role')
                    ->label('Professor role in school')
                    // ->size(Tables\Columns\TextColumn\TextColumnSize::ExtraSmall)
                    ->hidden(fn () => (auth()->user()->isAdministrator() || auth()->user()->isDirection()) === false),

                Tables\Columns\TextColumn::make('pivot.ApprovedBy.full_name')
                    ->label('Approval done by')
                    ->placeholder(__('Not approved yet.'))
                    // ->default(__('Not approved yet.'))
                    ->badge()
                    ->hidden(fn () => (auth()->user()->isAdministrator() || auth()->user()->isDirection()) === false),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),

                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['name', 'department'])
                    ->recordSelect(
                        fn (Select $select) => $select->placeholder(__('Search by name or department...')),
                    )
                    ->label(__('Add Jury Member'))
                    ->color('primary')
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\Select::make('jury_role')->options(Enums\JuryRole::class)
                            ->required()
                            ->default(Enums\JuryRole::Reviewer),
                    ])
                    ->hidden(fn () => auth()->user()->isAdministrator() === false),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label(__('Approve'))
                    ->tooltip(__('Approve this jury member (only for administrators and direction)'))
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->pivot->update(['approved_by' => auth()->user()->id]))
                    ->visible(fn () => auth()->user()->isAdministrator() || auth()->user()->isDirection()),
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make()
                    ->label('')
                    ->icon('heroicon-o-trash')
                    ->tooltip(__('Remove Jury Member'))
                    ->disabled(fn () => auth()->user()->isAdministrator() === false),
                // Tables\Actions\DeleteAction::make(),
            ], position: Tables\Enums\ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()
                        ->label(__('Remove Jury Members'))
                        ->hidden(fn () => auth()->user()->isAdministrator() === false),

                    // Tables\Actions\DeleteBulkAction::make(),
                ])
                    ->dropdownWidth(Filament\Support\Enums\MaxWidth::ExtraSmall),
            ]);
    }
}
