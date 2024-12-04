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

    protected static ?string $title = 'Internship agreements';

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
            ->columns([
                Tables\Columns\TextColumn::make('id_pfe'),
                Tables\Columns\TextColumn::make('agreeable.student.long_full_name'),
                Tables\Columns\TextColumn::make('agreeable.title'),
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
