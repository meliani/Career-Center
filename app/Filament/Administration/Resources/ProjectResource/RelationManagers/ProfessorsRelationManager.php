<?php

namespace App\Filament\Administration\Resources\ProjectResource\RelationManagers;

use App\Enums;
use Filament\Forms;
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
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('long_full_name')
                    // ->size(Tables\Columns\TextColumn\TextColumnSize::ExtraSmall)
                    ->label('Full Name'),
                Tables\Columns\TextColumn::make('jury_role')
                    ->label('Jury role')
                    ->badge(),
                Tables\Columns\TextColumn::make('pivot.CreatedBy.full_name')
                    // ->badge()
                    ->label('Assigned by'),
                Tables\Columns\TextColumn::make('role')
                    ->label('Professor role in school')
                    // ->size(Tables\Columns\TextColumn\TextColumnSize::ExtraSmall)
                    ->hidden(fn () => (auth()->user()->isAdministrator() || auth()->user()->isDirection()) === false),

                Tables\Columns\TextColumn::make('pivot.ApprovedBy.full_name')
                    ->label('Approval done by')
                    // ->badge()
                    ->hidden(fn () => (auth()->user()->isAdministrator() || auth()->user()->isDirection()) === false),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),

                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->label(__('Add Jury Member'))
                    ->color('primary')
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\Select::make('jury_role')->options(Enums\JuryRole::class)
                            ->required()
                            ->default(Enums\JuryRole::Reviewer),
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label(__('Approve'))
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->pivot->update(['approved_by' => auth()->user()->id]))
                    ->visible(fn () => auth()->user()->isAdministrator() || auth()->user()->isDirection()),
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make()
                    ->disabled(fn () => auth()->user()->isAdministrator() === false),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()
                        ->hidden(fn () => auth()->user()->isAdministrator() === false),

                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
