<?php
/**
 * Created by PhpStorm.
 * User: VJLau
 * Date: 2017/2/16
 * Time: 下午8:25
 */

namespace VJLau\ActivityLog\Middleware;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use VJLau\ActivityLog\Models\RequestLog;

/**
 * Http 请求唯一标识
 *
 * Class RequestId
 * @package VJLau\ActivityLog\Middleware
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
        $log->params = $request->all();
        $log->request_id = $request->headers->get('X-Request-ID');
        $log->duration = number_format(($end - $start) * 1000, 3);
        $log->fullUrl = $request->fullUrl();  // milliseconds
        $log->method = $request->method();
        $log->ip = $request->ip();
        $log->status_code = $response->getStatusCode();
        $log->save();
    }
}