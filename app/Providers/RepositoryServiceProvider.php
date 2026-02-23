<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register repository bindings automatically.
     *
     * This service provider scans the Contracts directory for all
     * *RepositoryInterface.php files and automatically binds them
     * to their corresponding Eloquent implementations.
     *
     * Naming Convention Required:
     *
     * Interface:
     *   App\Repositories\Contracts\UserRepositoryInterface
     *
     * Implementation:
     *   App\Repositories\Eloquent\UserRepository
     *
     * The system removes "Interface" and swaps the namespace
     * from Contracts → Eloquent.
     */
    public function register(): void
    {
        /**
         * Absolute path to:
         * app/Repositories/Contracts
         *
         * app_path() ensures this works regardless of environment.
         */
        $contractsPath = app_path('Repositories/Contracts');

        /**
         * Scan the Contracts folder for all files ending with:
         * *RepositoryInterface.php
         *
         * Example matched file:
         * UserRepositoryInterface.php
         */
        foreach (glob($contractsPath . '/*RepositoryInterface.php') as $file) {

            /**
             * Build the full interface namespace.
             *
             * basename($file, '.php') extracts the filename
             * without extension.
             *
             * Example result:
             * App\Repositories\Contracts\UserRepositoryInterface
             */
            $interface = 'App\\Repositories\\Contracts\\' . basename($file, '.php');

            /**
             * Convert Interface namespace into Implementation namespace.
             *
             * Step 1:
             * Remove "Interface" suffix
             * UserRepositoryInterface → UserRepository
             */
            $implementation = Str::replaceLast('Interface', '', $interface);

            /**
             * Step 2:
             * Replace namespace from:
             * Contracts => Eloquent
             *
             * Final result:
             * App\Repositories\Eloquent\UserRepository
             */
            $implementation = Str::replaceFirst(
                'App\\Repositories\\Contracts\\',
                'App\\Repositories\\Eloquent\\',
                $implementation
            );

            /**
             * Safety Check:
             * Only bind if BOTH interface and implementation exist.
             *
             * Prevents application crash if a file is missing.
             */
            if (interface_exists($interface) && class_exists($implementation)) {

                /**
                 * Bind interface to implementation.
                 *
                 * Now Laravel can resolve:
                 * UserRepositoryInterface → UserRepository automatically.
                 */
                $this->app->bind($interface, $implementation);
            }
        }
    }
}