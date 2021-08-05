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
     * NOTICE: THIS IS NOT A PATH - THIS IS USED AS ROUTE NAME!
     * 
     * 'index' is assumed by default, refer to defined Routes for the path
     * 
     * This route is used to redirect users after authentication.
     *
     * Modify this route if your application redirects authenticated users to
     * some kind of Dashboard/panel.
     */
    public const HOME = 'index';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     */
    protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
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

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web/main.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
