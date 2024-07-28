<?php

namespace App\Filament\Alumni\Resources;

use App\Filament\Alumni\Resources\JobOfferResource\Pages;
use App\Filament\Core\AlumniBaseResource;
use App\Models\JobOffer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;

class JobOfferResource extends AlumniBaseResource
{
    protected static ?string $model = JobOffer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('organization_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('organization_type')
                    ->required(),
                Forms\Components\TextInput::make('organization_id')
                    ->numeric(),
                Forms\Components\TextInput::make('country')
                    ->maxLength(255),
                Forms\Components\TextInput::make('responsible_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('responsible_occupation')
                    ->maxLength(255),
                Forms\Components\TextInput::make('responsible_phone')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('responsible_email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\Textarea::make('job_title')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('job_details')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('is_remote')
                    ->maxLength(255),
                Forms\Components\TextInput::make('job_location')
                    ->maxLength(255),
                Forms\Components\TextInput::make('keywords')
                    ->maxLength(255),
                Forms\Components\TextInput::make('attached_file')
                    ->maxLength(255),
                Forms\Components\Textarea::make('application_link')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('job_duration')
                    ->numeric(),
                Forms\Components\TextInput::make('remuneration')
                    ->maxLength(255),
                Forms\Components\TextInput::make('currency')
                    ->maxLength(255),
                Forms\Components\TextInput::make('workload')
                    ->numeric(),
                Forms\Components\TextInput::make('recruting_type'),
                Forms\Components\TextInput::make('application_email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\Toggle::make('applyable'),
                Forms\Components\DatePicker::make('expire_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->contentGrid(
                [
                    'md' => 1,
                    'lg' => 1,
                    'xl' => 1,
                    '2xl' => 1,
                ]
            )
            ->paginated(false)
            ->searchable(false)
            ->emptyStateHeading('')
            ->emptyStateDescription('')
            ->columns([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('organization_name')
                            ->description(__('Organization'), position: 'above')
                            ->weight(FontWeight::Bold)
                            ->toggleable(false)
                            ->sortable(false),
                        Tables\Columns\BadgeColumn::make('organization_type')
                            ->toggleable(false)
                            ->sortable(false),
                        Tables\Columns\TextColumn::make('organization_id')
                            ->toggleable(false)
                            ->sortable(false)
                            ->numeric(),
                        Tables\Columns\TextColumn::make('country')
                            ->toggleable(false)
                            ->sortable(false),
                    ]),
                ]),
                Tables\Columns\TextColumn::make('responsible_name')
                    ->toggleable(false)
                    ->sortable(false),
                Tables\Columns\TextColumn::make('responsible_occupation')
                    ->toggleable(false)
                    ->sortable(false),
                Tables\Columns\TextColumn::make('responsible_phone')
                    ->toggleable(false)
                    ->sortable(false),
                Tables\Columns\TextColumn::make('responsible_email')
                    ->toggleable(false)
                    ->sortable(false),
                Tables\Columns\TextColumn::make('is_remote')
                    ->toggleable(false)
                    ->sortable(false),
                Tables\Columns\TextColumn::make('job_location')
                    ->toggleable(false)
                    ->sortable(false),
                Tables\Columns\TextColumn::make('keywords')
                    ->toggleable(false)
                    ->sortable(false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('attached_file')
                    ->toggleable(false)
                    ->sortable(false),
                Tables\Columns\TextColumn::make('job_duration')
                    ->toggleable(false)
                    ->sortable(false)
                    ->numeric(),
                Tables\Columns\TextColumn::make('remuneration')
                    ->toggleable(false)
                    ->sortable(false),
                Tables\Columns\TextColumn::make('currency')
                    ->toggleable(false)
                    ->sortable(false),
                Tables\Columns\TextColumn::make('workload')
                    ->toggleable(false)
                    ->sortable(false)
                    ->numeric(),
                Tables\Columns\TextColumn::make('recruting_type')
                    ->toggleable(false)
                    ->sortable(false),
                Tables\Columns\TextColumn::make('application_email')
                    ->toggleable(false)
                    ->sortable(false),
                Tables\Columns\TextColumn::make('expire_at')
                    ->date()
                    ->toggleable(false)
                    ->sortable(false),
                // Tables\Columns\TextColumn::make('status')
                //     ->toggleable(false)
                //     ->sortable(false),
                // Tables\Columns\IconColumn::make('applyable')
                //     ->toggleable(false)
                //     ->sortable(false)
                //     ->boolean(),

            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->headerActions([
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
            'index' => Pages\ListJobOffers::route('/'),
            // 'create' => Pages\CreateJobOffer::route('/create'),
            'view' => Pages\ViewJobOffer::route('/{record}'),
            // 'edit' => Pages\EditJobOffer::route('/{record}/edit'),
        ];
    }
}
