<?php

namespace VJLau\ActivityLog;

use Illuminate\Support\ServiceProvider;

class ActivityLogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config
        $this->publishes([
            base_path('vendor/vjlau/laravel-activity-log/config/activity-log.php') => config_path('activity-log.php'),
        ], 'config');
    }
}
