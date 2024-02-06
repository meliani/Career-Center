<?php

namespace App\Providers;

use App\Doctrine\DBAL\Types\EnumType;
use App\Enums\Role;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use BezhanSalleh\PanelSwitch\PanelSwitch;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\Type;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\Entry;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Models\Project;
use App\Observers\ProjectObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(UrlGenerator $url): void
    {
        $this->turnOnSslIfProduction($url);
        $this->configureFilament();
        $this->addEnumTypeToDoctrine();
        // $this->configureDoctrine();
        $this->configureRateLimiter();
        $this->autoTranslateLabels();

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
            $column->translateLabel();
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
        // $this->translateLabels([
        //     Field::class,
        //     BaseFilter::class,
        //     Placeholder::class,
        //     Column::class,
        //     // or even `BaseAction::class`,
        // ]);
    }

    private function translateLabels(array $components = [])
    {
        foreach ($components as $component) {
            $component::configureUsing(function ($c): void {
                $c->translateLabel();
            });
        }
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
                ->locales(['ar', 'en', 'fr']); // also accepts a closure
        });
        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
            $panelSwitch
                ->visible(fn (): bool => auth()->user()?->hasAnyRole([
                    Role::SuperAdministrator,
                    Role::Administrator,
                ]));
        });
        TextColumn::configureUsing(function (TextColumn $column): void {
            $column
                ->toggleable()
                ->searchable()
                ->translateLabel();
        });
    }
}
