<?php

namespace App\Filament\Administration\Resources;

use App\Enums;
use App\Filament\Administration\Resources\OrganizationResource\Pages;
use App\Filament\Core\BaseResource;
use App\Models\Organization;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
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
                        Forms\Components\ToggleButtons::make('status')
                            ->options(Enums\OrganizationStatus::class)
                            ->enum(Enums\OrganizationStatus::class)
                            ->nullable(),
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
                                Forms\Components\TextInput::make('name_' . app()->getLocale())
                                    ->label(__('Name'))
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
                    ->sortable()
                    ->color(fn ($record) => $record->status && $record->status->value === 'Published' ? 'success' : null)
                    ->weight(fn ($record) => $record->status && $record->status->value === 'Published' ? 'bold' : null),
                Tables\Columns\TextColumn::make('website')
                    ->url(fn ($record) => $record->website)
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                \Parfaitementweb\FilamentCountryField\Tables\Columns\CountryColumn::make('country')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($record) => $record->status ? $record->status->getColor() : null),
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
                Tables\Columns\TextColumn::make('total_contacts_count')
                    ->label('Contacts')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->searchable(false)
                    ->color('success'),
                Tables\Columns\TextColumn::make('total_agreements_count')
                    ->label('Agreements')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->searchable(false)
                    ->color('info'),
                Tables\Columns\TextColumn::make('total_related_count')
                    ->label('Total Related')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('warning')
                    ->searchable(false)
                    ->weight('bold'),
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
                                Forms\Components\Section::make('Field Selection')
                                    ->description('Choose which organization to take each field from')
                                    ->schema([
                                        Forms\Components\Select::make('fields.name')
                                            ->label('Organization Name')
                                            ->options(function () use ($organizations) {
                                                return $organizations->mapWithKeys(fn ($org) => [
                                                    $org->id => ($org->status && $org->status->value === 'Published')
                                                        ? "★ {$org->name} ({$org->id})"
                                                        : "{$org->name} ({$org->id})",
                                                ]);
                                            })
                                            ->required(),

                                        Forms\Components\Select::make('fields.website')
                                            ->label('Website')
                                            ->options(function () use ($organizations) {
                                                return $organizations->mapWithKeys(fn ($org) => [
                                                    $org->id => ($org->status && $org->status->value === 'Published') ? '★ ' . ($org->website ?: 'No website') . " ({$org->id})" : ($org->website ?: 'No website') . " ({$org->id})",
                                                ]);
                                            })
                                            ->live(),

                                        Forms\Components\TextInput::make('fields.website_override')
                                            ->label('Custom Website')
                                            ->visible(fn (Forms\Get $get) => filled($get('fields.website')))
                                            ->url()
                                            ->prefix('https://'),

                                        Forms\Components\Select::make('fields.address')
                                            ->label('Address')
                                            ->options(function () use ($organizations) {
                                                return $organizations->mapWithKeys(fn ($org) => [
                                                    $org->id => ($org->status && $org->status->value === 'Published')
                                                        ? "★ {$org->address}, {$org->city} ({$org->id})"
                                                        : "{$org->address}, {$org->city} ({$org->id})",
                                                ]);
                                            })
                                            ->live(),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('fields.address_override')
                                                    ->label('Custom Address'),
                                                Forms\Components\TextInput::make('fields.city_override')
                                                    ->label('Custom City'),
                                            ])
                                            ->visible(fn (Forms\Get $get) => filled($get('fields.address'))),

                                        // ...other field selections with similar pattern
                                    ])->columns(2),

                                Forms\Components\Section::make('Target Organization')
                                    ->schema([
                                        Forms\Components\Select::make('target_organization')
                                            ->label('Primary Organization')
                                            ->options(function () use ($organizations) {
                                                return $organizations->mapWithKeys(fn ($org) => [
                                                    $org->id => ($org->status && $org->status->value === 'Published')
                                                        ? "★ {$org->name} ({$org->id})"
                                                        : "{$org->name} ({$org->id})",
                                                ]);
                                            })
                                            ->required()
                                            ->live()
                                            ->helperText('This organization will be kept while others will be merged into it.'),
                                    ]),

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
                                                        'name' => $org->name ? $org->name : 'No name',
                                                        'website' => $org->website ? $org->website : 'No website',
                                                        'address' => ($org->address || $org->city)
                                                            ? trim("{$org->address}, {$org->city}", ', ')
                                                            : 'No address',
                                                        'country' => $org->country ? $org->country : 'No country',
                                                        'industry' => $org->industryInformation ? $org->industryInformation->name : 'No industry',
                                                        default => ''
                                                    };
                                                    $preview .= "\n- {$field}: {$value}";
                                                }

                                                return $preview;
                                            })
                                            ->live()
                                            ->afterStateUpdated(fn ($state) => $state),
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
                                foreach ($data['fields'] as $field => $value) {
                                    if (str_ends_with($field, '_override')) {
                                        continue; // Skip override fields, they're handled with their parent fields
                                    }

                                    $sourceOrg = Organization::find($value);
                                    match ($field) {
                                        'name' => $targetOrg->name = $sourceOrg->name,
                                        'website' => $targetOrg->website = $data['fields']['website_override'] ?: $sourceOrg->website,
                                        'address' => [
                                            $targetOrg->address = $data['fields']['address_override'] ?: $sourceOrg->address,
                                            $targetOrg->city = $data['fields']['city_override'] ?: $sourceOrg->city,
                                            $targetOrg->setCountryAttribute($sourceOrg->country), // Updated to use setter
                                        ],
                                        'country' => $targetOrg->setCountryAttribute($sourceOrg->country), // Updated to use setter
                                        'industry' => $targetOrg->industry_information_id = $sourceOrg->industry_information_id,
                                        default => null
                                    };
                                }

                                // Set status to Published
                                $targetOrg->status = Enums\OrganizationStatus::Published;
                                $targetOrg->save();

                                // Update related records
                                foreach ($orgsToMerge as $org) {
                                    $org->internshipAgreementContacts()
                                        ->update(['organization_id' => $targetOrg->id]);
                                    $org->internshipAgreements()
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

    public static function infolist(Infolists\Infolist $infolist): Infolists\Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Basic Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('name'),
                        Infolists\Components\TextEntry::make('slug'),
                        Infolists\Components\TextEntry::make('website')
                            ->url(fn ($record) => $record->website_url)
                            ->openUrlInNewTab(),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn ($record) => $record->status?->getColor()),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Location')
                    ->schema([
                        Infolists\Components\TextEntry::make('address'),
                        Infolists\Components\TextEntry::make('city'),
                        Infolists\Components\TextEntry::make('country'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Relationships')
                    ->schema([
                        Infolists\Components\TextEntry::make('parentOrganization.name')
                            ->label('Parent Organization'),
                        Infolists\Components\TextEntry::make('industryInformation.name')
                            ->label('Industry'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Related Records Overview')
                    ->schema([
                        Infolists\Components\TextEntry::make('total_contacts_count')
                            ->label('Contacts')
                            ->badge()
                            ->color('success'),
                        Infolists\Components\TextEntry::make('internshipAgreements_count')
                            ->label('Internship Agreements')
                            ->badge()
                            ->color('info'),
                        Infolists\Components\TextEntry::make('finalYearInternshipAgreements_count')
                            ->label('Final Year Agreements')
                            ->badge()
                            ->color('info'),
                        Infolists\Components\TextEntry::make('apprenticeship_agreements_count') // Changed from apprenticeshipAgreements_count
                            ->label('Apprenticeship Agreements')
                            ->badge()
                            ->color('info'),
                        Infolists\Components\TextEntry::make('total_related_count')
                            ->label('Total Related Records')
                            ->weight('bold')
                            ->badge()
                            ->color('warning'),
                    ])
                    ->columns(5),
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
            ])
            ->withCount([
                'internshipAgreements as internshipAgreements_count', // Add explicit count name
                'finalYearInternshipAgreements as finalYearInternshipAgreements_count', // Add explicit count name
                'apprenticeshipAgreements as apprenticeship_agreements_count', // Add explicit count name
                'internshipAgreementContacts as total_contacts_count',
            ]);
    }
}
