<?php

namespace App\Filament\App\Resources\ApprenticeshipResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\FontWeight;
use Filament;
class AmendmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'amendments';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $title = 'Amendments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Placeholder::make('status')
                    ->label(__('Status'))
                    ->content(fn ($record): string => ucfirst($record->status)),
                
                Forms\Components\Placeholder::make('title')
                    ->label(__('New Title'))
                    ->content(fn ($record): ?string => $record->title ?? 'No title change'),
                
                Forms\Components\Placeholder::make('description')
                    ->label(__('New Description'))
                    ->content(fn ($record): ?string => $record->description ?? 'No description change'),
                
                Forms\Components\Placeholder::make('internship_period')
                    ->label(__('New Period'))
                    ->content(fn ($record): ?string => $record->internship_period ?? 'No period change'),
                
                Forms\Components\Placeholder::make('reason')
                    ->label(__('Reason for Amendment'))
                    ->content(fn ($record): string => $record->reason),
                
                Forms\Components\Placeholder::make('created_at')
                    ->label(__('Requested On'))
                    ->content(fn ($record): string => $record->created_at->format('d/m/Y H:i')),
                
                Forms\Components\Placeholder::make('validation_comment')
                    ->label(__('Administrator Comments'))
                    ->content(fn ($record): ?string => $record->validation_comment ?? 'No comments yet')
                    ->visible(fn ($record): bool => $record->status !== 'pending'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->heading(__('Amendment Requests'))
            ->description(__('View the history of amendment requests for this apprenticeship'))
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'validated' => 'success',
                        'rejected' => 'danger',
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Requested On'))
                    ->dateTime('d/m/Y H:i'),
                
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Title'))
                    ->placeholder('No title change')
                    ->limit(30)
                    ->tooltip(function ($record) {
                        return $record->title ? $record->title : null;
                    }),
                
                Tables\Columns\TextColumn::make('internship_period')
                    ->label(__('Period'))
                    ->placeholder('No period change'),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Last Update'))
                    ->dateTime('d/m/Y H:i'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'validated' => 'Validated',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('add_amendment')
                    ->label(__('Request Amendment'))
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary')
                    ->url(fn () => \App\Filament\App\Resources\ApprenticeshipResource::getUrl('view', ['record' => $this->getOwnerRecord()]))
                    ->visible(fn () => !$this->getOwnerRecord()->hasPendingAmendmentRequests()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->emptyStateHeading('No amendments')
            ->emptyStateDescription('This apprenticeship has no amendment requests.')
            ->emptyStateIcon('heroicon-o-document-text')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('Request Amendment')
                    ->url(fn () => \App\Filament\App\Resources\ApprenticeshipResource::getUrl('view', ['record' => $this->getOwnerRecord()]))
                    ->icon('heroicon-o-pencil-square')
                    ->visible(fn () => !$this->getOwnerRecord()->hasPendingAmendmentRequests()),
            ]);
    }
}
