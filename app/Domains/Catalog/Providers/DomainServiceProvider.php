<?php

namespace App\Domains\Catalog\Providers;

use App\Components\Queryable\Abstracts\FilterBuilder;
use App\Domains\Catalog\Console\Commands\UpdateProductCategoriesDisplayability;
use App\Domains\Catalog\Console\Commands\UpdateProductsDisplayability;
use App\Domains\Catalog\Services\Query\Filter\ProductFilterBuilder;
use App\Domains\Catalog\Services\Query\Filter\ProductFilterService;
use App\Domains\Generic\Enums\ServiceProviderNamespace;
use App\Infrastructure\Abstracts\Providers\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

final class DomainServiceProvider extends ServiceProvider
{
    public const NAMESPACE = ServiceProviderNamespace::CATALOG;

    protected array $providers = [
        RouteServiceProvider::class,
        EventServiceProvider::class,
    ];

    protected array $commands = [
        UpdateProductsDisplayability::class,
        UpdateProductCategoriesDisplayability::class,
    ];

    protected function afterRegistration(): void
    {
        $this->app
            ->when(ProductFilterService::class)
            ->needs(FilterBuilder::class)
            ->give(ProductFilterBuilder::class);
    }

    protected function registerSchedule(Schedule $schedule): void
    {
        $schedule->command(UpdateProductCategoriesDisplayability::class)->cron('* * * * *')->runInBackground()->environments(['local', 'staging']);
        $schedule->command(UpdateProductsDisplayability::class)->cron('* * * * *')->runInBackground()->environments(['local', 'staging']);
    }
}
