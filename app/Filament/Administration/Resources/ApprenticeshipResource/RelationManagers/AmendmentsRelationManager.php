<?php

namespace App\Filament\Administration\Resources\ApprenticeshipResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\FontWeight;

class AmendmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'amendments';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $title = 'Amendment Requests';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Placeholder::make('status')
                    ->label(__('Status'))
                    ->content(fn ($record): string => ucfirst($record->status)),
                    
                Forms\Components\Placeholder::make('created_at')
                    ->label(__('Requested On'))
                    ->content(fn ($record): string => $record->created_at->format('d/m/Y H:i')),
                
                Forms\Components\Section::make(__('Requested Changes'))
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Placeholder::make('current_title')
                                    ->label(__('Current Title'))
                                    ->content(fn ($record): string => $record->apprenticeship->title)
                                    ->hidden(fn ($record): bool => !$record->title),
                                    
                                Forms\Components\Placeholder::make('new_title')
                                    ->label(__('New Title'))
                                    ->content(fn ($record): ?string => $record->title)
                                    ->hidden(fn ($record): bool => !$record->title),
                            ])
                            ->columns(2)
                            ->hidden(fn ($record): bool => !$record->title),
                            
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Placeholder::make('current_description')
                                    ->label(__('Current Description'))
                                    ->content(fn ($record): string => mb_substr(strip_tags($record->apprenticeship->description), 0, 100) . '...')
                                    ->hidden(fn ($record): bool => !$record->description),
                                    
                                Forms\Components\Placeholder::make('new_description')
                                    ->label(__('New Description'))
                                    ->content(fn ($record): ?string => mb_substr(strip_tags($record->description), 0, 100) . '...')
                                    ->hidden(fn ($record): bool => !$record->description),
                            ])
                            ->columns(2)
                            ->hidden(fn ($record): bool => !$record->description),
                            
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Placeholder::make('current_period')
                                    ->label(__('Current Period'))
                                    ->content(fn ($record): string => $record->apprenticeship->internship_period)
                                    ->hidden(fn ($record): bool => !$record->new_starting_at && !$record->new_ending_at),
                                    
                                Forms\Components\Placeholder::make('new_period')
                                    ->label(__('New Period'))
                                    ->content(fn ($record): ?string => $record->internship_period)
                                    ->hidden(fn ($record): bool => !$record->new_starting_at && !$record->new_ending_at),
                            ])
                            ->columns(2)
                            ->hidden(fn ($record): bool => !$record->new_starting_at && !$record->new_ending_at),
                    ]),
                
                Forms\Components\Section::make(__('Amendment Information'))
                    ->schema([
                        Forms\Components\Placeholder::make('reason')
                            ->label(__('Reason for Amendment'))
                            ->content(fn ($record): string => $record->reason),
                            
                        Forms\Components\Textarea::make('validation_comment')
                            ->label(__('Administrator Comment'))
                            ->placeholder(__('Add a comment to explain validation decision'))
                            ->rows(3)
                            ->columnSpanFull()
                            ->disabled(fn ($record): bool => $record->status !== 'pending'),
                    ]),
                    
                Forms\Components\Section::make(__('Validation Actions'))
                    ->schema([
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('validate')
                                ->label(__('Approve Amendment'))
                                ->color('success')
                                ->icon('heroicon-o-check-circle')
                                ->action(function ($record, array $data) {
                                    $record->status = 'validated';
                                    $record->validation_comment = $data['validation_comment'];
                                    $record->validated_at = now();
                                    $record->validated_by = auth()->id();
                                    $record->save();
                                    
                                    // Apply the changes to the apprenticeship
                                    $record->apprenticeship->applyAmendment($record);
                                    
                                    $this->refreshFormData([
                                        'validation_comment',
                                    ]);
                                    
                                    $this->notification()->success(
                                        __('Amendment approved'),
                                        __('The amendment has been approved and changes have been applied.')
                                    );
                                })
                                ->requiresConfirmation()
                                ->modalHeading(__('Approve Amendment'))
                                ->modalDescription(__('Are you sure you want to approve this amendment? The changes will be applied to the apprenticeship.'))
                                ->modalSubmitActionLabel(__('Yes, approve'))
                                ->visible(fn ($record): bool => $record->status === 'pending'),
                                
                            Forms\Components\Actions\Action::make('reject')
                                ->label(__('Reject Amendment'))
                                ->color('danger')
                                ->icon('heroicon-o-x-circle')
                                ->action(function ($record, array $data) {
                                    $record->status = 'rejected';
                                    $record->validation_comment = $data['validation_comment'];
                                    $record->rejected_at = now();
                                    $record->validated_by = auth()->id();
                                    $record->save();
                                    
                                    $this->refreshFormData([
                                        'validation_comment',
                                    ]);
                                    
                                    $this->notification()->success(
                                        __('Amendment rejected'),
                                        __('The amendment has been rejected.')
                                    );
                                })
                                ->requiresConfirmation()
                                ->modalHeading(__('Reject Amendment'))
                                ->modalDescription(__('Are you sure you want to reject this amendment?'))
                                ->modalSubmitActionLabel(__('Yes, reject'))
                                ->visible(fn ($record): bool => $record->status === 'pending'),
                        ])
                        ->fullWidth()
                        ->visible(fn ($record): bool => $record->status === 'pending'),
                    ])
                    ->visible(fn ($record): bool => $record->status === 'pending'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->heading(__('Amendment Requests'))
            ->description(__('Review and manage amendment requests for this apprenticeship'))
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('ID'))
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'validated' => 'success',
                        'rejected' => 'danger',
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Requested On'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Title Change'))
                    ->placeholder('-')
                    ->limit(30)
                    ->tooltip(function ($record) {
                        return $record->title ? $record->title : null;
                    }),
                
                Tables\Columns\TextColumn::make('internship_period')
                    ->label(__('Period Change'))
                    ->placeholder('-'),
                
                Tables\Columns\TextColumn::make('validator.name')
                    ->label(__('Validated By'))
                    ->placeholder('-'),
                    
                Tables\Columns\TextColumn::make('validated_at')
                    ->label(__('Validated On'))
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'validated' => 'Validated',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }
}
