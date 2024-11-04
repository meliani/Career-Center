<?php

namespace App\Filament\Administration\Resources;

use App\Enums\Department;
use App\Enums\Program;
use App\Enums\Role;
use App\Enums\Title;
use App\Filament\Actions\BulkAction;
use App\Filament\Administration\Resources\ProfessorResource\Pages;
use App\Filament\Core;
use App\Models\Professor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Support\Enums as FilamentEnums;
use Filament\Tables;
use Filament\Tables\Table;
use Hydrat\TableLayoutToggle\Facades\TableLayoutToggle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

use function Spatie\LaravelPdf\Support\pdf;

class ProfessorResource extends Core\BaseResource
{
    protected static ?string $model = Professor::class;

    protected static ?string $modelLabel = 'Professor';

    protected static ?string $pluralModelLabel = 'Professors';

    protected static ?string $title = 'Manage professors';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Administration';

    // protected static ?string $navigationParentItem = '';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $sort = 9;

    public static function canAccess(): bool
    {
        if (auth()->check()) {
            return auth()->user()->isSuperAdministrator() || auth()->user()->isAdministrator() || auth()->user()->isDirection() || auth()->user()->isProgramCoordinator() || auth()->user()->isDepartmentHead() || auth()->user()->isProfessor();
        } else {
            return false;
        }
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->numeric()
                    ->required()
                    ->default(
                        Professor::max('id') + 1
                    ),
                Forms\Components\Select::make('title')
                    ->options(Title::class)
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label(__('Username'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('first_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('department')
                    ->options(Department::class),
                Forms\Components\Select::make('role')
                    ->options(Role::class)
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('assigned_program')
                    ->label(__('Program Coordinator'))
                    ->options(Program::class),
                Forms\Components\Toggle::make('is_enabled')
                    ->label(__('Account enabled'))
                    ->required(),
                // Forms\Components\DateTimePicker::make('email_verified_at'),
                // Forms\Components\TextInput::make('password')
                //     ->password()
                //     ->required()
                //     ->maxLength(255),
                Forms\Components\Toggle::make('active_status')
                    ->required(),
                // Forms\Components\FileUpload::make('avatar')
                //     ->default('avatar.png'),
                // Forms\Components\Toggle::make('dark_mode')
                //     ->required(),
                // Forms\Components\TextInput::make('messenger_color')
                //     ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        $livewire = $table->getLivewire();

        return $table
            // ->defaultPaginationPageOption(20)
            ->groups([
                Tables\Grouping\Group::make('department')
                    ->label(__('Department'))
                    ->collapsible(),
            ])
            ->striped()
            ->columns(
                $livewire->isGridLayout()
                    ? \App\Services\Filament\Tables\Professors\ProfessorsGrid::get()
                    : \App\Services\Filament\Tables\Professors\ProfessorsTable::get()
            )
            ->contentGrid(
                fn () => $livewire->isGridLayout()
                    ? [
                        'md' => 3,
                        'lg' => 3,
                        'xl' => 3,
                        '2xl' => 4,
                    ] : null
            )
            ->filters([
                Tables\Filters\SelectFilter::make('has_projects')
                    ->options([
                        'yes' => 'Yes',
                        'no' => 'No',
                    ])
                    ->label(__('Participating in projects'))
                    ->placeholder(__('All'))
                    ->query(
                        fn (Builder $query, array $data) => $query->when(
                            $data['value'] === 'yes',
                            fn (Builder $query) => $query->whereHas('projects')
                        )->when(
                            $data['value'] === 'no',
                            fn (Builder $query) => $query->whereDoesntHave('projects')
                        ),
                    ),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('Projects Participation')
                    // ->link()
                        ->action(
                            fn ($record) => pdf()
                                ->view('pdf.templates.professor_projects_participation', ['professor' => $record])
                                ->name('Professor Participation.pdf')
                                ->save(
                                    // storage_path(
                                    'storage/pdf/' .
                                    Str::slug($record->name) . '-Projects-participation-' . time() . '.pdf'
                                    // )
                                )
                        ),
                ])->hidden(fn () => (env('APP_ENV') === 'production')),
            ], position: Tables\Enums\ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction\Email\SendProfessorsProjectsOverview::make('Send Professors Projects Overview')
                        ->label(__('Send Professors Projects Overview'))
                        ->hidden(fn () => auth()->user()->isAdministrator() === false)
                        ->outlined(),
                ])
                    ->label(__('Send email'))
                    ->dropdownWidth(FilamentEnums\MaxWidth::Small)
                    ->hidden(fn () => auth()->user()->isAdministrator() === false),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ])
                    ->label(__('actions'))
                    ->hidden(fn () => auth()->user()->isAdministrator() === false),

            ])
            ->defaultSort('projects_count', 'desc')
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
                    ->hidden(fn () => auth()->user()->isAdministrator() === false),
                TableLayoutToggle::getToggleViewTableAction(compact: true),

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
            'index' => Pages\ListProfessors::route('/'),
            'create' => Pages\CreateProfessor::route('/create'),
            'edit' => Pages\EditProfessor::route('/{record}/edit'),
        ];
    }
}
