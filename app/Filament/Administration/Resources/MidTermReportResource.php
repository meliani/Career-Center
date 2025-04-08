<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Administration\Resources\MidTermReportResource\Pages;
use App\Models\MidTermReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MidTermReportResource extends Resource
{
    protected static ?string $model = MidTermReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Student Management';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('Mid-Term Reports');
    }

    public static function getModelLabel(): string
    {
        return __('Mid-Term Report');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->relationship('student', 'name', fn (Builder $query) => $query->whereNotNull('name'))
                            ->searchable()
                            ->preload()
                            ->getOptionLabelUsing(fn ($value): string => \App\Models\Student::find($value)?->name ?? 'Student #' . $value)
                            ->disabled(),

                        Forms\Components\Select::make('project_id')
                            ->relationship('project', 'title', fn (Builder $query) => $query->whereNotNull('title'))
                            ->searchable()
                            ->preload()
                            ->getOptionLabelUsing(fn ($value): string => \App\Models\Project::find($value)?->title ?? 'Project #' . $value)
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('submitted_at')
                            ->disabled(),

                        Forms\Components\Toggle::make('is_read_by_supervisor')
                            ->label('Marked as read by supervisor')
                            ->helperText('Toggle to mark report as read/unread'),

                        Forms\Components\Textarea::make('content')
                            ->label('Report Content')
                            ->disabled()
                            ->columnSpanFull()
                            ->rows(10),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('project.title')
                    ->label('Project')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Submitted')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_read_by_supervisor')
                    ->label('Read')
                    ->boolean()
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('content')
                    ->label('Content Preview')
                    ->limit(30)
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('student')
                    ->relationship('student', 'name', fn (Builder $query) => $query->whereNotNull('name'))
                    ->searchable()
                    ->preload()
                    ->getOptionLabelUsing(fn ($value): string => \App\Models\Student::find($value)?->name ?? 'Student #' . $value),

                Tables\Filters\SelectFilter::make('project')
                    ->relationship('project', 'title', fn (Builder $query) => $query->whereNotNull('title'))
                    ->searchable()
                    ->preload()
                    ->getOptionLabelUsing(fn ($value): string => \App\Models\Project::find($value)?->title ?? 'Project #' . $value),

                Tables\Filters\Filter::make('submitted_at')
                    ->form([
                        Forms\Components\DatePicker::make('submitted_from')
                            ->placeholder(fn ($state): string => 'Dec 18, 2020'),
                        Forms\Components\DatePicker::make('submitted_until')
                            ->placeholder(fn ($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['submitted_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('submitted_at', '>=', $date),
                            )
                            ->when(
                                $data['submitted_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('submitted_at', '<=', $date),
                            );
                    }),

                Tables\Filters\SelectFilter::make('is_read_by_supervisor')
                    ->label('Reading Status')
                    ->options([
                        '1' => 'Read',
                        '0' => 'Unread',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('mark_as_read')
                    ->label('Mark as Read')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(fn (Model $record) => $record->update(['is_read_by_supervisor' => true]))
                    ->visible(fn (Model $record) => ! $record->is_read_by_supervisor),
                Tables\Actions\Action::make('mark_as_unread')
                    ->label('Mark as Unread')
                    ->icon('heroicon-o-x-mark')
                    ->color('gray')
                    ->action(fn (Model $record) => $record->update(['is_read_by_supervisor' => false]))
                    ->visible(fn (Model $record) => $record->is_read_by_supervisor),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('mark_as_read')
                    ->label('Mark as Read')
                    ->icon('heroicon-o-check')
                    ->action(fn (Builder $query) => $query->update(['is_read_by_supervisor' => true]))
                    ->deselectRecordsAfterCompletion(),
                Tables\Actions\BulkAction::make('mark_as_unread')
                    ->label('Mark as Unread')
                    ->icon('heroicon-o-x-mark')
                    ->action(fn (Builder $query) => $query->update(['is_read_by_supervisor' => false]))
                    ->deselectRecordsAfterCompletion(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMidTermReports::route('/'),
            'create' => Pages\CreateMidTermReport::route('/create'),
            'view' => Pages\ViewMidTermReport::route('/{record}'),
            'edit' => Pages\EditMidTermReport::route('/{record}/edit'),
        ];
    }
}
