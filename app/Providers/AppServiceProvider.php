<?php

namespace App\Providers;

use App\Doctrine\DBAL\Types\EnumType;
use App\Enums\Role;
use BezhanSalleh\FilamentLanguageSwitch\Enums\Placement;
// use BezhanSalleh\PanelSwitch\PanelSwitch;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Carbon\Carbon;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\Type;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists;
use Filament\Infolists\Components\Entry;
use Filament\Navigation\NavigationGroup;
use Filament\Tables;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Spatie\LaravelPdf\Enums\Format;
use Spatie\LaravelPdf\Enums\Unit;
use Spatie\LaravelPdf\Facades\Pdf;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        \Spatie\Health\Facades\Health::checks([
            \Spatie\Health\Checks\Checks\UsedDiskSpaceCheck::new(),
            // \Spatie\Health\Checks\Checks\DatabaseCheck::new(),
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(UrlGenerator $url): void
    {

        // $this->turnOnSslIfProduction($url);
        $this->configureFilament();
        // $this->addEnumTypeToDoctrine();
        // $this->configureDoctrine();
        $this->configureRateLimiter();
        $this->autoTranslateLabels();
        $this->configurePdf();

        Carbon::setLocale(config('app.locale'));
        setlocale(LC_TIME, 'fr_FR.utf8', 'fr_FR.UTF-8', 'fr_FR', 'fr');

        require_once app_path('Helpers/SystemHelper.php');

    }

    private function configurePdf()
    {
        $top = 20;
        $right = 45;
        $bottom = 20;
        $left = 45;

        Pdf::default()
            ->format(Format::A4)
            ->margins($top, $right, $bottom, $left, Unit::Pixel);
        // ->headerView('pdf.layout.header')
        // ->footerView('pdf.layout.footer');
    }

    private function configureRateLimiter()
    {
        /* RateLimiter::for('filament', function ($request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        }); */

        /* Jobs / Queue configuration */
        RateLimiter::for('default', function (object $job) {
            return Limit::perMinute(1)->by($job->user->id);
            //     return $job->user->vipCustomer()
            //         ? Limit::none()
            //         : Limit::perMinute(3)->by($job->user->id);
        });
        /* end of Jobs/Queues Config  */
    }

    private function addEnumTypeToDoctrine()
    {
        Type::addType(EnumType::ENUM, EnumType::class);
    }

    private function configureDoctrine()
    {
        /* Add Enum support to DBAL */

        Type::addType('enum', StringType::class);

        // $platform = Schema::getConnection()->getDoctrineSchemaManager()->getDatabasePlatform();
        $platform = Schema::getConnection()->getDoctrineConnection()->getDatabasePlatform();
        // $connection = $this->app->make('db')->connection();
        // $platform = $connection->getDoctrineConnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');
    }

    private function autoTranslateLabels()
    {
        Column::configureUsing(function (Column $column): void {
            $column->translateLabel()
                ->toggleable()
                ->searchable()
                ->sortable()
                ->wrapHeader();
        });
        Filter::configureUsing(function (Filter $filter): void {
            $filter->translateLabel();
        });
        Field::configureUsing(function (Field $field): void {
            $field->translateLabel();
        });
        Entry::configureUsing(function (Entry $entry): void {
            $entry->translateLabel();
        });
        Placeholder::configureUsing(function (Placeholder $placeholder): void {
            $placeholder->translateLabel();
        });
        Section::configureUsing(function (Section $section): void {
            $section->translateLabel();
        });
        Field::configureUsing(function (Field $field): void {
            $field->translateLabel();
        });
        Fieldset::configureUsing(function (Fieldset $fieldset): void {
            $fieldset->translateLabel();
        });
        DateTimePicker::configureUsing(function (DateTimePicker $dateTimePicker): void {
            $dateTimePicker->translateLabel();
        });
        Textarea::configureUsing(function (Textarea $textarea): void {
            $textarea->translateLabel();
        });
        Select::configureUsing(function (Select $select): void {
            $select->translateLabel();
        });
        ToggleButtons::configureUsing(function (ToggleButtons $toggleButtons): void {
            $toggleButtons->translateLabel();
        });

        Section::configureUsing(function (Section $section): void {
            $section->translateLabel();
        });
        Infolists\Components\Fieldset::configureUsing(function (Infolists\Components\Fieldset $fieldset): void {
            $fieldset->translateLabel();
            Forms\Components\Select::configureUsing(function (Forms\Components\Select $select): void {
                $select->translateLabel();
            });
        });
        Tables\Actions\Action::configureUsing(function (Tables\Actions\Action $action): void {
            $action->translateLabel();
        });

        Tables\Actions\BulkAction::configureUsing(function (Tables\Actions\BulkAction $action): void {
            $action->translateLabel();
        });

        // NavigationGroup::configureUsing(function (NavigationGroup $group): void {
        //     $group->translateLabel();
        // });
        NavigationGroup::configureUsing(function (NavigationGroup $group): void {
            // $group->label(fn () => __($group->label));

        });
    }

    public function turnOnSslIfProduction(UrlGenerator $url): void
    {
        if (App::environment('production')) {
            $url->forceScheme('https');
        }
    }

    public function configureFilament(): void
    {
        Model::unguard();
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->visible(outsidePanels: true)
                ->locales(['ar', 'en', 'fr']) // also accepts a closure
                ->displayLocale('fr')
                ->outsidePanelRoutes([
                    'home',                ])
                ->outsidePanelPlacement(Placement::TopRight);
        });
        // PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
        //     $panelSwitch
        //         ->visible(fn (): bool => auth()->user()?->hasAnyRole([
        //             Role::SuperAdministrator,
        //             Role::Administrator,
        //         ]));
        // });
        // TextColumn::configureUsing(function (TextColumn $column): void {
        //     $column
        //         ->toggleable()
        //         ->searchable()
        //         ->translateLabel()
        //         ->sortable();
        // });

        Column::macro('sortableMany', function () {
            return $this->sortable(query: function (\Illuminate\Database\Eloquent\Builder $query, string $direction, $column): \Illuminate\Database\Eloquent\Builder {
                [$table, $field] = explode('.', $column->getName());

                return $query->withAggregate($table, $field)
                    ->orderBy(implode('_', [$table, $field]), $direction);
            });
        });
    }
}
