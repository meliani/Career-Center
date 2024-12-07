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
                        ->modalHeading('Merge Organizations')
                        ->modalDescription('Select which organization to keep as the primary and choose which fields to retain from each organization.')
                        ->form(function (Tables\Actions\BulkAction $action): array {
                            $organizations = Organization::query()
                                ->whereIn('id', $action->getRecords()->pluck('id'))
                                ->get();

                            return [
                                Forms\Components\Select::make('target_organization')
                                    ->label('Primary Organization')
                                    ->options($organizations->pluck('name', 'id'))
                                    ->required()
                                    ->live()
                                    ->helperText('This organization will be kept while others will be merged into it.'),

                                Forms\Components\Section::make('Field Selection')
                                    ->description('Choose which organization to take each field from')
                                    ->schema([
                                        Forms\Components\Select::make('fields.name')
                                            ->label('Organization Name')
                                            ->options(function (Forms\Get $get) use ($organizations) {
                                                $targetId = $get('target_organization');

                                                return $organizations->mapWithKeys(fn ($org) => [
                                                    $org->id => "{$org->name} ({$org->id})",
                                                ]);
                                            })
                                            ->required(),

                                        Forms\Components\Select::make('fields.website')
                                            ->label('Website')
                                            ->options(function (Forms\Get $get) use ($organizations) {
                                                return $organizations->mapWithKeys(fn ($org) => [
                                                    $org->id => $org->website
                                                        ? "{$org->website} ({$org->id})"
                                                        : "No website ({$org->id})",
                                                ]);
                                            }),

                                        Forms\Components\Select::make('fields.address')
                                            ->label('Address')
                                            ->options(function (Forms\Get $get) use ($organizations) {
                                                return $organizations->mapWithKeys(fn ($org) => [
                                                    $org->id => "{$org->address}, {$org->city} ({$org->id})",
                                                ]);
                                            }),

                                        Forms\Components\Select::make('fields.industry')
                                            ->label('Industry')
                                            ->options(function (Forms\Get $get) use ($organizations) {
                                                return $organizations->mapWithKeys(fn ($org) => [
                                                    $org->id => $org->industryInformation
                                                        ? "{$org->industryInformation->name} ({$org->id})"
                                                        : "No industry ({$org->id})",
                                                ]);
                                            }),

                                        Forms\Components\Select::make('fields.country')
                                            ->label('Country')
                                            ->options(function (Forms\Get $get) use ($organizations) {
                                                return $organizations->mapWithKeys(fn ($org) => [
                                                    $org->id => $org->country
                                                        ? "{$org->country} ({$org->id})"
                                                        : "No country ({$org->id})",
                                                ]);
                                            }),
                                    ])->columns(2),

                                Forms\Components\Section::make('Preview')
                                    ->description('Review the final organization details')
                                    ->schema([
                                        Forms\Components\Placeholder::make('preview')
                                            ->content(function (Forms\Get $get) use ($organizations) {
                                                $targetId = $get('target_organization');
                                                if (! $targetId) {
                                                    return 'Select a primary organization to see preview';
                                                }

                                                $fields = $get('fields');
                                                if (! $fields) {
                                                    return 'Select fields to see preview';
                                                }

                                                $preview = "Final Organization Details:\n";
                                                foreach ($fields as $field => $orgId) {
                                                    $org = $organizations->find($orgId);
                                                    if (! $org) {
                                                        continue;
                                                    }

                                                    $value = match ($field) {
                                                        'name' => $org->name ?? 'No name',
                                                        'website' => $org->website ?? 'No website',
                                                        'address' => ($org->address || $org->city)
                                                            ? trim("{$org->address}, {$org->city}", ', ')
                                                            : 'No address',
                                                        'country' => $org->country ?? 'No country',
                                                        'industry' => $org->industryInformation?->name ?? 'No industry',
                                                        default => ''
                                                    };
                                                    $preview .= "\n- {$field}: {$value}";
                                                }

                                                return $preview;
                                            })
                                            ->live() // Make preview reactive
                                            ->afterStateUpdated(fn ($state) => $state), // Force refresh on state change
                                    ]),
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

                                // Update target organization with selected fields
                                foreach ($data['fields'] as $field => $sourceOrgId) {
                                    $sourceOrg = Organization::find($sourceOrgId);
                                    match ($field) {
                                        'name' => $targetOrg->name = $sourceOrg->name,
                                        'website' => $targetOrg->website = $sourceOrg->website,
                                        'address' => [
                                            $targetOrg->address = $sourceOrg->address,
                                            $targetOrg->city = $sourceOrg->city,
                                            $targetOrg->country = $sourceOrg->country,
                                        ],
                                        'country' => $targetOrg->country = $sourceOrg->country,
                                        'industry' => $targetOrg->industry_information_id = $sourceOrg->industry_information_id,
                                        default => null
                                    };
                                }
                                $targetOrg->save();

                                // Update related records
                                foreach ($orgsToMerge as $org) {
                                    $org->internshipAgreementContacts()
                                        ->update(['organization_id' => $targetOrg->id]);
                                    $org->finalYearInternshipAgreements()
                                        ->update(['organization_id' => $targetOrg->id]);
                                    $org->apprenticeshipAgreements()
                                        ->update(['organization_id' => $targetOrg->id]);
                                    $org->projects()
                                        ->update(['organization_id' => $targetOrg->id]);
                                    $org->delete();
                                }

                                DB::commit();

                                Notification::make()
                                    ->success()
                                    ->title('Organizations Merged Successfully')
                                    ->body("All organizations have been merged into '{$targetOrg->name}'")
                                    ->actions([
                                        \Filament\Notifications\Actions\Action::make('view')
                                            ->label('View Organization')
                                            ->url(OrganizationResource::getUrl('view', ['record' => $targetOrg]))
                                            ->button(),
                                    ])
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
