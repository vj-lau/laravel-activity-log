<?php
/**
 * Created by PhpStorm.
 * User: VJLau
 * Date: 2017/2/16
 * Time: 下午9:09
 */

namespace Bidzm\ActivityLog;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Log\Writer;
use Illuminate\Support\ServiceProvider;
use Bidzm\ActivityLog\Models\QueryLog;

/***
 * SQL日志
 *
 * Class QueryLogServiceProvider
 * @package Bidzm\ActivityLog\Middleware\RequestId
 */
class QueryLogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     * @param Dispatcher $events
     * @param Writer $log
     */
    public function boot(Dispatcher $events, Writer $log)
    {
        $this->setupListener($events, $log);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * setting up listener
     *
     * @param Dispatcher $events
     * @param Writer $log
     */
    private function setupListener(Dispatcher $events, Writer $log)
    {
        $events->listen(QueryExecuted::class, function (QueryExecuted $queryExecuted) use ($log) {
            $sql = $queryExecuted->sql;
            $bindings = $queryExecuted->bindings;
            $time = $queryExecuted->time;
            try {
                foreach ($bindings as $val) {
                    $sql = preg_replace('/\?/', "'{$val}'", $sql, 1);
                }

                $request = request();
                $log = new QueryLog;
                $log->request_id = $request->headers->get('X-Request-ID');
                $log->duration = $time; // milliseconds
                $log->sql = $sql;
                $log->save();
            } catch (\Exception $e) {
                //  be quiet on error
            }
        });
    }
}