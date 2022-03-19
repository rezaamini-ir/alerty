<?php

namespace Alerty;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Events\QueryExecuted;
use Alerty\Services\QueryHandler;
use Alerty\Event\BadQueryExecuted;
use Alerty\Listeners\ShowAlertForBadQuery;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class AlertyServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->registerListeners();

        $this->loadMigrations();

        $this->loadViews();

        $this->registerRoutes();
    }

    private function registerListeners()
    {
        if ( ! $this->app->runningInConsole() && Schema::hasTable('query_entries') ) {
            Event::listen(QueryExecuted::class, [QueryHandler::class, 'handle']);
            Event::listen(BadQueryExecuted::class, [ShowAlertForBadQuery::class, 'handle']);
        }
    }

    private function loadMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    private function loadViews()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'alerty');
    }

    private function registerRoutes()
    {
        Route::middleware('web')->group(__DIR__.'/route.php');
    }

}
