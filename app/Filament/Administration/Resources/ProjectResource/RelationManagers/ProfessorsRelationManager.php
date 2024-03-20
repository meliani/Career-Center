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
                Forms\Components\Select::make('role')
                    ->label('Professor role in school')
                    ->options(Enums\JuryRole::class),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('long_full_name')
                    ->label('Full Name'),
                Tables\Columns\SelectColumn::make('jury_role')
                    ->label('Jury role')
                    ->disabled(fn () => auth()->user()->isAdministrator() === false)
                    ->options(Enums\JuryRole::class),
                Tables\Columns\TextColumn::make('pivot.assigned_by.full_name')
                    ->label('Assigned by'),

                Tables\Columns\TextColumn::make('role')
                    ->label('Professor role in school')
                    ->hidden(fn () => (auth()->user()->isAdministrator() || auth()->user()->isDirection()) === false),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make()
                    ->fillForm([
                        'created_by' => auth()->user()->id,
                    ])
                    // ->fillForm([
                    //     'owner_record_id' => $this->ownerRecord->id,
                    //     'owner_record_field' => $this->ownerRecord->name
                    // ])
                    ->preloadRecordSelect()
                    ->label(__('Add Jury Member'))
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\Select::make('jury_role')->options(Enums\JuryRole::class),
                        // Forms\Components\Hidden::make('created_by')
                        //     ->default(auth()->user()->id),

                        // Forms\Components\TextInput::make('created_by')
                        //     ->default(auth()->user()->id),

                    ]),
            ])
            ->actions([
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
