<?php

namespace App\Providers;

use App\Services\LoanService;
use Illuminate\Support\ServiceProvider;

class LoanServiceProvider extends ServiceProvider
{

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('loan', fn() => new LoanService());
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

}
