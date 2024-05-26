<?php

namespace App\Providers;

use App\Models\TaskCheckpoint;
use Illuminate\Support\ServiceProvider;
use App\Observers\TaskCheckpointObserver;

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
        TaskCheckpoint::observe(TaskCheckpointObserver::class);
    }
}
