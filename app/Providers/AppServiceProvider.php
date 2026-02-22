<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /**  Register the UserRepositoryInterface to UserRepository binding to avoid errors like the one i got:
         * Target [App\Repositories\Contracts\UserRepositoryInterface] is not instantiable while building 
         * [App\Http\Controllers\Api\UserController, App\Services\UserService].
         */
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
