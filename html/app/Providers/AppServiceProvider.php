<?php

namespace App\Providers;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
            $this->writeLogDebugQuery();
        }
    }

    /**
     * writeLogDebugQuery
     *
     * @return void
     */
    private function writeLogDebugQuery() {
        if (!config('app.debug_query')) {
            return;
        }
        
        DB::listen(function ($query) {
            $bindings = array_map(function ($binding) {
                if (is_string($binding)) {
                    return "'{$binding}'";
                }
                if (is_null($binding)) {
                    return 'NULL';
                }
                if (is_bool($binding)) {
                    return $binding ? 'true' : 'false'; 
                }
                return $binding; 
            }, $query->bindings);
        
            $formattedQuery = str_replace(['%', '?'], ['%%', '%s'], $query->sql);
            $finalQuery = vsprintf($formattedQuery, $bindings);
    
            Log::channel('debug_query')->debug($finalQuery . "\n" . print_r([
                'time' => "{$query->time} ms",
                'bindings' => $bindings
            ], true));
        });
    }
}
