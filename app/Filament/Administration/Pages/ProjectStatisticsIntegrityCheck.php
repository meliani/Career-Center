<?php

namespace App\Filament\Administration\Pages;

use App\Enums\Department;
use App\Models\Year;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class ProjectStatisticsIntegrityCheck extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static string $view = 'filament.pages.project-statistics-integrity-check';    protected static ?string $title = null;

    protected static ?string $navigationLabel = null;

    protected static ?string $navigationGroup = null;

    public static function getNavigationLabel(): string
    {
        return __('Statistics Integrity');
    }

    public function getTitle(): string  
    {
        return __('Project Statistics Integrity Check');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('System Administration');
    }

    protected static ?int $navigationSort = 10;

    public ?array $data = [];

    public ?array $checkResults = null;

    public ?string $lastCheckTime = null;

    public bool $isRunning = false;

    public function mount(): void
    {
        $this->form->fill([
            'year_id' => Year::current()->id,
            'department' => null,
            'detailed' => true,
            'fix_orphans' => false,
            'export_format' => null,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([                Select::make('year_id')
                    ->label(__('Academic Year'))
                    ->options(Year::all()->pluck('title', 'id'))
                    ->default(Year::current()->id)
                    ->required(),

                Select::make('department')
                    ->label(__('Department Filter'))
                    ->placeholder(__('All Departments'))
                    ->options(collect(Department::cases())->mapWithKeys(function ($department) {
                        return [$department->value => $department->getLabel()];
                    }))
                    ->nullable(),

                Toggle::make('detailed')
                    ->label(__('Show Detailed Breakdown'))
                    ->default(true)
                    ->helperText(__('Display detailed information for each department')),

                Toggle::make('fix_orphans')
                    ->label(__('Fix Orphaned Relationships'))
                    ->default(false)
                    ->helperText(__('Attempt to automatically fix orphaned database relationships'))
                    ->columnSpanFull(),                Select::make('export_format')
                    ->label(__('Export Results'))
                    ->placeholder(__('No Export'))
                    ->options([
                        'json' => __('JSON Format'),
                        'csv' => __('CSV Format'),
                    ])
                    ->nullable()
                    ->helperText(__('Export the integrity check results to a file')),
            ])
            ->statePath('data')
            ->columns(2);
    }

    protected function getFormActions(): array
    {
        return [            Action::make('runCheck')
                ->label(__('Run Integrity Check'))
                ->icon('heroicon-o-play')
                ->color('primary')
                ->disabled(fn () => $this->isRunning)
                ->action('runIntegrityCheck'),

            Action::make('clearResults')
                ->label(__('Clear Results'))
                ->icon('heroicon-o-trash')
                ->color('gray')
                ->action('clearResults')
                ->visible(fn () => $this->checkResults !== null),
        ];
    }

    public function runIntegrityCheck(): void
    {
        $this->isRunning = true;
        
        try {
            $this->validate();
            
            $command = 'check:project-statistics-integrity';
            $options = [];

            // Add year option if different from current
            if ($this->data['year_id'] && $this->data['year_id'] != Year::current()->id) {
                $options['--year'] = $this->data['year_id'];
            }

            // Add department filter
            if ($this->data['department']) {
                $options['--department'] = $this->data['department'];
            }

            // Add detailed flag
            if ($this->data['detailed']) {
                $options['--detailed'] = true;
            }

            // Add fix orphans flag
            if ($this->data['fix_orphans']) {
                $options['--fix-orphans'] = true;
            }

            // Add export format
            if ($this->data['export_format']) {
                $options['--export'] = $this->data['export_format'];
            }

            // Capture the command output
            $exitCode = Artisan::call($command, $options);
            $output = Artisan::output();

            $this->checkResults = [
                'exit_code' => $exitCode,
                'output' => $output,
                'success' => $exitCode === 0,
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'options_used' => $options,
            ];

            $this->lastCheckTime = now()->format('Y-m-d H:i:s');            if ($exitCode === 0) {
                Notification::make()
                    ->title(__('Integrity Check Completed'))
                    ->body(__('Integrity check completed successfully!'))
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title(__('Integrity Issues Found'))
                    ->body(__('Integrity check completed with warnings.'))
                    ->warning()
                    ->send();
            }        } catch (\Exception $e) {
            $this->checkResults = [
                'exit_code' => 1,
                'output' => 'Error running integrity check: ' . $e->getMessage(),
                'success' => false,
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'error' => $e->getMessage(),
            ];

            Notification::make()
                ->title(__('Error Running Integrity Check'))
                ->body(__('Integrity check failed. Please review the output.'))
                ->danger()
                ->send();
        } finally {
            $this->isRunning = false;
        }
    }

    public function clearResults(): void
    {
        $this->checkResults = null;
        $this->lastCheckTime = null;

        Notification::make()
            ->title(__('Results Cleared'))            ->body(__('Results cleared successfully.'))
            ->success()
            ->send();
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->isAdministrator();
    }

    protected function getViewData(): array
    {
        return [
            'checkResults' => $this->checkResults,
            'lastCheckTime' => $this->lastCheckTime,
            'isRunning' => $this->isRunning,
        ];
    }
}
