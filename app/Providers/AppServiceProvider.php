<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
    public function boot(): void
    {
        DB::statement("SET time_zone = '+06:00'");
        if ($this->app->environment('production')){
            URL::forceScheme('https');
        }

        Authenticate::redirectUsing(static function (Request $request) {
            toast('Please login to continue!', 'warning');
            return route('login');
        });
    }
}
