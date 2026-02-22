<?php

namespace App\Providers;

use App\Models\Contest;
use App\Models\LPTOSApplication;
use App\Models\LSApplication;
use App\Models\LTOSApplication;
use App\Models\MostBeautifulVillage;
use App\Models\Municipality;
use App\Models\PPMIApplication;
use App\Models\Register;
use App\Models\Role;
use App\Models\SZPTOSApplication;
use App\Models\User;
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
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        Route::model('role', Role::class);
        Route::model('user', User::class);
        Route::model('contest', Contest::class);
        Route::model('register', Register::class);
        Route::model('ppmi', PPMIApplication::class);
        Route::model('lto', LTOSApplication::class);
        Route::model('lpto', LPTOSApplication::class);
        Route::model('szpto', SZPTOSApplication::class);
        Route::model('l', LSApplication::class);
        Route::model('most_beautiful_village', MostBeautifulVillage::class);
        Route::model('municipality', Municipality::class);

        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
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
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
