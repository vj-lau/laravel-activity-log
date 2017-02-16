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

/**
 * Http 请求唯一标识
 *
 * Class RequestId
 * @package VJLau\ActivityLog\Middleware
 */
class LogAfterRequest
{
    private $start;
    private $end;

    public function handle(Request $request, \Closure $next)
    {
        $this->start = microtime(true);

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
        $this->end = microtime(true);

        // TODO 保存记录
//        Log::info('app.requests', ['request' => $request->all(), 'response' => $response]);
        //        Log::info('ApiLog done===========================');
//        Log::info('Duration:  ' . number_format($this->end - $this->start, 3));
//        Log::info('URL: ' . $request->fullUrl());
//        Log::info('Method: ' . $request->getMethod());
//        Log::info('IP Address: ' . $request->getClientIp());
//        Log::info('Status Code: ' . $response->getStatusCode());
    }
}