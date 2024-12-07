<?php

namespace App\Filament\Administration\Resources;

use App\Filament\Administration\Resources\OrganizationResource\Pages;
use App\Filament\Core\BaseResource;
use App\Models\Organization;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Support\Enums\ActionSize;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class OrganizationResource extends BaseResource
{
    protected static ?string $model = Organization::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Organization';

    protected static ?string $pluralModelLabel = 'Organizations';

    protected static ?string $navigationGroup = 'Administration';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('website')
                            ->url()
                            ->prefix('https://')
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->enum(Enums\OrganizationStatus::class)
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Location')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('city')
                            ->maxLength(255),
                        \Parfaitementweb\FilamentCountryField\Forms\Components\Country::make('country')
                            ->searchable(),
                    ])->columns(2),

                Forms\Components\Section::make('Relationships')
                    ->schema([
                        Forms\Components\Select::make('parent_organization')
                            ->relationship('parentOrganization', 'name')
                            ->searchable(),
                        Forms\Components\Select::make('industry_information_id')
                            ->relationship('industryInformation', 'name')
                            ->searchable()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                            ]),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('website')
                    ->url(fn ($record) => $record->website_url)
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                \Parfaitementweb\FilamentCountryField\Tables\Columns\CountryColumn::make('country')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($record) => $record->status->getColor()),
                Tables\Columns\TextColumn::make('parentOrganization.name')
                    ->label('Parent Organization')
                    ->searchable(),
                Tables\Columns\TextColumn::make('industryInformation.name')
                    ->label('Industry')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\BulkAction::make('merge')
                        ->label('Merge Organizations')
                        ->icon('heroicon-o-arrow-path')
                        ->size(ActionSize::Large)
                        ->requiresConfirmation()
                        ->form(function (Tables\Actions\BulkAction $action): array {
                            return [
                                Forms\Components\Select::make('target_organization')
                                    ->label('Merge into Organization')
                                    ->options(
                                        fn () => Organization::query()
                                            ->whereIn('id', $action->getRecords()->pluck('id'))
                                            ->pluck('name', 'id')
                                            ->toArray()
                                    )
                                    ->required()
                                    ->helperText('All selected organizations will be merged into this one. This action cannot be undone.'),
                            ];
                        })
                        ->action(function ($records, array $data): void {
                            DB::beginTransaction();

                            try {
                                $targetOrg = Organization::find($data['target_organization']);
                                $orgsToMerge = Organization::query()
                                    ->whereIn('id', collect($records)->pluck('id'))
                                    ->where('id', '!=', $targetOrg->id)
                                    ->get();

                                foreach ($orgsToMerge as $org) {
                                    // Update all related models using relationships
                                    $org->internshipAgreementContacts()
                                        ->update(['organization_id' => $targetOrg->id]);

                                    $org->finalYearInternshipAgreements()
                                        ->update(['organization_id' => $targetOrg->id]);

                                    $org->apprenticeshipAgreements()
                                        ->update(['organization_id' => $targetOrg->id]);

                                    $org->projects()
                                        ->update(['organization_id' => $targetOrg->id]);

                                    $org->internshipAgreementContacts()
                                        ->update(['organization_id' => $targetOrg->id]);

                                    // Delete the merged organization
                                    $org->delete();
                                }

                                DB::commit();

                                Notification::make()
                                    ->success()
                                    ->title('Organizations Merged')
                                    ->body('Selected organizations and all related data have been merged successfully.')
                                    ->send();
                            } catch (\Exception $e) {
                                DB::rollBack();

                                Notification::make()
                                    ->danger()
                                    ->title('Merge Failed')
                                    ->body('An error occurred while merging organizations: ' . $e->getMessage())
                                    ->send();
                            }
                        }),
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
            'index' => Pages\ListOrganizations::route('/'),
            'create' => Pages\CreateOrganization::route('/create'),
            'view' => Pages\ViewOrganization::route('/{record}'),
            'edit' => Pages\EditOrganization::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
