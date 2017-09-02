<?php

namespace Bidzm\ActivityLog;

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
            __DIR__.'/../config/activity-log.php' => config_path('activity-log.php'),
        ], 'config');

        if (! class_exists('CreateActivityLogsTable')) {
            $timestamp = date('Y_m_d_His', time());
            $this->publishes([
                __DIR__.'/../migrations/create_activity_logs_table.php.stub' => database_path("/migrations/{$timestamp}_create_activity_logs_table.php"),
            ], 'migrations');
        }
    }
}
