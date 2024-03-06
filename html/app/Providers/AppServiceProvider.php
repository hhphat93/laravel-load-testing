<?php

namespace App\Providers;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        URL::forceRootUrl('http://localhost:9001');

        Paginator::useBootstrap();

        DB::listen(function (QueryExecuted $query) {
            // dd(
            //     $query->connection,
            //     $query->sql,
            //     $query->bindings,
            //     $query->time
            // );
        });
    }
}
