<?php
declare(strict_types=1);


namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\ReportsFromDatesInterface;
use App\Repositories\ReportsFromDatesRepository;
use App\Models\Queue;

class RepositoryProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register repository bindings

        # Bing ReportsFromDatesInterface resolver
        $this->app->bind(ReportsFromDatesRepository::class, function () {
            return new ReportsFromDatesRepository(new Queue());
        });
        $this->app->bind(ReportsFromDatesInterface::class, ReportsFromDatesRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
