<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\InternshipApplicationResource\Pages;
use App\Filament\Core\StudentBaseResource;
use App\Models\InternshipApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InternshipApplicationResource extends StudentBaseResource
{
    protected static ?string $model = InternshipApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $modelLabel = 'My Internship Application';

    protected static ?string $pluralModelLabel = 'My Applications';

    protected static ?string $navigationGroup = 'Internship Offers';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('student_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('internship_offer_id')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['student', 'internshipOffer'])->where('student_id', auth()->id());
    }

    public static function table(Table $table): Table
    {
        return $table
            // ->heading(__('My Internship Applications'))
            // ->contentGrid(
            //     [
            //         'md' => 1,
            //         'lg' => 1,
            //         'xl' => 1,
            //         '2xl' => 1,
            //     ]
            // )
            ->columns([
                // Tables\Columns\TextColumn::make('student.full_name')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('internshipOffer.organization_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Applied at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
                // Tables\Actions\ViewAction::make(),
            ])
            ->emptyStateActions([
                Action::make('create')
                    ->label('Apply for an internship')
                    ->icon('heroicon-o-plus-circle')
                    ->url('/app/internship-offers'),
            ])
            ->emptyStateHeading('No internship applications')
            ->emptyStateDescription('You have not applied for any internships yet.')
            ->emptyStateIcon('heroicon-o-document-text');
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
            'index' => Pages\ListInternshipApplications::route('/'),
            // 'create' => Pages\CreateInternshipApplication::route('/create'),
            // 'edit' => Pages\EditInternshipApplication::route('/{record}/edit'),
        ];
    }
}
