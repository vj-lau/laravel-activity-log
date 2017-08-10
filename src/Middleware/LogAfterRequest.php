<?php
/**
 * Created by PhpStorm.
 * User: VJLau
 * Date: 2017/2/16
 * Time: 下午8:25
 */

namespace Bidzm\ActivityLog\Middleware;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Bidzm\ActivityLog\Models\RequestLog;

/**
 * Http 请求唯一标识
 *
 * Class RequestId
 * @package Bidzm\ActivityLog\Middleware
 */
class LogAfterRequest
{
    public function handle(Request $request, \Closure $next)
    {
        $start = microtime(true);
        $request->headers->set('X-Request-Start-Time', $start);

        $uuid = $request->headers->get('X-Request-ID');
        if (is_null($uuid)) {
            $uuid = Uuid::uuid4()->toString();
            $request->headers->set('X-Request-ID', $uuid);
        }

        $response = $next($request);
        $response->headers->set('X-Request-ID', $uuid);
        return $response;
    }

    public function terminate($request, $response)
    {
        $start = $request->headers->get('X-Request-Start-Time');
        $end = microtime(true);

        $log = new RequestLog;
        $log->request_id = $request->headers->get('X-Request-ID');
        $log->user_id = auth()->user()->id;
        $log->user_name = auth()->user()->name;
        $log->user_truename = auth()->user()->truename;
        $log->params = $request->all();
        $log->fullUrl = $request->fullUrl();
        $log->duration = number_format(($end - $start) * 1000, 3); // milliseconds
        $log->method = $request->method();
        $log->ip = $request->ip();
        $log->status_code = $response->getStatusCode();
        $log->save();
    }
}