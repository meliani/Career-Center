<?php

namespace App\Filament\Administration\Resources\ProjectResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class InternshipAgreementsRelationManager extends RelationManager
{
    protected static string $relationship = 'agreements';

    protected static ?string $inverseRelationship = 'project';

    protected static ?string $title = 'Students';

    protected static bool $isLazy = false;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __(self::$title);
    }

    public function table(Table $table): Table
    {

        return $table
            ->paginated(false)
            ->searchable(false)
            ->recordTitleAttribute('agreeable.title', 'agreeable.student.long_full_name')
            ->heading(false)
            ->recordTitleAttribute('title')
            ->paginated(false)
            ->searchable(false)
            ->emptyStateHeading('')
            ->emptyStateDescription('')
            ->emptyStateIcon('heroicon-o-users')
            ->columns([
                Tables\Columns\TextColumn::make('agreeable.student.id_pfe')
                    ->label(__('ID PFE')),
                Tables\Columns\TextColumn::make('agreeable.student.long_full_name')
                    ->label(__('Student name')),
                Tables\Columns\TextColumn::make('agreeable.student.email')
                    ->label(__('Student email')),
                Tables\Columns\TextColumn::make('agreeable.student.phone')
                    ->label(__('Student phone')),
                Tables\Columns\ImageColumn::make('agreeable.student.avatar_url')
                    ->label(__('Student photo'))
                    ->rounded(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AssociateAction::make()
                    // ->recordTitleAttribute('title', 'student.long_full_name')
                    ->recordTitle(fn ($record): string => "{$record->agreeable->title} ({$record->agreeable->student->long_full_name})")
                    // ->recordSelectSearchColumns(['student.name'])
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['agreeable.title', 'agreeable.student.long_full_name'])
                    ->recordSelect(
                        fn (Select $select) => $select->placeholder(__('Search by title or id_pfe...'))
                        // ->options(function (RelationManager $livewire): array {
                        //     return $livewire->getOwnerRecord()->students()
                        //         ->pluck('name')
                        //         ->toArray();
                        // }),
                    )
                    ->recordSelectOptionsQuery(
                        fn (Builder $query) => $query->where('agreeable_type', 'App\Models\FinalYearInternshipAgreement')
                    ),

            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
