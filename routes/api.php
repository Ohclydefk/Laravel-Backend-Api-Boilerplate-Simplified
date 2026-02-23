<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| API Routes (Auto-Registered Resources)
|--------------------------------------------------------------------------
|
| This file automatically registers API resources by scanning:
|   app/Http/Controllers/Api
|
| Any file ending with *Controller.php will automatically become
| a RESTful API resource.
|
| Example:
|   UserController.php     → /users
|   ProductController.php  → /products
|
| You still have full control:
| - You can exclude controllers
| - You can override resource names
| - You can override route names
| - You can add custom endpoints per resource
|
*/

Route::prefix('v1')
    ->name('api.v1.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Controllers to Ignore
        |--------------------------------------------------------------------------
        |
        | Add controllers here if you DO NOT want them to be automatically
        | registered as REST resources.
        |
        | Useful for:
        | - BaseController
        | - AuthController
        | - Any controller that uses only custom routes
        |
        */
        $excludeControllers = [
            'BaseController.php',
        ];

        /*
        |--------------------------------------------------------------------------
        | Resource Name Overrides
        |--------------------------------------------------------------------------
        |
        | By default, the resource name is generated automatically.
        |
        | Example:
        |   UserController → users
        |
        | If you want a different URL, override it here.
        |
        */
        $resourceOverrides = [
            // 'PeopleController.php' => 'users',
        ];

        /*
        |--------------------------------------------------------------------------
        | Route Name Overrides
        |--------------------------------------------------------------------------
        |
        | Default route names are:
        |   {resource}.list
        |   {resource}.create
        |   {resource}.view
        |   {resource}.update
        |   {resource}.delete
        |
        | You can override any of them per resource.
        |
        */
        $routeNameOverrides = [
            // 'users' => [
            //     'index' => 'users.custom-index',
            // ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Custom Endpoints Per Resource
        |--------------------------------------------------------------------------
        |
        | If a resource needs extra routes beyond apiResource(),
        | define them here.
        |
        | Each closure receives:
        |   $resource   → plural slug (e.g. "products")
        |   $controller → full controller class name
        |   $singular   → singular route parameter (e.g. "product")
        |
        */
        $customActions = [

            'users' => function ($resource, $controller, $singular) {

                Route::patch("$resource/{{$singular}}/toggle-active", [$controller, 'toggleActive'])
                    ->name("$resource.toggle-active");

                Route::patch("$resource/{{$singular}}/restore", [$controller, 'restore'])
                    ->name("$resource.restore");

                Route::delete("$resource/{{$singular}}/force-delete", [$controller, 'forceDelete'])
                    ->name("$resource.force-delete");
            },

            'products' => function ($resource, $controller, $singular) {

                Route::patch("$resource/{{$singular}}/toggle-active", [$controller, 'toggleActive'])
                    ->name("$resource.toggle-active");

                Route::patch("$resource/{{$singular}}/adjust-stock", [$controller, 'adjustStock'])
                    ->name("$resource.adjust-stock");

                Route::patch("$resource/{{$singular}}/restore", [$controller, 'restore'])
                    ->name("$resource.restore");

                Route::delete("$resource/{{$singular}}/force-delete", [$controller, 'forceDelete'])
                    ->name("$resource.force-delete");
            },
        ];

        /*
        |--------------------------------------------------------------------------
        | Scan API Controllers Folder
        |--------------------------------------------------------------------------
        |
        | We look inside:
        |   app/Http/Controllers/Api
        |
        | Every *Controller.php file found will be processed.
        |
        */
        $controllerFiles = File::files(app_path('Http/Controllers/Api'));

        foreach ($controllerFiles as $file) {

            $filename = $file->getFilename();

            // Skip excluded controllers
            if (in_array($filename, $excludeControllers, true)) {
                continue;
            }

            // Only process files ending with "Controller.php"
            if (!Str::endsWith($filename, 'Controller.php')) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Build Controller Class Name
            |--------------------------------------------------------------------------
            |
            | Example:
            |   UserController.php
            | becomes:
            |   App\Http\Controllers\Api\UserController
            |
            */
            $controllerClass = 'App\\Http\\Controllers\\Api\\' .
                Str::replaceLast('.php', '', $filename);

            if (!class_exists($controllerClass)) {
                continue; // Safety check
            }

            /*
            |--------------------------------------------------------------------------
            | Generate Resource Name
            |--------------------------------------------------------------------------
            |
            | UserController => users
            | ProductController => products
            |
            */
            $baseName = class_basename($controllerClass);
            $entity   = Str::replaceLast('Controller', '', $baseName);
            $defaultResource = Str::plural(Str::kebab($entity));

            // Apply override if defined
            $resource = $resourceOverrides[$filename]
                ?? $resourceOverrides[$baseName]
                ?? $defaultResource;

            $singularParam = Str::singular($resource);

            /*
            |--------------------------------------------------------------------------
            | Default Route Names
            |--------------------------------------------------------------------------
            */
            $defaultNames = [
                'index'   => "$resource.list",
                'store'   => "$resource.create",
                'show'    => "$resource.view",
                'update'  => "$resource.update",
                'destroy' => "$resource.delete",
            ];

            $names = array_merge($defaultNames, $routeNameOverrides[$resource] ?? []);

            /*
            |--------------------------------------------------------------------------
            | Register apiResource
            |--------------------------------------------------------------------------
            */
            Route::apiResource($resource, $controllerClass)
                ->parameter($resource, $singularParam)
                ->names($names);

            /*
            |--------------------------------------------------------------------------
            | Register Custom Routes (If Defined)
            |--------------------------------------------------------------------------
            */
            if (isset($customActions[$resource]) && is_callable($customActions[$resource])) {
                $customActions[$resource]($resource, $controllerClass, $singularParam);
            }
        }
    });
