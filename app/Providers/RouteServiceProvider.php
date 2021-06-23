<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    protected $namespace = 'App\\Http\\Controllers';

    /**
     * Route Path prefix - base path where the custom routes reside
     *
     * @var string
     */
    protected $customRoutePathPrefix = "routes/web/";

    /**
     * Custom Routes that will be mapped by this provider.
     * 
     * Each group has its own prefix. This will look a bit ugly when scaled...
     *
     * @var array
     */
    protected $customRoutePaths = [
        "prefixes",
        "groups",
    ];

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        // Build custom web routes that we will register in bulk
        $this->buildCustomRoutes();

        // --------------------------------------------------------------
        // There is a really nice thing that can map route parameters
        // This can force the route Parameters to match defined patterns
        // Route::pattern('thread_id', '[0-9]+');
        // --------------------------------------------------------------

        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            // The reason the the below namespace is that I keep all Web Routes mapped separately,
            // so they reside in different directory, organized
            // This is just the "landing" route, which will obviously not be called web.php
            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web/main.php'));

            // Custom Route Mapping
            for ($i = 0; $i < count($this->customRoutePaths["prefixes"]); $i++) {
                Route::middleware(['web' /* <include_your_middlewares_here> */])
                    ->namespace($this->namespace)
                    ->prefix($this->customRoutePaths["prefixes"][$i])
                    ->group(base_path($this->customRoutePaths["groups"][$i]));
            }
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }

    protected function buildCustomRoutes()
    {
        // NOTICE: the order of PREFIX - GROUP has to match
        // 'groups' represent file with Groups of routes - FileNames for short
        $routes = [
            "prefixes" => [
                "/"
            ],
            "groups" => [
                "main.php"
            ]
        ];

        // This is faster when cached
        $routesCount = count($routes) - 1;

        // Add routes in bulk from the array above
        for ($i = 0; $i < $routesCount; $i++) {
            $this->customRoutePaths["prefixes"][$i] = $routes["prefixes"][$i];
            $this->customRoutePaths["groups"][$i] = $this->customRoutePathPrefix . $routes["groups"][$i];
        }
    }
}
