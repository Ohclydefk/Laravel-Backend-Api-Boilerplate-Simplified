<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Providers\RepositoryServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     *
     * IMPORTANT:
     * ---------------------------------------------------------
     * Repository bindings are NO LONGER handled here.
     *
     * They are automatically registered inside:
     * App\Providers\RepositoryServiceProvider
     *
     * That provider:
     * - Scans app/Repositories/Contracts
     * - Finds all *RepositoryInterface.php files
     * - Automatically binds them to their matching
     *   Eloquent implementations
     *
     * Example convention:
     *
     * Interface:
     *   App\Repositories\Contracts\UserRepositoryInterface
     *
     * Implementation:
     *   App\Repositories\Eloquent\UserRepository
     *
     * Because of that automation, you DO NOT need to:
     * - Import repository classes here
     * - Manually call $this->app->bind()
     *
     * If you need manual binding (special cases),
     * you may still define them here.
     */
    public function register(): void
    {
        $this->app->register(RepositoryServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
