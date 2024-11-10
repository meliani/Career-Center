<?php

namespace App\Filament\Administration\Resources\InternshipOfferResource\RelationManagers;

use App\Filament\Administration\Resources\StudentResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class ApplicationsRelationManager extends RelationManager
{
    protected static string $relationship = 'applications';

    public static $primaryColumn = 'student.name';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        // return $form
        //     ->schema([
        //         // Forms\Components\TextInput::make('student.name')
        //         //     ->required()
        //         //     ->maxLength(255),
        //     ]);
        return StudentResource::form($form);

    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('student.name')
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Name'),
                Tables\Columns\TextColumn::make('student.level')
                    ->label('Level'),
                Tables\Columns\TextColumn::make('student.email')
                    ->label('Email'),
                Tables\Columns\TextColumn::make('student.phone')
                    ->label('Phone'),
                Tables\Columns\TextColumn::make('student.email_perso')
                    ->label('Email perso'),
                Tables\Columns\TextColumn::make('student.cv')
                    ->label('CV'),
                Tables\Columns\TextColumn::make('student.lm')
                    ->label('Cover letter'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('share')
                    ->label('Share Applications')
                    ->icon('heroicon-o-share')
                    ->action(function () {
                        $internship = $this->getOwnerRecord();
                        $expiresAt = $internship->expire_at
                            ? $internship->expire_at
                            : now()->addDays(30);

                        $url = URL::temporarySignedRoute(
                            'internship.applications.preview',
                            $expiresAt,
                            ['internship' => $internship->id]
                        );

                        return response()->json([
                            'url' => $url,
                        ]);
                    })
                    ->modalHeading('Share Applications')
                    ->modalDescription(function () {
                        $internship = $this->getOwnerRecord();
                        $expiresAt = $internship->expire_at
                            ? $internship->expire_at->format('d/m/Y')
                            : now()->addDays(30)->format('d/m/Y');

                        return __('This link will expire on :date', ['date' => $expiresAt]);
                    })
                    ->modalContent(function () {
                        $internship = $this->getOwnerRecord();
                        $expiresAt = $internship->expire_at
                            ? $internship->expire_at
                            : now()->addDays(30);

                        return view('filament.modals.share-link', [
                            'url' => URL::temporarySignedRoute(
                                'internship.applications.preview',
                                $expiresAt,
                                ['internship' => $internship->id]
                            ),
                        ]);
                    }),
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make(),

            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->label(false),
            ], position: \Filament\Tables\Enums\ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->recruiting_type === \App\Enums\RecruitingType::SchoolManaged;
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
