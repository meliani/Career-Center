<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Administration\Resources\AlumniResource\Pages;
use App\Filament\Core\BaseResource;
use App\Models\Alumni;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AlumniResource extends BaseResource
{
    protected static ?string $model = Alumni::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Alumni';

    protected static ?string $pluralModelLabel = 'Alumni';

    protected static ?string $navigationGroup = 'Alumni';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title'),
                Forms\Components\TextInput::make('name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('first_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('email_verified_at'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_number')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('graduation_year_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('degree'),
                Forms\Components\TextInput::make('assigned_program'),
                Forms\Components\TextInput::make('is_enabled')
                    ->numeric(),
                Forms\Components\TextInput::make('is_mobility')
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('abroad_school')
                    ->maxLength(191),
                Forms\Components\TextInput::make('work_status'),
                Forms\Components\TextInput::make('resume_url')
                    ->maxLength(255),
                Forms\Components\TextInput::make('avatar_url')
                    ->maxLength(255),
                Forms\Components\TextInput::make('number_of_bounces')
                    ->numeric(),
                Forms\Components\TextInput::make('bounce_reason')
                    ->maxLength(255),
                Forms\Components\TextInput::make('is_account_disabled')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('graduationYear.title')
                    ->searchable(false),
                Tables\Columns\TextColumn::make('degree'),
                Tables\Columns\TextColumn::make('assigned_program'),
                Tables\Columns\TextColumn::make('is_enabled')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('is_mobility')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('abroad_school')
                    ->searchable(),
                Tables\Columns\TextColumn::make('work_status'),
                Tables\Columns\TextColumn::make('resume_url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('avatar_url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('number_of_bounces')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bounce_reason')
                    ->searchable(),
                Tables\Columns\TextColumn::make('is_account_disabled')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('graduation_year_id')
                    ->options(
                        // \App\Models\Year::pluck('title', 'id')->toArray()
                        Alumni::query()
                            ->select('graduation_year_id')
                            ->distinct()
                            ->pluck('graduation_year_id')
                            ->mapWithKeys(fn ($id) => [$id => $id])
                            ->toArray()
                    )
                    ->query(
                        fn (QueryBuilder $query, array $data) => $query->when(
                            $data['values'],
                            fn (QueryBuilder $query, $data): QueryBuilder => $query->whereIn('graduation_year_id', $data)
                        ),
                    )
                    ->multiple(),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                \STS\FilamentImpersonate\Tables\Actions\Impersonate::make()
                    ->hidden(fn ($record) => ! $record->canBeImpersonated())
                    ->guard('alumnis'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListAlumnis::route('/'),
            'create' => Pages\CreateAlumni::route('/create'),
            'view' => Pages\ViewAlumni::route('/{record}'),
            'edit' => Pages\EditAlumni::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): EloquentBuilder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
