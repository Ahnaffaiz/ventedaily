<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\View\Components\DataPagination;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        date_default_timezone_set('Asia/Jakarta');
        Carbon::setTestNow();
        View::share('subtitle', 'default');
        View::share('subRoute', 'dashboard');

        // Register custom components
        Blade::component('data-pagination', DataPagination::class);
    }
}
